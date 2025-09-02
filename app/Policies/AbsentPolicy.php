<?php

namespace App\Policies;

use App\Models\Absent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_own_absent') || $user->can('view_all_absent');
    }

    public function view(User $user, Absent $absent): bool
    {
        if ($user->can('view_all_absent')) {
            return true;
        }

        return $user->can('view_own_absent') && $absent->employee_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_absent');
    }

    public function update(User $user, Absent $absent): bool
    {
        if ($user->can('edit_absent')) {
            return true;
        }

        // Employee can only edit their own records
        return $user->can('view_own_absent') && 
               $absent->employee_id === $user->id;
    }

    public function delete(User $user, Absent $absent): bool
    {
        if ($user->can('delete_absent')) {
            return true;
        }

        // Employee can only delete their own records
        return $user->can('view_own_absent') && 
               $absent->employee_id === $user->id;
    }

    public function restore(User $user, Absent $absent): bool
    {
        return $user->can('delete_absent');
    }

    public function forceDelete(User $user, Absent $absent): bool
    {
        return $user->can('delete_absent');
    }
}