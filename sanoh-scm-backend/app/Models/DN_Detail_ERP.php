<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DN_Detail_ERP extends Model
{
    use HasFactory;

    protected $connection = "sqlsrv";

    protected $table = "dn_detail";
}
