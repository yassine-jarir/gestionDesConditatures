<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view users list.
     */
    public function view(User $user)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can update a profile.
     */
    public function update(User $user, User $model)
    {
        // Admin can update any user, user can update their own profile
        return $user->role === 'admin' || $user->id === $model->id;
    }

    /**
     * Determine if the user can delete another user.
     */
    public function delete(User $user, User $model)
    {
        // Only admin can delete users, and cannot delete other admins
        return $user->role === 'admin' && $model->role !== 'admin';
    }

    /**
     * Determine if the user can create a job.
     */
    public function createJob(User $user)
    {
        return in_array($user->role, ['admin', 'recruiter']);
    }
}