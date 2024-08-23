<?php

namespace App\Models;

use App\Models\DN_Detail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DN_Label extends Model
{
    use HasFactory;

    protected $primaryKey = "dn_label_no";

    public $timestamps = false;

    protected $table = "dn_label";

    // relationship dn_detail
    public function dnDetail(): BelongsTo
    {
        return $this->belongsTo(DN_Detail::class, 'dn_detail_no', 'dn_detail_no');
    }
}
