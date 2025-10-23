<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['user_id', 'nom', 'prenom', 'email', 'telephone', 'adresse', 'date_naissance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comptes()
    {
        return $this->hasMany(Compte::class);
    }
}
