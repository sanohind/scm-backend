<?php

namespace App\Models\PerformanceReport;

use App\Models\Users\PartnerLocal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class PerformanceReport extends Model
{
    use HasFactory, Notifiable;

    protected $connection = 'mysql';

    // Table name
    protected $table = 'listing_report';

    protected $primaryKey = 'po_listing_no';

    public $timestamps = false;

    // Column
    protected $fillable = [
        'po_listing_no',
        'bp_code',
        'date',
        'file',
        'upload_at',
    ];

    // Relationship
    public function listingreport(): BelongsTo
    {
        return $this->belongsTo(PartnerLocal::class, 'bp_code', 'bp_code');
    }
}
