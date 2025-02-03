<?php

namespace App\Models\DeliveryNote;

use Illuminate\Database\Eloquent\Model;

class DN_Header_Delete_ERP extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dn_header_deleted';
}
