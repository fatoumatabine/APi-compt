<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'compte_id' => 'required|exists:comptes,id',
            'type' => 'required|in:depot,retrait',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $transaction = $this->service->create($data);
        return response()->json($transaction, 201);
    }

    public function show(string $id)
    {
        $transaction = $this->service->find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($transaction);
    }

    public function update(Request $request, string $id)
    {
        // Perhaps not allow update
        return response()->json(['message' => 'Not allowed'], 405);
    }

    public function destroy(string $id)
    {
        // Perhaps not allow delete
        return response()->json(['message' => 'Not allowed'], 405);
    }

    // Custom methods for depot/retrait
    public function depot(Request $request)
    {
        $data = $request->validate([
            'compte_id' => 'required|exists:comptes,id',
            'montant' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $transaction = $this->service->depot($data['compte_id'], $data['montant'], $data['description']);
        if ($transaction) {
            return response()->json($transaction, 201);
        }
        return response()->json(['message' => 'Failed'], 400);
    }

    public function retrait(Request $request)
    {
        $data = $request->validate([
            'compte_id' => 'required|exists:comptes,id',
            'montant' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $transaction = $this->service->retrait($data['compte_id'], $data['montant'], $data['description']);
        if ($transaction) {
            return response()->json($transaction, 201);
        }
        return response()->json(['message' => 'Insufficient funds or failed'], 400);
    }
}
