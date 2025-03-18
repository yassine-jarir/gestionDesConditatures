<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Jobs",
 *     description="Endpoints pour la gestion des offres d'emploi"
 * )
 */
class JobController extends Controller {
    
    /**
     * @OA\Get(
     *     path="/job",
     *     summary="Liste des offres d'emploi",
     *     tags={"Jobs"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des jobs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Développeur Laravel"),
     *                 @OA\Property(property="description", type="string", example="Nous recherchons un développeur Laravel expérimenté"),
     *                 @OA\Property(property="company", type="string", example="Tech Corp"),
     *                 @OA\Property(property="location", type="string", example="Paris, France"),
     *                 @OA\Property(property="salary", type="number", example=50000)
     *             )
     *         )
     *     )
     * )
     */
    public function index() {
        return response()->json(Job::all());
    }

    /**
     * @OA\Post(
     *     path="/job",
     *     summary="Créer une offre d'emploi",
     *     tags={"Jobs"},
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "company", "location"},
     *             @OA\Property(property="name", type="string", example="Développeur Laravel"),
     *             @OA\Property(property="description", type="string", example="Nous recherchons un développeur Laravel expérimenté"),
     *             @OA\Property(property="company", type="string", example="Tech Corp"),
     *             @OA\Property(property="location", type="string", example="Paris, France"),
     *             @OA\Property(property="salary", type="number", example=50000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Job créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Développeur Laravel"),
     *             @OA\Property(property="description", type="string", example="Nous recherchons un développeur Laravel expérimenté"),
     *             @OA\Property(property="company", type="string", example="Tech Corp"),
     *             @OA\Property(property="location", type="string", example="Paris, France"),
     *             @OA\Property(property="salary", type="number", example=50000)
     *         )
     *     )
     * )
     */
    public function store(Request $request) {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'company' => 'required|string',
                'location' => 'required|string',
                'salary' => 'nullable|numeric',
            ]);
    
            $job = Job::create($data);
    
            return response()->json($job, 201);
        } catch(\Exception $e) {
            return response()->json([
                'error' => 'Creation failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/job/{id}",
     *     summary="Obtenir les détails d'une offre d'emploi",
     *     tags={"Jobs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'offre d'emploi",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du job",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Développeur Laravel"),
     *             @OA\Property(property="description", type="string", example="Nous recherchons un développeur Laravel expérimenté"),
     *             @OA\Property(property="company", type="string", example="Tech Corp"),
     *             @OA\Property(property="location", type="string", example="Paris, France"),
     *             @OA\Property(property="salary", type="number", example=50000)
     *         )
     *     )
     * )
     */
    public function show(Job $job) {
        return response()->json($job);
    }

    /**
     * @OA\Put(
     *     path="/job/{id}",
     *     summary="Mettre à jour une offre d'emploi",
     *     tags={"Jobs"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'offre d'emploi",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Développeur Fullstack"),
     *             @OA\Property(property="description", type="string", example="Expérience requise en Laravel et React"),
     *             @OA\Property(property="company", type="string", example="Tech Corp"),
     *             @OA\Property(property="location", type="string", example="Marseille, France"),
     *             @OA\Property(property="salary", type="number", example=60000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job mis à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Job updated successfully")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Job $job) {
        $data = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'company' => 'string',
            'location' => 'string',
            'salary' => 'nullable|numeric',
        ]);

        $job->update($data);

        return response()->json([
            'message' => 'Job updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/job/{id}",
     *     summary="Supprimer une offre d'emploi",
     *     tags={"Jobs"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'offre d'emploi",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Job supprimé avec succès"
     *     )
     * )
     */
    public function destroy(Job $job) {
        try {
            $job->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Deleting failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
