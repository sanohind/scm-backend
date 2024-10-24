<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Forecast extends Model
{
    use HasFactory;

    protected $connection = "mysql";

    protected $table = "forecast";

    protected $primaryKey = "forecast_id";

    public $timestamps = false;

    protected $fillable = [
        "forecast_id",
        "bp_code",
        "description",
        "file",
        "upload_at"
    ];

    /**
     * Get the user that owns the Forecast
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bp_code', 'bp_code');
    }
}
