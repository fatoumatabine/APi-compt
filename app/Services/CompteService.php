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
        // Remove solde if present, as it's computed
        unset($data['solde']);
        return parent::create($data);
    }


}
