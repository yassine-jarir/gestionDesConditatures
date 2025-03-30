<?php

namespace App\Http\Controllers;

use App\Models\Job;
 use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecruteurController extends Controller {   
    use AuthorizesRequests;

    public function getMyJobs()
    {
        try {
             $user = Auth::user();
    
             $this->authorize('viewMyJobs', Job::class);
    
             $jobs = Job::where('recruteur_id', $user->id)->get();
    
            return response()->json($jobs);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'You are not authorized to view these jobs.'],500);
         }
    }

    public function createJob(Request $request)
{
    $user = Auth::user();

     if (!$this->authorize('create', Job::class)) {
        return response()->json(['error' => 'You are not authorized to post a job.'], 403);
    }

    try {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'salary' => 'required',
            'company' => 'required|string'
        ]);

         $job = Job::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'salary' => $validatedData['salary'],
            'company' => $validatedData['company'],
            'recruteur_id' => $user->id, 
        ]);

         return response()->json($job, 201); 
    } catch (\Throwable $th) {
         return response()->json(['error' => 'Something went wrong. ' . $th->getMessage()], 500);
    }
}


    
public function deleteJob($jobId)
{
     $job = Job::findOrFail($jobId);

     if (!$this->authorize('delete-job', $job)) {
        return response()->json(['error' => 'You are not authorized to delete this job'], 403);
    }

    try {
         $job->delete();

        return response()->json(['message' => 'Job deleted successfully']);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'An error occurred while deleting the job.',
            'details' => $th->getMessage()
        ], 500);
    }
}



    



















 
}
