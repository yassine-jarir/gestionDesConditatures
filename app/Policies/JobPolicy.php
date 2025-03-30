<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Job;

class JobPolicy
{
    public function viewMyJobs(User $user)
    {
        return $user->role === 'recruteur';
    }
    public function create(User $user)
    {
        return $user->role === 'recruteur';
    }

    public function update(User $user, Job $job)
    {
        return $user->role === 'recruteur' && $user->id === $job->recruteur_id;
    }

    public function delete(User $user, Job $job)
    {
        return $user->role === 'recruteur' && $user->id === $job->recruteur_id;
    }

    public function viewAny(User $user)
    {
        return $user->role === 'admin';
    }

    public function viewCandidates(User $user, Job $job)
    {
        return $user->role === 'recruteur' && $user->id === $job->recruiter_id;
    }

    public function exportCandidates(User $user, Job $job)
    {
        return $user->role === 'recruteur' && $user->id === $job->recruiter_id;
    }
}
