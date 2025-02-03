<?php

namespace App\Models\PurchaseOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoHeader_ERP extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'po_header';
}
