<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionStock extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_stock';

    protected $fillable = [
        'id_produit',
        'type_mouvement',
        'quantite',
        'date_mouvement',
        'description',
        'id_historique',
    ];

    protected $casts = [
        'date_mouvement' => 'datetime',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit', 'id_produit');
    }

    public function historique()
    {
        return $this->belongsTo(Historique::class, 'id_historique', 'id_historique');
    }

    public function getTypeBadgeAttribute(): string
    {
        return $this->type_mouvement === 'ENTREE' ? 'success' : 'danger';
    }

    public function getTypeIconAttribute(): string
    {
        return $this->type_mouvement === 'ENTREE' ? '↑' : '↓';
    }

    public function scopeEntrees($query)
    {
        return $query->where('type_mouvement', 'ENTREE');
    }

    public function scopeSorties($query)
    {
        return $query->where('type_mouvement', 'SORTIE');
    }
}