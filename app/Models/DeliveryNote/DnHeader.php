<?php

namespace App\Models\DeliveryNote;

use App\Models\PurchaseOrder\PoHeader;
use App\Models\Users\BusinessPartner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class DnHeader extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'no_dn';

    protected $keyType = 'string';

    public $timestamps = false;

    protected $table = 'dn_header';

    protected $fillable = [
        'no_dn',
        'po_no',
        'supplier_code',
        'supplier_name',
        'dn_created_date',
        'dn_year',
        'dn_period',
        'plan_delivery_date',
        'plan_delivery_time',
        'status_desc',
        'packing_slip',
        'confirm_update_at',
        'dn_printed_at',
        'dn_label_printed_at',
    ];

    // Relationship poheader
    public function partner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'supplier_code', 'bp_code');
    }

    public function poHeader(): BelongsTo
    {
        return $this->belongsTo(PoHeader::class, 'po_no', 'po_no');
    }

    // Relationship dndetail
    public function dnDetail(): HasMany
    {
        return $this->hasMany(DnDetail::class, 'no_dn', 'no_dn');
    }

    // Relationship dnOutstanding
    public function dnOutstanding(): HasMany
    {
        return $this->hasMany(DnDetailOutstanding::class, 'no_dn', 'no_dn');
    }
}
