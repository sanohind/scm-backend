<?php

namespace App\Console\Commands;

use App\Models\Users\BusinessPartner;
use App\Service\User\BusinessPartnerUnifiedService;
use Illuminate\Console\Command;

class SetupBusinessPartnerRelations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business-partner:setup-relations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup parent-child relationships for business partners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to setup business partner relations...');
        
        $service = new BusinessPartnerUnifiedService();
        
        // Get all business partners
        $businessPartners = BusinessPartner::all();
        $this->info("Found {$businessPartners->count()} business partners");
        
        $updatedCount = 0;
        $createdParentCount = 0;
        
        foreach ($businessPartners as $partner) {
            $baseCode = $service->getBaseCodeFromBpCode($partner->bp_code);
            
            // If this is a child record (has suffix)
            if ($baseCode !== $partner->bp_code) {
                // Check if parent record exists
                $parentExists = BusinessPartner::where('bp_code', $baseCode)->exists();
                
                if ($parentExists) {
                    // Update child record with parent reference
                    $partner->update(['parent_bp_code' => $baseCode]);
                    $updatedCount++;
                    $this->line("Updated {$partner->bp_code} -> parent: {$baseCode}");
                } else {
                    // Create parent record if it doesn't exist
                    $parentData = $partner->toArray();
                    $parentData['bp_code'] = $baseCode;
                    $parentData['parent_bp_code'] = null;
                    
                    BusinessPartner::create($parentData);
                    $partner->update(['parent_bp_code' => $baseCode]);
                    
                    $createdParentCount++;
                    $this->line("Created parent {$baseCode} and linked {$partner->bp_code}");
                }
            }
        }
        
        $this->info("Setup completed!");
        $this->info("Updated records: {$updatedCount}");
        $this->info("Created parent records: {$createdParentCount}");
        
        return 0;
    }
} 