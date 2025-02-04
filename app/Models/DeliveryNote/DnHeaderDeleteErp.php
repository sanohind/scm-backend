<?php

namespace App\Models\DeliveryNote;

use Illuminate\Database\Eloquent\Model;

class DnHeaderDeleteErp extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dn_header_deleted';
}
