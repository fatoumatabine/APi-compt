<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompteResource;
use App\Scopes\CompteScope;
use App\Services\CompteService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CompteController extends Controller
{
    use ApiResponse;
    protected $service;

    public function __construct(CompteService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/comptes",
     *     tags={"Comptes"},
     *     summary="Lister les comptes",
     *     @OA\Parameter(name="page", in="query", description="Numéro de page", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="limit", in="query", description="Nombre par page", @OA\Schema(type="integer", default=10, maximum=100)),
     *     @OA\Parameter(name="type", in="query", description="Type de compte", @OA\Schema(type="string", enum={"cheque", "epargne"})),
     *     @OA\Parameter(name="statut", in="query", description="Statut", @OA\Schema(type="string", enum={"actif", "bloque", "ferme"})),
     *     @OA\Parameter(name="search", in="query", description="Recherche", @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort", in="query", description="Tri", @OA\Schema(type="string", enum={"created_at", "numero_compte"})),
     *     @OA\Parameter(name="order", in="query", description="Ordre", @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des comptes",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Compte")),
     *             @OA\Property(property="pagination", ref="#/components/schemas/Pagination"),
     *             @OA\Property(property="links", ref="#/components/schemas/Links")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = \App\Models\Compte::with('client');

        // Filters
        if ($request->has('type') && in_array($request->type, ['epargne', 'cheque'])) {
            $query->where('type_compte', $request->type);
        }

        if ($request->has('statut') && in_array($request->statut, ['actif', 'bloque', 'ferme'])) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_compte', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('nom', 'like', "%{$search}%")
                                  ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $allowedSorts = ['created_at', 'solde', 'numero_compte'];
        if (in_array($sort, $allowedSorts)) {
            if ($sort == 'solde') {
                // Custom sort for computed solde, but since it's computed, perhaps skip or use raw
                // For simplicity, sort by created_at
            } else {
                $query->orderBy($sort, $order);
            }
        }

        // Pagination
        $limit = min($request->get('limit', 10), 100);
        $comptes = $query->paginate($limit);

        return $this->paginatedResponse(CompteResource::collection($comptes));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/comptes/archived",
     *     tags={"Comptes"},
     *     summary="Lister les comptes archivés",
     *     @OA\Parameter(name="page", in="query", description="Numéro de page", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="limit", in="query", description="Nombre par page", @OA\Schema(type="integer", default=10, maximum=100)),
     *     @OA\Parameter(name="type", in="query", description="Type de compte", @OA\Schema(type="string", enum={"cheque", "epargne"})),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des comptes archivés",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Compte")),
     *             @OA\Property(property="pagination", ref="#/components/schemas/Pagination"),
     *             @OA\Property(property="links", ref="#/components/schemas/Links")
     *         )
     *     )
     * )
     */
    public function archived(Request $request)
    {
        $query = \App\Models\Compte::with('client')->withoutGlobalScope(CompteScope::class)->where('statut', 'ferme');

        // Similar filters as index
        if ($request->has('type') && in_array($request->type, ['epargne', 'cheque'])) {
            $query->where('type_compte', $request->type);
        }

        // Pagination
        $limit = min($request->get('limit', 10), 100);
        $comptes = $query->paginate($limit);

        return $this->paginatedResponse(CompteResource::collection($comptes));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'numero_compte' => 'nullable|string|unique:comptes',
            'type_compte' => 'nullable|in:cheque,epargne',
            'devise' => 'nullable|string',
            'statut' => 'nullable|in:actif,bloque,ferme',
            'client_id' => 'required|exists:clients,id',
        ]);
        $compte = $this->service->create($data);
        return new CompteResource($compte);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/comptes/{id}",
     *     tags={"Comptes"},
     *     summary="Détails d'un compte",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID du compte", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du compte",
     *         @OA\JsonContent(ref="#/components/schemas/Compte")
     *     ),
     *     @OA\Response(response=404, description="Compte non trouvé")
     * )
     */
    public function show(string $id)
    {
        $compte = $this->service->find($id);
        if (!$compte) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return new CompteResource($compte);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'numero_compte' => 'nullable|string|unique:comptes,numero_compte,' . $id,
            'type_compte' => 'nullable|in:cheque,epargne',
            'devise' => 'nullable|string',
            'statut' => 'nullable|in:actif,bloque,ferme',
            'client_id' => 'nullable|exists:clients,id',
        ]);
        $compte = $this->service->update($id, $data);
        if (!$compte) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return new CompteResource($compte);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->service->delete($id)) {
            return response()->json(['message' => 'Deleted']);
        }
        return response()->json(['message' => 'Not found'], 404);
    }
}
