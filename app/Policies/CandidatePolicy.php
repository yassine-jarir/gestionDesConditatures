<?php

namespace App\Policies;

use App\Models\User;

class CandidatePolicy
{
    /**
     * Vérifie si l'utilisateur est un candidat.
     */
    private function isCandidate(User $user)
    {
        return $user->role === 'candidate';
    }

    /**
     * Autoriser un candidat à postuler.
     */
    public function apply(User $user)
    {
        return $this->isCandidate($user);
    }

    /**
     * Autoriser un candidat à gérer son profil.
     */
    public function updateProfile(User $user)
    {
        return $this->isCandidate($user);
    }
} 