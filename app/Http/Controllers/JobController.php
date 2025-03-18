<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller {
    public function index() {
        return response()->json(Job::all());
    }

    public function store(Request $request) {

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'company' => 'required|string',
                'location' => 'required|string',
                'salary' => 'nullable|numeric',
            ]);
    
            $job = Job::create();
    
            return response()->json($job, 201);
        } catch(\Exception $e) {
            return response()->json([
                'error' => 'creation failed',
                'details' => $e->getMessage()
            ], 500);
        }
}
    

    public function show(Job $job) {
        return response()->json($job);
    }

    public function update(Request $request, Job $job) {
 
        $data = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'company' => 'string',
            'location' => 'string',
            'salary' => 'nullable|numeric',
        ]);

        $job->update($data);

        return response()->json($job);
    }

    public function destroy(Job $job) {
        try {
            $job->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'deleting failed',
                'details' => $e->getMessage()
            ], 500);
        }
        }
    }