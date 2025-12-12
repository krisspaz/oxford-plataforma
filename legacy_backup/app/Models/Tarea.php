<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tb_tareas';

    // Los atributos que son asignables en masa
    protected $fillable = [
        'titulo',
        'bimestre_id',
        'descripcion',
        'punteo',
        'materia_id',
        'docente_id',
        'fexpiracion',
        'tiempo_extra_automatico',
        'estado_id',
    ];

    /**
     * Relación con el modelo Docente
     * Una tarea pertenece a un docente.
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function bimestre()
    {
        return $this->belongsTo(Bimestre::class, 'bimestre_id');
    }

    /**
     * Relación con el modelo Materia
     * Una tarea pertenece a una materia.
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    /**
     * Relación con el modelo Estado
     * Una tarea tiene un estado.
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function tareaEstudiantes()
    {
        return $this->hasMany(TareaEstudiante::class);
    }

    public function gestionMateria()
    {
        return $this->belongsTo(GestionMateria::class, 'materia_id', 'id');
    }
}
