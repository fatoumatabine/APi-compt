<?php

namespace App\Services;

use App\Repositories\TransactionRepository;

class TransactionService extends BaseService
{
    public function __construct(TransactionRepository $repository)
    {
        parent::__construct($repository);
    }

    // Business logic for Transaction
    public function depot($compteId, $montant, $description = null)
    {
        // Logic for deposit
        $compte = \App\Models\Compte::find($compteId);
        if ($compte) {
            $compte->solde += $montant;
            $compte->save();
            return $this->create([
                'compte_id' => $compteId,
                'type' => 'depot',
                'montant' => $montant,
                'description' => $description,
            ]);
        }
        return null;
    }

    public function retrait($compteId, $montant, $description = null)
    {
        // Logic for withdrawal
        $compte = \App\Models\Compte::find($compteId);
        if ($compte && $compte->solde >= $montant) {
            $compte->solde -= $montant;
            $compte->save();
            return $this->create([
                'compte_id' => $compteId,
                'type' => 'retrait',
                'montant' => $montant,
                'description' => $description,
            ]);
        }
        return null;
    }
}
