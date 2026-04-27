<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id_produit';

    protected $fillable = [
        'nom_produit',
        'id_categorie',
        'prix_unitaire',
        'quantite_stock',
        'seuil_alerte',
        'description',
        'date_ajout',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'date_ajout' => 'datetime',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }

    public function detailHistoriques()
    {
        return $this->hasMany(DetailHistorique::class, 'id_produit', 'id_produit');
    }

    public function mouvementsStock()
    {
        return $this->hasMany(GestionStock::class, 'id_produit', 'id_produit');
    }

    public function getStockFaibleAttribute(): bool
    {
        return $this->quantite_stock <= $this->seuil_alerte;
    }

    public function getStockCritiqueAttribute(): bool
    {
        return $this->quantite_stock === 0;
    }

    public function getStatutStockAttribute(): string
    {
        if ($this->quantite_stock === 0) return 'rupture';
        if ($this->quantite_stock <= $this->seuil_alerte) return 'faible';
        return 'normal';
    }

    public function decrementerStock(int $quantite, ?int $idHistorique = null, string $description = ''): bool
    {
        if ($this->quantite_stock < $quantite) {
            return false;
        }

        $this->decrement('quantite_stock', $quantite);

        GestionStock::create([
            'id_produit' => $this->id_produit,
            'type_mouvement' => 'SORTIE',
            'quantite' => $quantite,
            'description' => $description ?: "Utilisation lors d'un service",
            'id_historique' => $idHistorique,
        ]);

        return true;
    }

    public function incrementerStock(int $quantite, string $description = ''): void
    {
        $this->increment('quantite_stock', $quantite);

        GestionStock::create([
            'id_produit' => $this->id_produit,
            'type_mouvement' => 'ENTREE',
            'quantite' => $quantite,
            'description' => $description ?: 'Entrée de stock',
        ]);
    }

    public function scopeStockFaible($query)
    {
        return $query->whereColumn('quantite_stock', '<=', 'seuil_alerte');
    }

    public function scopeEnRupture($query)
    {
        return $query->where('quantite_stock', 0);
    }
}