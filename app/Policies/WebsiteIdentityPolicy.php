<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebsiteIdentity;
use Illuminate\Auth\Access\Response;

/**
 * Policy untuk Website Identity
 * 
 * Hanya administrator yang dapat mengakses dan mengelola identitas website
 */
class WebsiteIdentityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        try {
            // Prioritize permission over role check
            return $user->can('manage_website_identity');
        } catch (\Exception $e) {
            // Fallback ke role check jika permission system error
            return $user->hasRole(['Super Admin', 'Admin']);
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WebsiteIdentity $websiteIdentity): bool
    {
        try {
            return $user->can('manage_website_identity');
        } catch (\Exception $e) {
            return $user->hasRole(['Super Admin', 'Admin']);
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya boleh create jika belum ada data (singleton pattern)
        if (WebsiteIdentity::exists()) {
            return false;
        }

        try {
            return $user->can('manage_website_identity');
        } catch (\Exception $e) {
            return $user->hasRole(['Super Admin', 'Admin']);
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WebsiteIdentity $websiteIdentity): bool
    {
        try {
            return $user->can('manage_website_identity');
        } catch (\Exception $e) {
            return $user->hasRole(['Super Admin', 'Admin']);
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WebsiteIdentity $websiteIdentity): bool
    {
        // Tidak boleh delete untuk menjaga singleton pattern
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WebsiteIdentity $websiteIdentity): bool
    {
        try {
            return $user->can('manage_website_identity');
        } catch (\Exception $e) {
            return $user->hasRole(['Super Admin', 'Admin']);
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WebsiteIdentity $websiteIdentity): bool
    {
        // Tidak boleh force delete untuk menjaga singleton pattern
        return false;
    }
}