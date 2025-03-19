<?php

namespace App\Service\DeliveryNote;

use Carbon\Carbon;
use App\Trait\ResponseApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\DeliveryNote\DnDetail;
use App\Models\DeliveryNote\DnHeader;
use App\Models\DeliveryNote\DnDetailOutstanding;
use App\Service\User\UserGetEmailInternalPurchasing;
use App\Mail\DnDetailAndOutstandingNotificationInternal;

class DeliveryNoteUpdateTransaction
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     */
    use ResponseApi;

    public function __construct(protected UserGetEmailInternalPurchasing $userGetEmailInternalPurchasing) {}

    public function updateQuantity(string $noDn, array $data)
    {
        $message = '';

        $update = DB::transaction(function () use($noDn, $data)
        {
            // Update confirmation to the latest date
            $updateConfirmationDate = $this->confirmUpdateAt($noDn);

            // Cheking the update confirmation date and then
            $result = ($updateConfirmationDate == 'first') ? $this->updateQtyFirst($data) : $this->updateQtyOutstanding($noDn, $data);

            return $result;
        });

        // Variable for get email purchasing & get data user
        $emailPurchasing = $this->userGetEmailInternalPurchasing->getEmailPurchasing();

        $emailData = collect([
            'supplier_code' => Auth::user()->bp_code,
            'supplier_name' => Auth::user()->partner->adr_line_1 ?? Auth::user()->partner->bp_name,
            'no_dn' => $noDn,
        ]);

        // Return response to user
        switch ($update) {
            case '1':
                // Sending Mail to internal (purchasing)
                try {
                    foreach ($emailPurchasing as $email) {
                        Mail::to($email)->send(new DnDetailAndOutstandingNotificationInternal($emailData));
                    }

                    $message = 'Quantity Confirm Successfully';
                } catch (\Throwable $th) {
                    Log::warning("Failed to send email to PT Sanoh Indonesia Internal. Please check the server configuration / ENV. Error: $th");
                }
            break;

            case '2':
                try {
                    // Sending Mail to internal (purchasing)
                    foreach ($emailPurchasing as $email) {
                        Mail::to($email)->send(new DnDetailAndOutstandingNotificationInternal($emailData));
                    }

                    $message = 'Quantity Confirm Outstanding Successfully';
                } catch (\Throwable $th) {
                    Log::warning("Failed to send email to PT Sanoh Indonesia Internal. Please check the server configuration / ENV. Error: $th");
                }
            break;
        }

        return $message;
    }

    /**
     * Check confirmation submit first or outstanding
     * @param string $noDn
     * @return mixed|string|\Illuminate\Http\JsonResponse
     */
    private function confirmUpdateAt(string $noDn)
    {
        $result = '';

        $dnData = DnHeader::where('no_dn', $noDn)->first();
        if (! $dnData) {
           return $this->returnResponseApi(false, "DN Header Not Found (e:1)", null, 404);

        }

        if ($dnData->confirm_update_at == null) {
            // First submit
            $time = Carbon::now()->format('Y-m-d H:i:s');

            $dnData->update(['confirm_update_at' => $time]);

            $result = 'first';
        }else {
            // Outstanding submit
            $result = 'outstanding';
        }

        return $result;
    }

    /**
     * Update qty dn detail first submit
     * @param array $data
     * @return int|mixed|\Illuminate\Http\JsonResponse
     */
    private function updateQtyFirst(array $data)
    {
        foreach ($data as $d) {
            $record = DnDetail::where('dn_detail_no', $d['dn_detail_no'])->first();
            if (!$record) {
                return $this->returnResponseApi(false, "DN Detail Not Found For: {$d['dn_detail_no']}", null, 404);
            }

            if ($d['qty_confirm'] <= $record->dn_qty) {
                $record->update([
                    'qty_confirm' => $d['qty_confirm'],
                ]);
            } else {
                throw $this->returnResponseApi(false, 'Quantity Confirm exceeds Quantity Requested', null, 422);
            }
        }

        return 1;
    }

    /**
     * Update qty dn detail outstanding submit
     * @param string $noDN
     * @param array $data
     * @return int|mixed|\Illuminate\Http\JsonResponse
     */
    private function updateQtyOutstanding(string $noDN, array $data)
    {
        foreach ($data as $d) {
            $record = DnDetail::where('dn_detail_no', $d['dn_detail_no'])->first();
            if (! $record) {
                return $this->returnResponseApi(false, "DN Detail Not Found For: {$d['dn_detail_no']}", null, 404);
            }

            // Check if qty_confirm exceeds dn_qty
            if (($d['qty_confirm'] + $record->qty_confirm) > $record->dn_qty) {
                return $this->returnResponseApi(false, 'Quantity Confirm exceeds Quantity Requested', null, 422);
            }

            // Calculate the wave number
            $lastOutstanding = DnDetailOutstanding::where('dn_detail_no', $d['dn_detail_no'])
                ->orderBy('wave', 'desc')
                ->first('wave');
            $wave = ($lastOutstanding ? $lastOutstanding->wave : 0) + 1;

            DnDetailOutstanding::create([
                'no_dn' => $noDN,
                'dn_detail_no' => $d['dn_detail_no'],
                'qty_outstanding' => $d['qty_confirm'],
                'add_outstanding_date' => Carbon::now()->format('Y-m-d'),
                'add_outstanding_time' => Carbon::now()->format('H:i:s'),
                'wave' => $wave,
            ]);

            // Sum Qty confirm and outstanding
            $getAllQtyOutstanding = DnDetailOutstanding::where('no_dn', $noDN)
                ->where('dn_detail_no', $d['dn_detail_no'])
                ->sum('qty_outstanding');
            $qtyConfirm = $record->qty_confirm;

            // Check if the total qty_confirm has reached dn_qty
            $totalSum = $getAllQtyOutstanding + $qtyConfirm;
            if ($totalSum > $record->dn_qty) {
                return $this->returnResponseApi(false, 'All quantities have been confirmed. No more outstanding transactions allowed.', null,  422);
            }
        }

        return 2;
    }
}
