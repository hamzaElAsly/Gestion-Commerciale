<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailVente extends Model
{
    protected $table = 'detail_ventes';

    protected $fillable = [
        'id_vente',
        'id_produit',
        'nom_produit',
        'quantite',
        'prix_vente',
        'prix_total'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit');
    }
}