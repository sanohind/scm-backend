<?php

namespace App\Models\Users;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Connection
    protected $connection = 'mysql';

    // Table name
    protected $table = 'user';

    protected $primaryKey = 'user_id';

    // Column
    protected $fillable = [
        'bp_code',
        'name',
        'role',
        'status',
        'username',
        'password',
        'email',
    ];

    // Relationship
    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnerLocal::class, 'bp_code', 'bp_code');
    }
}
