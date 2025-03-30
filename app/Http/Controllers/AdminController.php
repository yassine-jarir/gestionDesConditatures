<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Job;
use App\Models\User;

class AdminController extends Controller
{
    use AuthorizesRequests;

    public function getAllJobs()
{
    try {
        $jobs = Job::all(); 
        return response()->json($jobs);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while fetching jobs.'], 500);
    }
}

    public function deleteJob($jobId)
    {
        try {
            $job = Job::findOrFail($jobId);

            $this->authorize('delete', $job);

            $job->delete(); 
            return response()->json(['message' => 'Job deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the job.'], 500);
        }
    }

    public function createJob(Request $request)
    {
 
        $this->authorize('createJob', User::class);
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string',
                'salary' => 'required',
            ]);
            if (!$this->authorize('create', Job::class)) {
                return response()->json(['error' => 'You are not authorized to post a job.'], 403);
            }
            $job = Job::create($request->all());

            return response()->json($job, 201);  
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the job.'], 500);
        }
    }

    public function getAllUsers()
    {
        if (!$this->authorize('view', User::class)) {
            return response()->json(['error' => 'You are not authorized to get users'], 403);
        }
        try {
            $users = User::all(); 
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching users.'], 500);
        }
}

public function updateUser(Request $request, $userId)
{
    try {
        $user = User::findOrFail($userId);
        
         $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:candidate,recruiter,admin', 
        ]);

        $user->update($request->all());
        return response()->json($user);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'User not found'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while updating the user.'], 500);
    }
}

public function deleteUser($userId)
{
    try {
        $user = User::findOrFail($userId);

         $this->authorize('delete', $user);

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'User not found'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while deleting the user.'], 500);
    }
}

}
