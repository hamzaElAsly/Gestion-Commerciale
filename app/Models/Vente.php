<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id_vente';

    protected $fillable = [
        'charges',
        'nom_client',
        'montant_total',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'charges'       => 'decimal:2',
    ];

    public function details()
    {
        return $this->hasMany(DetailVente::class, 'id_vente', 'id_vente');
    }

    public function getSousTotalAttribute(): float
    {
        return (float) $this->details->sum('prix_total');
    }
}