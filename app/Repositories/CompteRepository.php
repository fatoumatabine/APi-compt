<?php

namespace App\Repositories;

use App\Models\Compte;

class CompteRepository extends BaseRepository
{
    public function __construct(Compte $model)
    {
        parent::__construct($model);
    }

    // Additional methods specific to Compte
    public function findByNumero($numero)
    {
        return $this->model->where('numero_compte', $numero)->first();
    }

    public function getByClient($clientId)
    {
        return $this->model->where('client_id', $clientId)->get();
    }
}
