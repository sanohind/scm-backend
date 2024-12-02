<?php

namespace App\Models\PurchaseOrder;


use App\Models\PurchaseOrder\PO_Header;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PO_Detail extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = "po_detail_no";

    public $timestamps = false;

    protected $table = "po_detail";

    protected $fillable = [
        'po_detail_no',
        'po_no',
        'po_line',
        'po_sequence',
        'item_code',
        'code_item_type',
        'bp_part_no',
        'bp_part_name',
        'item_desc_a',
        'item_desc_b',
        'planned_receipt_date',
        'po_qty',
        'receipt_qty',
        'invoice_qty',
        'purchase_unit',
        'price',
        'amount',
    ];

    // Relationship belongs to po header
    public function poHeader(): BelongsTo
    {
        return $this->belongsTo(PO_Header::class, 'po_no', 'po_no');
    }

}
