<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionMateria extends Model
{
    use HasFactory;

    protected $table = 'gestion_materias'; // Nombre de la tabla

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
    ];
}
