<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    use HasFactory;

    protected $fillable = ['numero_compte', 'solde', 'type_compte', 'client_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
