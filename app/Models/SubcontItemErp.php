<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubcontItemErp extends Model
{
    protected $connection = "sqlsrv";

    protected $table = "item_data";
}
