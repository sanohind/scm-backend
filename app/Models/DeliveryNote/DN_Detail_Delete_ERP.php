<?php

namespace App\Models\DeliveryNote;

use Illuminate\Database\Eloquent\Model;

class DN_Detail_Delete_ERP extends Model
{
    protected $connection = "sqlsrv";

    protected $table = "dn_detail_deleted";
}
