<?php

namespace App\Models\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

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
