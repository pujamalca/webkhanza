<?php

namespace App\Policies;

use App\Models\Cuti;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CutiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_own_cuti') || $user->can('view_all_cuti');
    }

    public function view(User $user, Cuti $cuti): bool
    {
        if ($user->can('view_all_cuti')) {
            return true;
        }

        return $user->can('view_own_cuti') && $cuti->employee_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_cuti');
    }

    public function update(User $user, Cuti $cuti): bool
    {
        if ($user->can('edit_cuti')) {
            return true;
        }

        // Employee can only edit their own pending requests
        return $user->can('view_own_cuti') && 
               $cuti->employee_id === $user->id && 
               $cuti->status === 'pending';
    }

    public function delete(User $user, Cuti $cuti): bool
    {
        if ($user->can('delete_cuti')) {
            return true;
        }

        // Employee can only delete their own pending requests
        return $user->can('view_own_cuti') && 
               $cuti->employee_id === $user->id && 
               $cuti->status === 'pending';
    }

    public function approve(User $user, Cuti $cuti): bool
    {
        return $user->can('approve_cuti') && $cuti->status === 'pending';
    }

    public function reject(User $user, Cuti $cuti): bool
    {
        return $user->can('approve_cuti') && $cuti->status === 'pending';
    }

    public function restore(User $user, Cuti $cuti): bool
    {
        return $user->can('delete_cuti');
    }

    public function forceDelete(User $user, Cuti $cuti): bool
    {
        return $user->can('delete_cuti');
    }
}