<?php

namespace App\Models;

use App\Models\SubcontItem;
use Illuminate\Database\Eloquent\Model;
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
        "transaction_date",
        "transaction_time",
        "qty_ok",
        "qty_ng",
        "status",
    ];

    // SubcontItem relation
    public function subItem(): BelongsTo
    {
        return $this->belongsTo(SubcontItem::class, 'item_code', 'item_code');
    }
}
