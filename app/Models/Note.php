<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $table = 'notes';

    protected $primaryKey = 'id_note';

    protected $fillable = [
        'title',
        'nom_client',
        'description',
    ];
}