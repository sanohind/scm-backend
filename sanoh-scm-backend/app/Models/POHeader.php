<?php

namespace App\Models;

use App\Models\PartnerLocal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
