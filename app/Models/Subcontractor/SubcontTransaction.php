<?php

namespace App\Models\Subcontractor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubcontTransaction extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'subcont_transaction';

    protected $primaryKey = 'sub_transaction_id';

    protected $keyType = 'integer';

    public $timestamps = false;

    protected $fillable = [
        'sub_transaction_id',
        'bp_code',
        'delivery_note',
        'sub_item_id',
        'item_code',
        'item_name',
        'transaction_type',
        'actual_transaction_date',
        'actual_transaction_time',
        'transaction_date',
        'transaction_time',
        'qty_ok',
        'qty_ng',
        'status',
        'actual_qty_ok_receive',
        'actual_qty_ng_receive',
        'response',
        'update_at',
    ];

    // SubcontItem relation
    public function subItem(): BelongsTo
    {
        return $this->belongsTo(SubcontItem::class, 'sub_item_id', 'sub_item_id');
    }
}
