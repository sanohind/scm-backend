<?php

namespace App\Models;

use App\Models\PartnerLocal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingReport extends Model
{
    use HasFactory, Notifiable;

    protected $connection = "mysql";

    // Table name
    protected $table = "listing_report";

    protected $primaryKey = "po_listing_no";

    // Column
    protected $fillable = [
        'bp_code',
        'date',
        'file',
    ];

   // Relationship
   public function listingreport(): BelongsTo
   {
       return $this->belongsTo(PartnerLocal::class, 'bp_code', 'bp_code');
   }
}
