<?php

namespace App\Service\DeliveryNote;

use App\Models\DeliveryNote\DN_Detail;
use App\Models\DeliveryNote\DN_Detail_Outstanding;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\DeliveryNote\DN_Header;

class DeliveryNoteUpdateTransaction
{
    public function updateQuantity($data) {


        return DB::transaction(function () use($data) {
            // initialization
            $updateQuantityConfirm = false;
            $updateQuantityOutstanding = false;

            // Update confirmation to the latest date
            $updateConfirmationDate = $this->confirmUpdateAt($data['no_dn']);

            // Cheking the update confirmation date and then
            if ($updateConfirmationDate == false) {
                $updateQuantityConfirm = $this->updateQuantityConfirm($data);
            } elseif ($updateConfirmationDate == true) {
                $updateQuantityOutstanding = $this->updateOutstanding($data);
            } else {
                throw new \Exception("Error processing confirm update at", 500);
            }

            if ($updateQuantityConfirm == true) {
                return response()->json([
                    'status' => true,
                    'message' => 'Quantity confirm process successfully',
                ],200);
            } elseif($updateQuantityOutstanding == true){
                return response()->json([
                    'status' => true,
                    'message' => 'Add Outstanding Successfully',
                ],200);
            }
        });
    }

    private function confirmUpdateAt($data) {
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

    private function updateQuantityConfirm($data): bool {
        // Update DN_Detail records
        foreach ($data['updates'] as $d) {
            $record = DN_Detail::where('dn_detail_no', $d['dn_detail_no'])->first();

            if ($record) {
                if ($d['qty_confirm'] <= $record->dn_qty) {
                    $record->update([
                        'qty_confirm' => $d['qty_confirm'],
                    ]);
                } else {
                    throw new \Exception("Quantity Confirm exceeds Quantity Requested",  422);
                }
            } else {
                // Handle the case where the record is not found
                return response()->json(['error' => 'DN Detail Not Found For: ' . $d['dn_detail_no']], 404);
            }
        }
        return true;
    }

    private function updateOutstanding($data): bool {
        foreach ($data['updates'] as $d) {
            $dnDetailRecord = DN_Detail::where('dn_detail_no', $d['dn_detail_no'])->first();

            if (!$dnDetailRecord) {
                throw new \Exception("DN Detail Not Found For: " . $d['dn_detail_no'], 404);
            }

            // Check if qty_confirm exceeds dn_qty
            if (($d['qty_confirm'] + $dnDetailRecord->qty_confirm) > $dnDetailRecord->dn_qty) {
                throw new \Exception("Quantity Confirm exceeds Quantity Requested", 422);
            }

            // Calculate the wave number
            $lastOutstanding = DN_Detail_Outstanding::where('dn_detail_no', $d['dn_detail_no'])
                ->orderBy('wave', 'desc')
                ->first();
                // dd($lastOutstanding->wave);
            $wave = ($lastOutstanding->wave ?? 0) + 1;

            DN_Detail_Outstanding::create([
                "no_dn" => $data['no_dn'],
                "dn_detail_no" => $d['dn_detail_no'],
                "qty_outstanding" => $d['qty_confirm'],
                "add_outstanding_date" => Carbon::now()->format("Y-m-d"),
                "add_outstanding_time" => Carbon::now()->format("H:i:s"),
                "wave" => $wave,
            ]);

            // increment qty_confirm data from table dn_detail
            $dnDetailRecord->increment("qty_confirm", $d['qty_confirm']);

            // Check if the total qty_confirm has reached dn_qty
            if ($dnDetailRecord->qty_confirm >= $dnDetailRecord->dn_qty) {
                throw new \Exception("All quantities have been confirmed. No more outstanding transactions allowed.", 422);
            }
        }
        return true;
    }
}
