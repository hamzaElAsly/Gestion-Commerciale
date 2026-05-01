<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDevis extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_devis',
        'id_produit',
        'quantite',
        'prix_vente',
        'prix_total',
    ];

    protected $casts = [
        'prix_vente' => 'decimal:2',
        'prix_total' => 'decimal:2',
    ];
    
    public function Devis()
    {
        return $this->belongsTo(Devis::class, 'id_devis', 'id_devis');
    }

    public function produit() 
    {
        return $this->belongsTo(Produit::class, 'id_produit');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            $detail->prix_total = $detail->quantite * $detail->prix_vente;
        });
    }
}
