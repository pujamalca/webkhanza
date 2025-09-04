<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BlogPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Website & Blog Permissions
        $websitePermissions = [
            // Website Identity Permissions
            'view_website_identity',
            'view_any_website_identity',
            'create_website_identity',
            'update_website_identity',
            'delete_website_identity',
            
            // Blog Posts Permissions
            'view_blog',
            'view_any_blog', 
            'create_blog',
            'update_blog',
            'delete_blog',
            'restore_blog',
            'force_delete_blog',
            
            // Blog Categories Permissions
            'view_blog_category',
            'view_any_blog_category',
            'create_blog_category', 
            'update_blog_category',
            'delete_blog_category',
            'restore_blog_category',
            'force_delete_blog_category',
            
            // Blog Tags Permissions
            'view_blog_tag',
            'view_any_blog_tag',
            'create_blog_tag',
            'update_blog_tag', 
            'delete_blog_tag',
            'restore_blog_tag',
            'force_delete_blog_tag',
            
            // Advanced Blog Permissions
            'publish_blog',
            'unpublish_blog',
            'schedule_blog',
            'feature_blog',
            'manage_blog_seo',
            'view_blog_analytics',
        ];

        // Create permissions
        foreach ($websitePermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Get existing roles
        $superAdminRole = Role::where('name', 'Super Admin')->orWhere('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $editorRole = Role::where('name', 'editor')->first();
        $authorRole = Role::where('name', 'author')->first();

        // Assign permissions to roles (only if roles exist)
        
        // Super Admin - All permissions
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($websitePermissions);
        }

        // Admin - All website permissions except force delete
        if ($adminRole) {
            $adminPermissions = array_filter($websitePermissions, function($permission) {
                return !str_contains($permission, 'force_delete');
            });
            $adminRole->givePermissionTo($adminPermissions);
        }

        // Editor - Manage content and SEO, but not delete permanently
        if ($editorRole) {
            $editorPermissions = [
                'view_website_identity', 'view_any_website_identity',
                'view_blog', 'view_any_blog', 'create_blog', 'update_blog', 'delete_blog', 'restore_blog',
                'view_blog_category', 'view_any_blog_category', 'create_blog_category', 'update_blog_category',
                'view_blog_tag', 'view_any_blog_tag', 'create_blog_tag', 'update_blog_tag',
                'publish_blog', 'unpublish_blog', 'schedule_blog', 'feature_blog', 'manage_blog_seo',
                'view_blog_analytics'
            ];
            $editorRole->givePermissionTo($editorPermissions);
        }

        // Author - Create and manage own content
        if ($authorRole) {
            $authorPermissions = [
                'view_website_identity', 'view_any_website_identity',
                'view_blog', 'view_any_blog', 'create_blog', 'update_blog',
                'view_blog_category', 'view_any_blog_category',
                'view_blog_tag', 'view_any_blog_tag', 'create_blog_tag',
                'manage_blog_seo'
            ];
            $authorRole->givePermissionTo($authorPermissions);
        }

        $this->command->info('Website & Blog permissions seeded successfully!');
        $this->command->info('Created permissions: ' . count($websitePermissions));
        $rolesUpdated = array_filter([
            $superAdminRole ? 'Super Admin' : null,
            $adminRole ? 'Admin' : null, 
            $editorRole ? 'editor' : null,
            $authorRole ? 'author' : null
        ]);
        $this->command->info('Updated roles: ' . implode(', ', $rolesUpdated));
    }
}