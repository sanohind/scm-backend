<?php

namespace App\Models\Users;

use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessPartner extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'business_partner';

    protected $primaryKey = 'bp_code';

    protected $keyType = 'string';

    protected $fillable = [
        'bp_code',
        'parent_bp_code',
        'bp_name',
        'bp_status_desc',
        'bp_role',
        'bp_role_desc',
        'bp_currency',
        'country',
        'adr_line_1',
        'adr_line_2',
        'adr_line_3',
        'adr_line_4',
        'bp_phone',
        'bp_fax',
    ];

    public $timestamps = false;

    public function poHeaders(): HasMany
    {
        return $this->hasMany(PoHeader::class, 'supplier_code', 'bp_code');
    }

    public function dnHeaders(): HasMany
    {
        return $this->hasMany(DnHeader::class, 'supplier_code', 'bp_code');
    }

    /**
     * The email that belong to the PartnerLocal
     */
    public function email(): BelongsToMany
    {
        return $this->belongsToMany(Email::class, 'business_partner_email', 'partner_id', 'email_id')->withTimestamps();
    }

    /**
     * Get the base bp_code (without suffix)
     */
    public function getBaseBpCodeAttribute(): string
    {
        // If this is a parent record (no suffix), return the bp_code as is
        if (!$this->parent_bp_code) {
            return $this->bp_code;
        }
        
        // If this is a child record, return the parent_bp_code
        return $this->parent_bp_code;
    }

    /**
     * Scope to get all related bp_codes (parent and children)
     */
    public function scopeRelatedBpCodes($query, $bpCode)
    {
        // Find the base bp_code
        $baseCode = $this->getBaseCodeFromBpCode($bpCode);
        
        return $query->where(function($q) use ($baseCode, $bpCode) {
            $q->where('bp_code', $baseCode)
              ->orWhere('parent_bp_code', $baseCode)
              ->orWhere('bp_code', $bpCode);
        });
    }

    /**
     * Get base code from bp_code (remove suffix like -1, -2, etc.)
     */
    private function getBaseCodeFromBpCode($bpCode): string
    {
        // Remove suffix pattern like -1, -2, -3, etc.
        return preg_replace('/-\d+$/', '', $bpCode);
    }

    /**
     * Check if this is a parent record (no suffix)
     */
    public function isParentRecord(): bool
    {
        return !$this->parent_bp_code;
    }

    /**
     * Check if this is a child record (has suffix)
     */
    public function isChildRecord(): bool
    {
        return !empty($this->parent_bp_code);
    }
}