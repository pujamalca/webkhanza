<?php

namespace App\Filament\Clusters\UserRole\Resources\Roles\Pages;

use App\Filament\Clusters\UserRole\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
    
    protected array $selectedPermissions = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Debug: log what we're trying to save
        \Log::info('CreateRole data before create:', $data);
        
        // Store permission IDs temporarily for use after create
        $allPermissions = [];
        
        if (!empty($data['dashboard_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['dashboard_permissions']);
        }
        if (!empty($data['admin_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['admin_permissions']);
        }
        if (!empty($data['erm_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['erm_permissions']);
        }
        if (!empty($data['soapie_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['soapie_permissions']);
        }
        if (!empty($data['sdm_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['sdm_permissions']);
        }
        if (!empty($data['pegawai_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['pegawai_permissions']);
        }
        if (!empty($data['master_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['master_permissions']);
        }
        if (!empty($data['marketing_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['marketing_permissions']);
        }
        if (!empty($data['website_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['website_permissions']);
        }
        
        // Store permissions for afterCreate
        $this->selectedPermissions = array_unique($allPermissions);
        
        \Log::info('CreateRole selected permissions:', $this->selectedPermissions);
        
        // Remove all permission fields so they don't get saved to roles table
        unset($data['dashboard_permissions']);
        unset($data['admin_permissions']);
        unset($data['erm_permissions']);
        unset($data['soapie_permissions']);
        unset($data['sdm_permissions']);
        unset($data['pegawai_permissions']);
        unset($data['master_permissions']);
        unset($data['marketing_permissions']);
        unset($data['website_permissions']);
        unset($data['permissions']); // Also remove this to avoid column error
        
        // Remove any other permission-related fields that might be added by Filament
        foreach ($data as $key => $value) {
            if (str_contains($key, 'permission') || $key === 'permissions') {
                unset($data[$key]);
            }
        }
        
        \Log::info('CreateRole final data for database:', $data);
        
        return $data;
    }
    
    public function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Create the role first
        $record = static::getModel()::create($data);
        
        \Log::info('CreateRole record created:', [
            'initial_id' => $record->id,
            'name' => $record->name
        ]);
        
        // Try to find the record by name since ID might be inconsistent
        $actualRecord = static::getModel()::where('name', $record->name)
            ->where('guard_name', $record->guard_name)
            ->first();
        
        if ($actualRecord) {
            \Log::info('CreateRole found actual record:', [
                'actual_id' => $actualRecord->id,
                'name' => $actualRecord->name
            ]);
            
            // Use the actual record for permission assignment
            $workingRecord = $actualRecord;
        } else {
            // Fallback to original record
            $workingRecord = $record;
        }
        
        // Assign permissions after role is created
        if (!empty($this->selectedPermissions)) {
            \Log::info('CreateRole assigning permissions:', [
                'role_id' => $workingRecord->id,
                'role_name' => $workingRecord->name,
                'permission_ids' => $this->selectedPermissions
            ]);
            
            try {
                // Convert permission IDs to Permission objects
                $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();
                
                \Log::info('CreateRole found permissions:', [
                    'requested_ids' => $this->selectedPermissions,
                    'found_permissions' => $permissions->pluck('id')->toArray()
                ]);
                
                if ($permissions->isNotEmpty()) {
                    $workingRecord->syncPermissions($permissions);
                    
                    \Log::info('CreateRole successfully assigned permissions:', [
                        'role_id' => $workingRecord->id,
                        'permissions_count' => $permissions->count()
                    ]);
                    
                    // Verify the permissions were actually assigned
                    $verifyCount = $workingRecord->permissions()->count();
                    \Log::info('CreateRole permission verification:', [
                        'role_id' => $workingRecord->id,
                        'assigned_count' => $verifyCount
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('CreateRole permission assignment failed:', [
                    'role_id' => $workingRecord->id,
                    'error' => $e->getMessage(),
                    'permission_ids' => $this->selectedPermissions
                ]);
            }
        }
        
        // Always return the original record to maintain Filament's expectations
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        // Always redirect to index to avoid ID issues
        return $this->getResource()::getUrl('index');
    }
}
