<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Historique extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id_historique';

    protected $fillable = [
        'id_client',
        'date_service',
        'charges',
        'montant_total',
        'remarque',
        'statut',
    ];

    protected $casts = [
        'date_service' => 'datetime',
        'montant_total' => 'decimal:2',
        'charges' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function details()
    {
        return $this->hasMany(DetailHistorique::class, 'id_historique', 'id_historique');
    }

    public function mouvementsStock()
    {
        return $this->hasMany(GestionStock::class, 'id_historique', 'id_historique');
    }

    public function recalculerMontant(): void
    {
        $total = $this->details()->sum('prix_total');
        $this->update(['montant_total' => $total]);
    }

    public function scopeParMois($query, int $mois, int $annee)
    {
        return $query->whereMonth('date_service', $mois)
                     ->whereYear('date_service', $annee);
    }

    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_service', [$dateDebut, $dateFin]);
    }

    public function getStatutBadgeAttribute(): string
    {
        return match($this->statut) {
            'en_cours' => 'warning',
            'termine' => 'success',
            'annule' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé',
            default => 'Inconnu',
        };
    }
}