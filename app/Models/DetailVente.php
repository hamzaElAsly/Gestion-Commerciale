<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailVente extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_detail';
    protected $fillable = [
        'id_vente',
        'id_produit',
        'nom_produit',
        'quantite',
        'prix_vente',
        'prix_total',
    ];

    protected $casts = [
        'prix_vente' => 'decimal:2',
        'prix_total' => 'decimal:2',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class, 'id_vente', 'id_vente');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit', 'id_produit');
    }
}