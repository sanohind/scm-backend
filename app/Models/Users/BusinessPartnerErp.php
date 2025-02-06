<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPartnerErp extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'business_partner';
}
