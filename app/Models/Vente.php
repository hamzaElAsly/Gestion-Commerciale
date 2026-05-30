<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventes';
    protected $primaryKey = 'id_vente';

    protected $fillable = [
        'nom_client',
        'montant_total'
    ];

    public function details() :HasMany
    {
        return $this->hasMany(DetailVente::class, 'id_vente');
    }
}