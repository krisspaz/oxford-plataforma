<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialMedico extends Model
{
    use HasFactory;

    protected $table = 'historial_medicos';

    protected $fillable = [

        'estudiante_id',
        'grupo_sanguineo',
        'alergias',
        'enfermedades',
        'medicamentos',
        'medico',
        'telefono_medico',
        'observacion',
        'estado_id',
    ];


    // Definir las relaciones
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
