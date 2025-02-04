<?php

namespace App\Models\PurchaseOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoHeaderErp extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'po_header';
}
