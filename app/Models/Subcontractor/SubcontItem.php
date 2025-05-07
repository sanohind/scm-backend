<?php

namespace App\Models\Subcontractor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SubcontItem extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'subcont_item';

    protected $primaryKey = 'sub_item_id';

    protected $keyType = 'integer';

    public $timestamps = false;

    protected $fillable = [
        'sub_item_id',
        'bp_code',
        'item_code',
        'item_name',
        'item_old_name',
        'status',
        'min_stock_incoming',
        'min_stock_outgoing',
    ];

    // SubcontTransaction relation
    public function subTrans(): HasMany
    {
        return $this->hasMany(SubcontTransaction::class, 'sub_item_id', 'sub_item_id');
    }

    public function subStock(): HasOne
    {
        return $this->hasOne(SubcontStock::class, 'sub_item_id', 'sub_item_id');
    }
}
