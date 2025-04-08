<?php

namespace App\Jobs\Email;

use App\Models\Users\User;
use App\Mail\PoResponseSupplier;
use App\Service\User\UserGetEmail;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Mail;
use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailNotificationDailyJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(UserGetEmail $userGetEmail): void
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

            // Use the service class instead of duplicating logic
            $email = $userGetEmail->getEmail($data);
            \Log::info($email);

            foreach ($email as $emailaddr) {
                Mail::to($emailaddr)->send(new PoResponseSupplier($poHeader, $dnHeader));
            }
        }
    }
}
