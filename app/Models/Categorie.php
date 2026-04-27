<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorie extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id_categorie';

    protected $fillable = [
        'nom_categorie',
        'description',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class, 'id_categorie', 'id_categorie');
    }

    public function getNombreProduitsAttribute()
    {
        return $this->produits()->count();
    }
}