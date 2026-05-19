<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {
        // Admin can see all profiles; students can only view their own record
        return $user->isAdmin() || $student->user_id === $user->id;
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
    public function update(User $user, Student $student): bool
    {
        // Admin can edit all profiles; students can only update their own record
        return $user->isAdmin() || $student->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    /**
     * Custom Action Gate: Verify that student payment transactions are valid.
     */
    public function verify(User $user, Student $student): bool
    {
        // Requires Admin privileges AND a non-empty payment receipt file path indicator
        return $user->isAdmin() && $student->payment_receipt !== null;
    }
}