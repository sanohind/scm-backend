<?php

namespace App\Models;

use App\Models\PartnerLocal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Email extends Model
{
    protected $connection = "mysql";
    protected $table = "email";
    protected $primaryKey = "email_id";
    public $timestamps = true;

    protected $fillable = [
        "email",
    ];

    /**
     * The partner that belong to the EmailOrganization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function partner(): BelongsToMany
    {
        return $this->belongsToMany(PartnerLocal::class, 'business_partner_email', 'email_id',
        'partner_id')->withTimestamps();
    }
}
