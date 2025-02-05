<?php

namespace App\Models\PurchaseOrder;

use App\Models\Users\PartnerLocal;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class PoHeader extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'po_no';

    protected $keyType = 'string';

    public $timestamps = false;

    protected $table = 'po_header';

    protected $fillable = [
        'po_no',
        'supplier_code',
        'supplier_name',
        'po_type_desc',
        'po_date',
        'po_year',
        'po_period',
        'po_status',
        'reference_1',
        'reference_2',
        'attn_name',
        'po_currency',
        'pr_no',
        'planned_receipt_date',
        'payment_term',
        'po_origin',
        'po_revision_no',
        'po_revision_date',
        'response',
        'reason',
        'accept_at',
        'decline_at',
        'po_printed_at',
    ];

    // bussines_partner relationship
    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnerLocal::class, 'supplier_code', 'bp_code');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_code', 'bp_code');
    }

    // po_detail relationship
    public function poDetail(): HasMany
    {
        return $this->hasMany(PoDetail::class, 'po_no', 'po_no');
    }
}
