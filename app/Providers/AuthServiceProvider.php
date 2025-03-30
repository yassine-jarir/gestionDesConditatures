<?php

namespace App\Providers;

use App\Models\Job;
use App\Models\User;
use App\Policies\CandidatePolicy;
use App\Policies\JobPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Job::class => JobPolicy::class,
        User::class => UserPolicy::class,
        User::class => CandidatePolicy::class,
    ];


    public function boot()
    {
        $this->registerPolicies();
        Gate::define('create-job', function (User $user) {
            return $user->role === 'admin';
        });
    
        Gate::define('update-job', function (User $user, Job $job) {
            return $user->id === $job->recruteur_id;
        });
    
        Gate::define('delete-job', function (User $user, Job $job) {
            return $user->role === 'admin' || Auth::user()->id === $job->recruteur_id;
        });
    
        Gate::define('manage-users', function (User $user) {
            return $user->role === 'admin';
        });
    
        Gate::define('apply-job', function (User $user) {
            return $user->role === 'candidat';
        });
    }
}
