<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaEstudiante extends Model
{
    use HasFactory;

    // Definir la tabla asociada a este modelo (si no sigue la convención de pluralización)
    protected $table = 'tb_tarea_estudiantes';

    // Los atributos que son asignables en masa
    protected $fillable = [
        'tarea_id',
        'estudiante_id',
        'archivo',
        'fecha_entrega',
        'estado',
    ];
 
    /**
     * Relación con el modelo Tarea
     * Una tarea estudiante está relacionada con una tarea específica.
     */
    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }
 
    /**
     * Relación con el modelo Estudiante
     * Una tarea estudiante está relacionada con un estudiante específico.
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function calificacionesTareas()
    {
        return $this->hasMany(CalificacionTarea::class);
    }

    public function calificacion()
    {
        return $this->hasOne(CalificacionTarea::class, 'tarea_estudiante_id');
    }
}
