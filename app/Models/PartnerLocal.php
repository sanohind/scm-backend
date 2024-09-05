<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerLocal extends Model
{
    use HasFactory;

    protected $connection = "mysql";

    protected $table = "business_partner";

    protected $primaryKey = "bp_code";

    protected $keyType = 'string';

    protected $fillable = [
        'bp_code',
        'bp_name',
        'bp_status_desc',
        'bp_role',
        'bp_role_desc',
        'bp_currency',
        'country',
        'adr_line_1',
        'adr_line_2',
        'adr_line_3',
        'adr_line_4',
        'bp_phone',
        'bp_fax',
    ];

    public $timestamps = false;
}
