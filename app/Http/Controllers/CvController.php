<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="CV",
 *     description="Endpoints pour la gestion des CV"
 * )
 */
class CvController extends Controller
{
    /**
     * @OA\Post(
     *     path="/cv/upload",
     *     summary="Upload un CV",
     *     tags={"CV"},
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="cv",
     *                     type="string",
     *                     format="binary",
     *                     description="Fichier CV à uploader"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="CV uploadé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="CV uploaded successfully!"),
     *             @OA\Property(property="path", type="string", example="cvs/mon_cv.pdf")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur d'upload",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Upload failed"),
     *             @OA\Property(property="details", type="string", example="File format not allowed")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/cv/getcv",
     *     summary="Récupérer les CV de l'utilisateur",
     *     tags={"CV"},
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des CV",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=2),
     *                 @OA\Property(property="file_path", type="string", example="cvs/mon_cv.pdf"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-18T12:34:56Z")
     *             )
     *         )
     *     )
     * )
     */
    public function getCvs()
    {
        $user = Auth::user();
        $cvs = $user->cvs;

        return response()->json($cvs);
    }
}
