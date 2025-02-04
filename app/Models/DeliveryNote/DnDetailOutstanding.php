<?php

namespace App\Models\DeliveryNote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DnDetailOutstanding extends Model
{
    /** @use HasFactory<\Database\Factories\DNDetailOutstandingFactory> */
    use HasFactory;

    protected $primaryKey = 'dn_detail_outstanding_ no';

    public $timestamps = false;

    protected $table = 'dn_detail_outstanding';

    protected $fillable = [
        'dn_detail_outstanding_no',
        'dn_detail_no',
        'no_dn',
        'qty_outstanding',
        'add_outstanding_date',
        'add_outstanding_time',
        'wave',
    ];

    // Relationship to dnDetail
    public function dnDetail(): BelongsTo
    {
        return $this->belongsTo(DnDetail::class, 'dn_detail_no', 'dn_detail_no');
    }

    // Relationship to dnHeader
    public function dnHeader(): BelongsTo
    {
        return $this->belongsTo(DnHeader::class, 'no_dn', 'no_dn');
    }
}
