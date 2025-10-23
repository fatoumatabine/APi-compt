<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository extends BaseRepository
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }

    // Additional methods
    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
}
