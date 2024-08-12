<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POHeader extends Model
{
    use HasFactory;

    protected $table = "po_header";

    protected $fillable = [
        'po_no',
        'bp_code',
        'response',
    ];

    // Relationship
   public function poheader(): BelongsTo
   {
       return $this->belongsTo(PartnerLocal::class, 'bp_code', 'bp_code');
   }
}
