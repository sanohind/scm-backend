<?php

namespace App\Jobs\Email;

use App\Models\PO_Header;
use App\Models\Users\User;
use App\Mail\PoResponseSupplier;
use Illuminate\Support\Facades\Mail;
use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MailPoNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function handle(): void
    {
        // Get data User
        $user = User::where('role', 5)->get(['bp_code', 'email']);

        // Get PO open based of bp_code
        foreach ($user as $data) {

            $poHeader = PoHeader::with('user')
                ->where('supplier_code', $data->bp_code)
                ->whereIn('po_status', ['Sent', 'Open'])
                ->get();

            $dnHeader = DnHeader::with('partner')
                ->where('supplier_code', $data)
                ->whereIn('status_desc', ['Open'])
                ->get();

            Mail::to($data->email)->send(new PoResponseSupplier(po_header: $poHeader, dn_header: $dnHeader));
        }
    }
}
