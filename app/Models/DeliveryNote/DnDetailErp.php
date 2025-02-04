<?php

namespace App\Models\DeliveryNote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DnDetailErp extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'dn_detail';
}
