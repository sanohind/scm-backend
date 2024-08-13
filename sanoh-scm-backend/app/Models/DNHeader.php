<?php

namespace App\Models;

use App\Models\POHeader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DNHeader extends Model
{
    use HasFactory, Notifiable;

    protected $table = "dn_header";

    // Relationship
    public function dnheader(): BelongsTo
    {
        return $this->belongsTo(POHeader::class, 'po_no', 'po_no');
    }
}
