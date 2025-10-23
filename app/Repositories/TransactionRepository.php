<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository extends BaseRepository
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    // Additional methods
    public function getByCompte($compteId)
    {
        return $this->model->where('compte_id', $compteId)->get();
    }
}
