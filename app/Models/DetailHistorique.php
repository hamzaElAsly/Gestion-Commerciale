<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailHistorique extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_historique',
        'id_produit',
        'quantite_utilisee',
        'prix_vente',
        'prix_total',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'prix_total' => 'decimal:2',
    ];

    public function historique()
    {
        return $this->belongsTo(Historique::class, 'id_historique', 'id_historique');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit', 'id_produit');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            $detail->prix_total = $detail->quantite_utilisee * $detail->prix_unitaire;
        });
    }
}