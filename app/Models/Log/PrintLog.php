<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Model;

class PrintLog extends Model
{
    protected $connection = 'mysql';

    protected $table = 'print_log';

    public $timestamps = true;

    protected $fillable = [
        'method_type',
        'method_key',
        'printing_type',
        'created_by',
    ];
}
