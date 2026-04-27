<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $primaryKey = 'id_client';
 
    protected $fillable = [
        'nom',
        'telephone',
        'adresse',
        'date_creation',
    ];
 
    protected $casts = [
        'date_creation' => 'datetime',
    ];
 
    public function historiques()
    {
        return $this->hasMany(Historique::class, 'id_client', 'id_client');
    }
 
    public function getMontantTotalAttribute()
    {
        return $this->historiques()->sum('montant_total');
    }
 
    public function getNombreServicesAttribute()
    {
        return $this->historiques()->count();
    }
 
    public function getDernierServiceAttribute()
    {
        return $this->historiques()->latest('date_service')->first();
    }
}
