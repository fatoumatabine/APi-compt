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
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->service->all());
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
