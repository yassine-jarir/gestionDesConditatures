<?php

namespace App\Http\Controllers;

use App\Exports\ApplicationsExport;
use App\Exports\CandidaturesExport;
use App\Jobs\ProcessCandidatureJob;
use App\Models\Competence;
use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

class CandidatController extends Controller
{
    /**
      */
      public function updateProfile(Request $request)
      {
           $user = Auth::user();
        
           if (!$user->isCandidat()) {
              return response()->json(['message' => 'Vous n\'avez pas l\'autorisation d\'ajouter des compétences.'], 403);
          }
      
           $request->validate([
              'name' => 'string|max:255',  
              'competences' => 'array',  
          ]);
      
          // Update the user’s name (if provided)
          if ($request->has('name')) {
              $user->update(['name' => $request->name]);
          }
      
           if ($request->has('competences')) {
              // Find or create competences by name doneeeeeeeeeeeeeeeeeeeeeeee
              $competenceIds = Competence::whereIn('name', $request->competences)->pluck('id');
              
              // If some competences don't exist in the database, create them doneeeeeeeeeeeeeeeeeeeeeeeee
              $newCompetences = array_diff($request->competences, $competenceIds->toArray());
              foreach ($newCompetences as $competence) {
                  $competenceModel = Competence::create(['name' => $competence]);
                  $competenceIds[] = $competenceModel->id;
              }
      
               $user->competences()->sync($competenceIds);
          }
      
           return response()->json([
              'message' => 'Profil mis à jour avec succès',
              'user' => $user->load('competences'),   
          ]);
      }
    //   public function updateProfile(Request $request)
    //   {
    //       $user = Auth::user(); 
      
    //        $request->validate([
    //           'name' => 'string|max:255',
    //           'email' => 'email|unique:users,email,' . $user->id,
    //           'password' => 'nullable|min:6',
    //       ]);
      
    //        if ($request->has('name')) {
    //           $user->name = $request->name;
    //       }
      
    //       if ($request->has('email')) {
    //           $user->email = $request->email;
    //       }
      
    //       if ($request->has('password')) {
    //           $user->password = bcrypt($request->password);
    //       }
       
    //       $user->save();
          
      
    //       return response()->json(['message' => 'Profile updated successfully!', 'user' => $user], 200);
    //   }

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
          public function apply(Request $request) {
            if (!Gate::allows('apply-job')) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
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
                    'user_id' => $user->id,
                    'job_id' => $request->job_id,
                    'cv_path' => $request->cv_path
                ]);
    
                return response()->json([
                    'message' => 'Application submitted successfully!',
                    'application' => $application
                ], 201);
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => $th->getMessage(),
                    'error' => $th
                ], 500);
            }
        } 
        public function postulerPlusieurs(Request $request) {
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
    
            return response()->json(['message' => 'Candidatures envoyées avec succès.'], 202);
        }
        public function export()
        {
            return Excel::download(new ApplicationsExport, 'applications.xlsx');
        }
}
