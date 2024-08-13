<?php

namespace App\Models;

use App\Models\POHeader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PODetail extends Model
{
    use HasFactory;

    protected $table = "po_detail";

    // Relationship
    public function podetail(): BelongsTo
    {
        return $this->belongsTo(POHeader::class, 'po_no', 'po_no');
    }
}
