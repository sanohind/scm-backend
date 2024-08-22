<?php

namespace App\Models;

use App\Models\PO_Header;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DN_Header extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = "no_dn";

    protected $keyType = 'string';

    public $timestamps = false;

    protected $table = "dn_header";

    // Relationship poheader
    public function poHeader(): BelongsTo
    {
        return $this->belongsTo(PO_Header::class, 'po_no', 'po_no');
    }

    // Relationship dndetail
    public function dnDetail(): HasMany
    {
        return $this->hasMany(Comment::class, 'foreign_key', 'local_key');
    }
}
