<?php

namespace App\Service\DeliveryNote;

use App\Mail\DnDetailAndOutstandingNotificationInternal;
use App\Models\DeliveryNote\DN_Detail;
use App\Models\DeliveryNote\DN_Detail_Outstanding;
use App\Models\DeliveryNote\DN_Header;
use App\Service\User\UserGetEmailInternalPurchasing;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DeliveryNoteUpdateTransaction
{
    public function __construct(protected UserGetEmailInternalPurchasing $userGetEmailInternalPurchasing) {}

    public function updateQuantity($data)
    {

        $return = DB::transaction(function () use ($data) {
            // Update confirmation to the latest date
            $updateConfirmationDate = $this->confirmUpdateAt($data['no_dn']);

            // Cheking the update confirmation date and then
            if ($updateConfirmationDate == false) {
                $updateQuantityConfirm = $this->updateQuantityFirstConfirm($data);

                return ($updateQuantityConfirm == true) ? 1 : throw new \Exception('Error Processing Quantity Confirm', 500);

            } elseif ($updateConfirmationDate == true) {
                $updateQuantityOutstanding = $this->updateOutstanding($data);

                return ($updateQuantityOutstanding == true) ? 2 : throw new \Exception('Error Processing Outstanding', 500);
            } else {
                throw new \Exception('Error processing confirm update at', 500);
            }
        });

        // Variable for get email purchasing & get data user
        $emailPurchasing = $this->userGetEmailInternalPurchasing->getEmailPurchasing();

        $emailData = collect([
            'supplier_code' => Auth::user()->bp_code,
            'supplier_name' => Auth::user()->partner->adr_line_1 ?? Auth::user()->partner->bp_name,
            'no_dn' => $data['no_dn'],
        ]);

        // Return response to user
        switch ($return) {
            case '1':
                // Mail to internal
                try {
                    foreach ($emailPurchasing as $email) {
                        Mail::to($email)->send(new DnDetailAndOutstandingNotificationInternal($emailData));
                    }
                } catch (\Throwable $th) {
                    // Log report
                    Log::warning("Failed to send email to PT Sanoh Indonesia Internal. Please check the server configuration / ENV. Error: $th");

                    // Return response
                    return response()->json([
                        'status' => 'email error',
                        'message' => 'Quantity confirm process successfully, but notification email to PT Sanoh Indonesia error',
                    ], 200);
                }

                // Return response
                return response()->json([
                    'status' => true,
                    'message' => 'Quantity confirm process successfully',
                ], 200);

            case '2':
                try {
                    // Mail to internal
                    foreach ($emailPurchasing as $email) {
                        Mail::to($email)->send(new DnDetailAndOutstandingNotificationInternal($emailData));
                    }
                } catch (\Throwable $th) {
                    // Log report
                    Log::warning("Failed to send email to PT Sanoh Indonesia Internal. Please check the server configuration / ENV. Error: $th");

                    // Return response
                    return response()->json([
                        'status' => 'email error',
                        'message' => 'Quantity confirm process successfully, but notification email to PT Sanoh Indonesia error',
                    ], 200);
                }

                // Return response
                return response()->json([
                    'status' => true,
                    'message' => 'Add Outstanding Successfully',
                ], 200);

            default:
                throw new \Exception('Error Returning Response', 500);
        }
    }

    private function confirmUpdateAt($data)
    {
        // Update DN_Header with current timestamp
        $header = DN_Header::where('no_dn', $data)->first();

        if ($header->confirm_update_at == null) {
            $time = Carbon::now()->format('Y-m-d H:i:s'); // Correct datetime format
            $header->update([
                'confirm_update_at' => $time,
            ]);

            return false;
        }

        if ($header->confirm_update_at != null) {
            return true;
        }
    }

    private function updateQuantityFirstConfirm($data): bool
    {
        // Update DN_Detail records
        foreach ($data['updates'] as $d) {
            $record = DN_Detail::where('dn_detail_no', $d['dn_detail_no'])->first();

            if ($record) {
                if ($d['qty_confirm'] <= $record->dn_qty) {
                    $record->update([
                        'qty_confirm' => $d['qty_confirm'],
                    ]);
                } else {
                    throw new \Exception('Quantity Confirm exceeds Quantity Requested', 422);
                }
            } else {
                // Handle the case where the record is not found
                return response()->json(['error' => 'DN Detail Not Found For: '.$d['dn_detail_no']], 404);
            }
        }

        return true;
    }

    private function updateOutstanding($data): bool
    {
        foreach ($data['updates'] as $d) {
            $dnDetailRecord = DN_Detail::where('dn_detail_no', $d['dn_detail_no'])->first();

            if (! $dnDetailRecord) {
                throw new \Exception('DN Detail Not Found For: '.$d['dn_detail_no'], 404);
            }

            // Check if qty_confirm exceeds dn_qty
            if (($d['qty_confirm'] + $dnDetailRecord->qty_confirm) > $dnDetailRecord->dn_qty) {
                throw new \Exception('Quantity Confirm exceeds Quantity Requested', 422);
            }

            // Calculate the wave number
            $lastOutstanding = DN_Detail_Outstanding::select('wave')
                ->where('dn_detail_no', $d['dn_detail_no'])
                ->orderBy('wave', 'desc')
                ->first();

            $wave = ($lastOutstanding ? $lastOutstanding->wave : 0) + 1;

            // Create data
            DN_Detail_Outstanding::create([
                'no_dn' => $data['no_dn'],
                'dn_detail_no' => $d['dn_detail_no'],
                'qty_outstanding' => $d['qty_confirm'],
                'add_outstanding_date' => Carbon::now()->format('Y-m-d'),
                'add_outstanding_time' => Carbon::now()->format('H:i:s'),
                'wave' => $wave,
            ]);

            // increment qty_confirm data from table dn_detail
            // $dnDetailRecord->increment("qty_confirm", $d['qty_confirm']);

            // Sum Qty confirm and outstanding
            $getAllQtyOutstanding = DN_Detail_Outstanding::where('no_dn', $data['no_dn'])
                ->where('dn_detail_no', $d['dn_detail_no'])
                ->sum('qty_outstanding'); // get all the outstanding based on no_dn and dn_detail_no then sum all
            $qtyConfirm = $dnDetailRecord->qty_confirm; // get data qty_confirm from dn_detail

            $totalSum = $getAllQtyOutstanding + $qtyConfirm;

            // Check if the total qty_confirm has reached dn_qty
            if ($totalSum > $dnDetailRecord->dn_qty) {
                throw new \Exception('All quantities have been confirmed. No more outstanding transactions allowed.', 422);
            }
        }

        return true;
    }
}
