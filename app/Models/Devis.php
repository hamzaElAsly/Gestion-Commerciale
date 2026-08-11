<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devis extends Model
{

    use HasFactory, SoftDeletes;
    
    protected $primaryKey = 'id_devis';

    protected $fillable = [
        'titre',
        'id_devis',
        'nom_client',
        'tva',
        'montant_total',
    ];
    protected $casts = [
        'tva' => 'decimal:2',
        'montant_total' => 'decimal:2',
    ];


    public function details() {
        return $this->hasMany(DetailDevis::class, 'id_devis');
    }

    public function getTotalHtAttribute(): float
    {
        return (float) $this->details->sum('prix_total');
    }

    public function getMontantTvaAttribute(): float
    {
        return round($this->total_ht * (float) $this->tva / 100, 2);
    }
}
