<?php

namespace App\Models;

use App\Models\SubcontStock;
use App\Models\SubcontTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubcontItem extends Model
{
    use HasFactory;

    protected $connection = "mysql";

    protected $table = "subcont_item";

    protected $primaryKey = "sub_item_id";

    protected $keyType = "integer";

    public $timestamps = false;

    protected $fillable = [
        "sub_item_id",
        "bp_code",
        "item_code",
        "item_name",
    ];

    // SubcontTransaction relation
    public function subTrans(): HasMany
    {
        return $this->hasMany(SubcontTransaction::class, 'item_code', 'item_code');
    }

    public function subStock(): HasOne
    {
        return $this->hasOne(SubcontStock::class, 'sub_item_id','sub_item_id');
    }
}
