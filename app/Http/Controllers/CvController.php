<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use function Illuminate\Log\log;

class CvController extends Controller
{
    public function uploadCv(Request $request)
    {
        try {    
            $request->validate([
                'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
            ]);
    
            $file = $request->file('cv');
    
            $filePath = $file->store('cvs', 'public');

            Cv::create([
                'user_id' => Auth::user()->id,
                'file_path' => $filePath
            ]);

            return response()->json([
                'message' => 'CV uploaded successfully!',
                'path' => $filePath
            ], 201);
    
        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());  
    
            return response()->json([
                'error' => 'Upload failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
     
    public function getCvs()
    {
        $user = Auth::user();
        $cvs = $user->cvs;

        return response()->json($cvs);
    }
}
