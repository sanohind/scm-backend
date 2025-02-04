<?php

namespace App\Models\DeliveryNote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DnLabel extends Model
{
    use HasFactory;

    protected $primaryKey = 'dn_label_no';

    public $timestamps = false;

    protected $table = 'dn_label';

    protected $fillable = [
        'dn_label_no',
        'dn_detail_no',
    ];

    // relationship dn_detail
    public function dnDetail(): BelongsTo
    {
        return $this->belongsTo(DnDetail::class, 'dn_detail_no', 'dn_detail_no');
    }
}
