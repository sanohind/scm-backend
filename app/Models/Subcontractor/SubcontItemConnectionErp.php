<?php

namespace App\Models\Subcontractor;

use Illuminate\Database\Eloquent\Model;

class SubcontItemConnectionErp extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'item_data';
}
