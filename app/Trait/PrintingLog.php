<?php

namespace App\Trait;

use App\Models\Log\PrintLog;
use Illuminate\Support\Facades\Auth;

trait PrintingLog
{
    /**
     * Summary of printLog
     * For printing log
     * @param string $methodType    value for this params is like "PO", "DN", etc.
     * @param string $methodKey value for this params is the number of document like no_dn and po_no
     * @param string $printingType value for this params is like "File" or "Label"
     * @example printLog("PO", "PO0000123", "File") example for define params value
     * @return void
     *
     */
    public function printLog(string $methodType, string $methodKey, string $printingType = 'File'): void
    {
        $user = Auth::user();
        $name = $user->name;
        $bpCode = $user->bp_code;

        PrintLog::create([
            'method_type' => $methodType,
            'method_key' => $methodKey,
            'printing_type' => $printingType,
            'created_by' => "$bpCode - $name",
        ]);
    }
}
