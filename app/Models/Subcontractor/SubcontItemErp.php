<?php

namespace App\Models\Subcontractor;

use Illuminate\Database\Eloquent\Model;

class SubcontItemErp extends Model
{
    protected $connection = "mysql";

    protected $table = "subcont_item_erp";

    protected $primaryKey = "item_id";

    protected $keyType = "integer";

    protected $timestamps = true;

    protected $fillable = [
        "item",
        "description",
        "item_group",
        "group_desc",
        "material",
        "old_item",
        "unit",
        "div_code",
        "divisi",
        "model",
    ];
}
