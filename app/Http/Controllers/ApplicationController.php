<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCandidatureJob;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller {
    public function apply(Request $request) {

        try {
            $request->validate([
                'job_id' => 'required',
                'cv_path' => 'required' 
            ]);
    
            $user = Auth::user();   
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
             $application = Application::create([
                'user_id' => Auth::user()->id,
                'job_id' => $request->job_id,
                'cv_path' => $request->cv_path
            ]);
    
            return response()->json([
                'message' => 'Application submitted successfully!',
                'application' => $application
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=> $th->getMessage(),
                ''=> $th
                ],500);
        }
    } 

    public function postulerPlusieurs(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'job_ids' => 'required|array|min:1',  
            'job_ids.*' => 'exists:jobs,id', 
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        
        $cvPath = $request->file('cv')->store('cvs', 'public');

        
        ProcessCandidatureJob::dispatch($request->user_id, $request->job_ids, $cvPath);

        return response()->json(['message' => 'Candidatures envoyées avec succès'], 202);
    }
    
}

