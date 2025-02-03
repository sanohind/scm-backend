<?php

namespace App\Models\PurchaseOrder;

use Illuminate\Database\Eloquent\Model;

class PoDetailDeleteErp extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'po_header_deleted';
}
