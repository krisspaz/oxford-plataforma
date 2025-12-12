<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuadroNota extends Model
{
    use HasFactory;

    // Definir la tabla asociada a este modelo (si no sigue la convención de pluralización)
    protected $table = 'tb_cuadro_notas';

    // Los atributos que son asignables en masa
    protected $fillable = [
        'estudiante_id',
        'materia_id',
        'bimestre',
        'nota_final',
        'nota_acumulada',
        'ciclo_escolar',
        'cierre',
    ];

    /**
     * Relación con el modelo Estudiante
     * Un cuadro de notas pertenece a un estudiante.
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function bimestres()
    {
        return $this->belongsTo(Bimestre::class, 'bimestre');
    }


    /**
     * Relación con el modelo Materia
     * Un cuadro de notas pertenece a una materia.
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }


}
