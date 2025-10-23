<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CompteService;
use Illuminate\Http\Request;

class CompteController extends Controller
{
    protected $service;

    public function __construct(CompteService $service)
    {
        $this->service = $service;
    }

    /**
    * @OA\Get(
    *     path="/api/comptes",
     *     summary="Liste des comptes",
     *     @OA\Response(response="200", description="Liste des comptes")
     * )
     */
    public function index()
    {
        return response()->json($this->service->all());
    }

    /**
     * @OA\Post(
     *     path="/api/comptes",
     *     summary="Créer un compte",
     *     @OA\Response(response="201", description="Compte créé")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'numero_compte' => 'nullable|string|unique:comptes',
            'solde' => 'nullable|numeric',
            'type_compte' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
        ]);
        $compte = $this->service->create($data);
        return response()->json($compte, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/comptes/{id}",
     *     summary="Détails d'un compte",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Détails du compte")
     * )
     */
    public function show(string $id)
    {
        $compte = $this->service->find($id);
        if (!$compte) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($compte);
    }

    /**
     * @OA\Put(
     *     path="/api/comptes/{id}",
     *     summary="Mettre à jour un compte",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Compte mis à jour")
     * )
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'numero_compte' => 'nullable|string|unique:comptes,numero_compte,' . $id,
            'solde' => 'nullable|numeric',
            'type_compte' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
        ]);
        $compte = $this->service->update($id, $data);
        if (!$compte) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($compte);
    }

    /**
     * @OA\Delete(
     *     path="/api/comptes/{id}",
     *     summary="Supprimer un compte",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="204", description="Compte supprimé")
     * )
     */
    public function destroy(string $id)
    {
        if ($this->service->delete($id)) {
            return response()->json(['message' => 'Deleted']);
        }
        return response()->json(['message' => 'Not found'], 404);
    }
}
