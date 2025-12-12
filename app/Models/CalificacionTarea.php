<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalificacionTarea extends Model
{
    use HasFactory;

    // Definir la tabla asociada a este modelo (si no sigue la convención de pluralización)
    protected $table = 'tb_calificaciones_tareas';

    // Los atributos que son asignables en masa
    protected $fillable = [
        'tarea_estudiante_id',
        'calificacion',
        'comentarios',
    ];

    /**
     * Relación con el modelo TareaEstudiante
     * Una calificación de tarea pertenece a una tarea asignada a un estudiante.
     */
    public function tareaEstudiante()
    {
        return $this->belongsTo(TareaEstudiante::class, 'tarea_estudiante_id');
    }


    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    public function materia()
    {
        return $this->tarea->materia->gestionMateria;  // Se accede a la materia a través de la tarea
    }

    public function estado()
    {
        return $this->tarea->estado;  // Se accede al estado a través de la tarea
    }

    protected static function booted()
    {
        static::created(function ($calificacion) {
            $tareaEstudiante = $calificacion->tareaEstudiante;
            if ($tareaEstudiante && $tareaEstudiante->estado === 'pendiente') {
                $tareaEstudiante->update(['estado' => 'calificada']);
            }
        });
    }

}
