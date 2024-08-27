<?php

namespace App\Models;

use App\Models\PartnerLocal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PO_Header extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = "po_no";

    protected $keyType = 'string';

    public $timestamps = false;

    protected $table = "po_header";

    protected $fillable = [
        'po_no',
        'bp_code',
        'po_type_desc',
        'po_date',
        'po_year',
        'po_period',
        'po_status',
        'references_1',
        'references_2',
        'attn_name',
        'po_currency',
        'pr_no',
        'planned_receipt_date',
        'payment_term',
        'po_origin',
        'po_revision_no',
        'po_revision_date',
        'response',
        'accept_at',
        'decline_at',
        'po_printed_at',
    ];

    // bussines_partner relationship
    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnerLocal::class, 'bp_code', 'bp_code');
    }

    // po_detail relationship
    public function poDetail(): HasMany
    {
        return $this->hasMany(PO_Detail::class, 'po_no', 'po_no');
    }
}
