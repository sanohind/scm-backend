<?php

namespace App\Models\PurchaseOrder;

use Illuminate\Database\Eloquent\Model;

class PO_Detail_Delete_ERP extends Model
{
    protected $connection = "sqlsrv";

    protected $table = "po_header_deleted";
}
