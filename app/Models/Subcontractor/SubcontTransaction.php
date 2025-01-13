<?php

namespace App\Models\Subcontractor;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subcontractor\SubcontItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubcontTransaction extends Model
{
    use HasFactory;

    protected $connection = "mysql";

    protected $table = "subcont_transaction";

    protected $primaryKey = "sub_transaction_id";

    protected $keyType = "integer";

    public $timestamps = false;

    protected $fillable = [
        "sub_transaction_id",
        "delivery_note",
        "sub_item_id",
        "item_code",
        "transaction_type",
        "actual_transaction_date",
        "actual_transaction_time",
        "transaction_date",
        "transaction_time",
        "qty_ok",
        "qty_ng",
        "status",
        "response",
    ];

    // SubcontItem relation
    public function subItem(): BelongsTo
    {
        return $this->belongsTo(SubcontItem::class, 'item_code', 'item_code');
    }
}
