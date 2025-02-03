<?php

namespace App\Models\Subcontractor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubcontStock extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'subcont_stock';

    protected $primaryKey = 'sub_stock_id';

    protected $keyType = 'integer';

    public $timestamps = false;

    protected $fillable = [
        'sub_stock_id',
        'sub_item_id',
        'item_code',
        'incoming_fresh_stock',
        'incoming_replating_stock',
        'process_fresh_stock',
        'process_replating_stock',
        'ng_fresh_stock',
        'ng_replating_stock',
    ];

    public function subItem(): BelongsTo
    {
        return $this->belongsTo(SubcontItem::class, 'sub_item_id', 'sub_item_id');
    }
}
