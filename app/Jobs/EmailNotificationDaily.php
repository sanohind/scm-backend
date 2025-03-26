<?php

namespace App\Jobs;

use App\Models\Users\User;
use App\Mail\PoResponseSupplier;
use Illuminate\Support\Facades\Mail;
use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Service\User\UserGetEmail;

class EmailNotificationDaily implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $bpCode = User::where('role', 5)->pluck('bp_code');

        foreach ($bpCode as $data) {
            // dd($data);
            $poHeader = PoHeader::with('user')
                ->where('supplier_code', $data)
                ->whereIn('po_status', ['Open'])
                ->get();

            $dnHeader = DnHeader::with('partner')
                ->where('supplier_code', $data)
                ->whereIn('status_desc', ['Open'])
                ->get();

            $email = $this->userGetEmail->getEmail($data);

            foreach ($email as $data) {
                Mail::to($data)->send(new PoResponseSupplier($poHeader, $dnHeader));
            }
        }
    }
}
