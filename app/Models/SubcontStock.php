<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubcontStock extends Model
{
    use HasFactory;

    protected $connection = "mysql";

    protected $table = "subcont_stock";

    protected $primaryKey = "sub_stock_id";

    protected $keyType = "integer";

    public $timestamps = false;

    protected $fillable = [
        "sub_stock_id",
        "sub_item_id",
        "item_code",
        "fresh_stock",
        "replating_stock",
    ];

    public function subItem(): BelongsTo
    {
        return $this->belongsTo(SubcontItem::class, 'sub_item_id', 'sub_item_id');
    }
}
