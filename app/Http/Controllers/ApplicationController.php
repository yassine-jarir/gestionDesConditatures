<?php
namespace App\Http\Controllers;

use App\Jobs\ProcessCandidatureJob;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller {
    
    /**
     * @OA\Post(
     *     path="/job/apply",
     *     summary="Submit a job application",
     *     tags={"Applications"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"job_id", "cv_path"},
     *             @OA\Property(property="job_id", type="integer", example=1),
     *             @OA\Property(property="cv_path", type="string", example="cvs/sample.pdf")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Application submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Application submitted successfully!"),
     *             @OA\Property(property="application", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/postuler-multiple",
     *     summary="Apply to multiple jobs",
     *     tags={"Applications"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "job_ids", "cv"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="job_ids", type="array",
     *                 @OA\Items(type="integer", example=2)
     *             ),
     *             @OA\Property(property="cv", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Candidatures envoyées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Candidatures envoyées avec succès.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
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
}
