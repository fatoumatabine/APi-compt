<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\CompteScope;

class Compte extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new CompteScope);
    }

    protected $fillable = ['numero_compte', 'type_compte', 'devise', 'statut', 'version', 'client_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getSoldeAttribute()
    {
        $depot = $this->transactions()->where('type', 'depot')->sum('montant');
        $retrait = $this->transactions()->where('type', 'retrait')->sum('montant');
        return $depot - $retrait;
    }

    public function getTitulaireAttribute()
    {
        return $this->client->nom . ' ' . $this->client->prenom;
    }

    public function getDateCreationAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }

    public function getMetadataAttribute()
    {
        return [
            'derniereModification' => $this->updated_at,
            'version' => $this->version,
        ];
    }

    public function scopeNumero($query, $numero)
    {
        return $query->where('numero_compte', $numero);
    }

    public function scopeClient($query, $telephone)
    {
        return $query->whereHas('client', function ($q) use ($telephone) {
            $q->where('telephone', $telephone);
        });
    }
}
