<?php

namespace App\Models\DeliveryNote;

use App\Models\DeliveryNote\DN_Detail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DN_Detail_Outstanding extends Model
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
        return $this->belongsTo(DN_Detail::class, 'dn_detail_no', 'dn_detail_no');
    }

    // Relationship to dnHeader
    public function dnHeader(): BelongsTo
    {
        return $this->belongsTo(DN_Header::class, 'no_dn', 'no_dn');
    }
}
