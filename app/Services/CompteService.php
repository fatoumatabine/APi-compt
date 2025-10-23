<?php

namespace App\Services;

use App\Repositories\CompteRepository;

class CompteService extends BaseService
{
    public function __construct(CompteRepository $repository)
    {
        parent::__construct($repository);
    }

    // Business logic for Compte
    public function create(array $data)
    {
        // Generate numero_compte if not provided
        if (!isset($data['numero_compte'])) {
            $data['numero_compte'] = 'CPT' . time() . rand(100, 999);
        }
        return parent::create($data);
    }

    public function transfer($fromId, $toId, $amount)
    {
        // Logic for transfer
        $from = $this->find($fromId);
        $to = $this->find($toId);
        if ($from && $to && $from->solde >= $amount) {
            $from->solde -= $amount;
            $to->solde += $amount;
            $from->save();
            $to->save();
            return true;
        }
        return false;
    }
}
