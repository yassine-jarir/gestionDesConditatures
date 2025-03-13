<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller {
    public function apply(Request $request) {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'cv_path' => 'required|string' 
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

         $application = Application::create([
            'user_id' => $user->id,
            'job_id' => $request->job_id,
            'cv_path' => $request->cv_path
        ]);

        return response()->json([
            'message' => 'Application submitted successfully!',
            'application' => $application
        ], 201);
    }
}
