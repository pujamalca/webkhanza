<?php

namespace App\Filament\Clusters\UserRole\Resources\Roles\Pages;

use App\Filament\Clusters\UserRole\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;
use Filament\Notifications\Notification;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;
    
    protected array $selectedPermissions = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->before(function ($record, $action) {
                    $usersCount = $record->users()->count();
                    
                    if ($usersCount > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus role')
                            ->body("Role '{$record->name}' tidak dapat dihapus karena masih memiliki {$usersCount} user yang menggunakan role ini.")
                            ->persistent()
                            ->send();
                            
                        $action->cancel();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        
        if ($record && $record->exists) {
            // Get current permissions for this role
            $permissionIds = $record->permissions->pluck('id')->toArray();
            
            \Log::info('EditRole loading existing permissions:', [
                'role_id' => $record->id,
                'role_name' => $record->name,
                'permission_ids' => $permissionIds
            ]);
            
            // Get permission IDs for each section and populate section fields
            $dashboardIds = Permission::whereIn('name', [
                'dashboard_access', 'system_settings_access', 'system_logs_access'
            ])->pluck('id')->toArray();
            $data['dashboard_permissions'] = array_values(array_intersect($permissionIds, $dashboardIds));
            
            $adminIds = Permission::where('name', 'like', 'administrator_access')
                ->orWhere('name', 'like', 'users_%')
                ->orWhere('name', 'like', 'roles_%')
                ->orWhere('name', '=', 'multi_device_login')
                ->pluck('id')->toArray();
            $data['admin_permissions'] = array_values(array_intersect($permissionIds, $adminIds));

            $ermIds = Permission::where('name', 'like', 'erm_access')
                ->orWhere('name', 'like', 'registrasi_%')
                ->orWhere('name', 'like', 'rawat_jalan_%')
                ->orWhere('name', 'like', 'pasien_%')
                ->pluck('id')->toArray();
            $data['erm_permissions'] = array_values(array_intersect($permissionIds, $ermIds));
                
            $sdmIds = Permission::where('name', 'like', 'sdm_access')
                ->orWhere('name', 'like', 'pegawai_%')
                ->orWhere('name', 'like', 'dokter_%')
                ->orWhere('name', 'like', 'petugas_%')
                ->orWhere('name', 'like', 'berkas_pegawai_%')
                ->pluck('id')->toArray();
            $data['sdm_permissions'] = array_values(array_intersect($permissionIds, $sdmIds));
                
            $pegawaiIds = Permission::whereIn('name', [
                'view_own_absent', 'view_all_absent', 'create_absent', 'edit_absent', 'delete_absent',
                'view_own_cuti', 'view_all_cuti', 'create_cuti', 'approve_cuti', 'edit_cuti', 'delete_cuti'
            ])->pluck('id')->toArray();
            $data['pegawai_permissions'] = array_values(array_intersect($permissionIds, $pegawaiIds));
                
            $masterIds = Permission::where('name', 'like', 'master_%')
                ->pluck('id')->toArray();
            $data['master_permissions'] = array_values(array_intersect($permissionIds, $masterIds));
            
            \Log::info('EditRole populated checkboxes with permissions:', [
                'dashboard_permissions' => $data['dashboard_permissions'],
                'admin_permissions' => $data['admin_permissions'],
                'erm_permissions' => $data['erm_permissions'],
                'sdm_permissions' => $data['sdm_permissions'],
                'pegawai_permissions' => $data['pegawai_permissions'],
                'master_permissions' => $data['master_permissions']
            ]);
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Debug: log what we're trying to update
        \Log::info('EditRole data before save:', $data);
        
        // Store permission IDs temporarily for use after save
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
        if (!empty($data['sdm_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['sdm_permissions']);
        }
        if (!empty($data['pegawai_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['pegawai_permissions']);
        }
        if (!empty($data['master_permissions'])) {
            $allPermissions = array_merge($allPermissions, $data['master_permissions']);
        }
        
        // Store permissions for afterSave
        $this->selectedPermissions = array_unique($allPermissions);
        
        \Log::info('EditRole selected permissions:', $this->selectedPermissions);
        
        // Remove all permission fields so they don't get saved to roles table
        unset($data['dashboard_permissions']);
        unset($data['admin_permissions']);
        unset($data['erm_permissions']);
        unset($data['sdm_permissions']);
        unset($data['pegawai_permissions']);
        unset($data['master_permissions']);
        unset($data['permissions']); // Also remove this to avoid column error
        
        // Remove any other permission-related fields that might be added by Filament
        foreach ($data as $key => $value) {
            if (str_contains($key, 'permission') || $key === 'permissions') {
                unset($data[$key]);
            }
        }
        
        \Log::info('EditRole final data for database:', $data);
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Update permissions for the role after it's saved
        $role = $this->getRecord();
        
        if (!empty($this->selectedPermissions)) {
            // Convert permission IDs to Permission objects
            $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();
            
            \Log::info('EditRole converting permission IDs to objects:', [
                'permission_ids' => $this->selectedPermissions,
                'permission_names' => $permissions->pluck('name')->toArray()
            ]);
            
            // Sync permissions (this will remove old ones and add new ones)
            $role->syncPermissions($permissions);
            
            \Log::info('EditRole updated permissions for role:', [
                'role_id' => $role->id,
                'permissions_count' => $permissions->count()
            ]);
        } else {
            // If no permissions selected, remove all permissions
            $role->syncPermissions([]);
            
            \Log::info('EditRole removed all permissions for role:', [
                'role_id' => $role->id
            ]);
        }
    }
}
