<?php

namespace App\Models;

use App\Models\DN_Header;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DN_Detail extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = "dn_detail_no";

    public $timestamps = false;

    protected $table = "dn_detail";

    protected $fillable = [
        'dn_detail_no',
        'no_dn',
        'qty_confirm',
    ];

    // Relationship
    public function dndetail(): BelongsTo
    {
        return $this->belongsTo(DN_Header::class, 'no_dn', 'no_dn');
    }
}
