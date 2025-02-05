<?php

namespace App\Models\Forecast;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Forecast extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'forecast';

    protected $primaryKey = 'forecast_id';

    public $timestamps = false;

    protected $fillable = [
        'forecast_id',
        'bp_code',
        'description',
        'file',
        'upload_at',
    ];

    /**
     * Get the user that owns the Forecast
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bp_code', 'bp_code');
    }
}
