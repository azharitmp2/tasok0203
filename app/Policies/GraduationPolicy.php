<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Graduation;
use App\Models\User;

class GraduationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allowed for everyone so students can browse upcoming options
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Graduation $graduation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Graduation $graduation): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Graduation $graduation): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        // Financial Guard Rule: Prevent hard/soft deleting if money has already moved!
        return $graduation->students()
            ->whereNotNull('paid_at')
            ->doesntExist();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Graduation $graduation): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Graduation $graduation): bool
    {
        return $user->isAdmin() && $graduation->students()->doesntExist();
    }
}