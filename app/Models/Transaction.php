<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['compte_id', 'type', 'montant', 'description', 'date_transaction'];

    public function compte()
    {
        return $this->belongsTo(Compte::class);
    }
}
