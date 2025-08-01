<?php

namespace App\Service\User;

use App\Models\Users\BusinessPartner;

class BusinessPartnerUnifiedService
{
    /**
     * Get all related business partners (parent and children)
     * @param string $bpCode
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedBusinessPartners($bpCode)
    {
        $baseCode = $this->getBaseCodeFromBpCode($bpCode);
        
        return BusinessPartner::where(function($query) use ($bpCode, $baseCode) {
            $query->where('bp_code', $bpCode)
                  ->orWhere('bp_code', $baseCode)
                  ->orWhere('parent_bp_code', $baseCode);
        })->get();
    }

    /**
     * Get unified business partner data (combines old and new system data)
     * @param string $bpCode
     * @return array
     */
    public function getUnifiedBusinessPartnerData($bpCode)
    {
        $baseCode = $this->getBaseCodeFromBpCode($bpCode);
        
        // Get all related records
        $relatedPartners = $this->getRelatedBusinessPartners($bpCode);
        
        if ($relatedPartners->isEmpty()) {
            return null;
        }

        // Get the primary record (prefer parent record if exists)
        $primaryRecord = $relatedPartners->firstWhere('bp_code', $baseCode) 
                        ?? $relatedPartners->firstWhere('bp_code', $bpCode)
                        ?? $relatedPartners->first();

        // Get all related bp_codes
        $relatedBpCodes = $relatedPartners->pluck('bp_code')->toArray();

        return [
            'primary_data' => $primaryRecord,
            'all_related_bp_codes' => $relatedBpCodes,
            'base_bp_code' => $baseCode,
            'search_bp_code' => $bpCode,
            'total_related_records' => $relatedPartners->count()
        ];
    }

    /**
     * Search business partners with unified logic
     * @param string $searchTerm
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchUnifiedBusinessPartners($searchTerm)
    {
        $baseCode = $this->getBaseCodeFromBpCode($searchTerm);
        
        return BusinessPartner::where(function($query) use ($searchTerm, $baseCode) {
            $query->where('bp_code', $searchTerm)
                  ->orWhere('bp_code', $baseCode)
                  ->orWhere('parent_bp_code', $baseCode)
                  ->orWhere('bp_name', 'like', '%' . $searchTerm . '%');
        })->where(function($query) {
            $query->where('bp_code', 'like', 'SL%')
                  ->orWhere('bp_code', 'like', 'SI%');
        })->get();
    }

    /**
     * Update parent_bp_code for existing records
     * @return void
     */
    public function updateParentBpCodes()
    {
        // Get all business partners
        $businessPartners = BusinessPartner::all();
        
        foreach ($businessPartners as $partner) {
            $baseCode = $this->getBaseCodeFromBpCode($partner->bp_code);
            
            // If this is a child record (has suffix) and doesn't have parent_bp_code set
            if ($baseCode !== $partner->bp_code && empty($partner->parent_bp_code)) {
                // Check if parent record exists
                $parentExists = BusinessPartner::where('bp_code', $baseCode)->exists();
                
                if ($parentExists) {
                    $partner->update(['parent_bp_code' => $baseCode]);
                }
            }
        }
    }

    /**
     * Get base code from bp_code (remove suffix like -1, -2, etc.)
     * @param string $bpCode
     * @return string
     */
    public function getBaseCodeFromBpCode($bpCode): string
    {
        return preg_replace('/-\d+$/', '', $bpCode);
    }

    /**
     * Check if bp_code has suffix
     * @param string $bpCode
     * @return bool
     */
    public function hasSuffix($bpCode): bool
    {
        return preg_match('/-\d+$/', $bpCode);
    }

    /**
     * Get all suffixes for a base bp_code
     * @param string $baseCode
     * @return array
     */
    public function getSuffixes($baseCode): array
    {
        return BusinessPartner::where('bp_code', 'like', $baseCode . '-%')
            ->pluck('bp_code')
            ->map(function($code) use ($baseCode) {
                return str_replace($baseCode . '-', '', $code);
            })
            ->toArray();
    }
} 