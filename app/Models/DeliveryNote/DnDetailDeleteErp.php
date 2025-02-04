<?php

namespace App\Models\DeliveryNote;

use Illuminate\Database\Eloquent\Model;

class DnDetailDeleteErp extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dn_detail_deleted';
}
