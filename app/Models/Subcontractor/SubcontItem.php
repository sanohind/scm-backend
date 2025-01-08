<?php

namespace App\Models\Subcontractor;


use Illuminate\Database\Eloquent\Model;
use App\Models\Subcontractor\SubcontStock;
use App\Models\Subcontractor\SubcontTransaction;
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
        "old_item_name",
        "status",
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
