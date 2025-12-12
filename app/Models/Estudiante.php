<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    // Nombre de la tabla asociada
    protected $table = 'estudiantes';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'fotografia_estudiante',
        'persona_id',
        'carnet',
        'cgshges_id',
        'estado_id',
    ];

    /**
     * Relación con el modelo Persona
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }



    /**
     * Relación con el modelo Nivel
     */
    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    /**
     * Relación con el modelo Grado
     */
    public function cgshges()
    {
        return $this->belongsTo(Cgshge::class, 'cgshges_id');
    }



    /**
     * Relación con el modelo Estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function asignacion()
    {
        return $this->belongsTo(Cgshge::class, 'cgshges_id');
    }



    /* public function familia()
{
     return $this->hasOne(Familia::class, 'estudiante_id');
}*/


    public function medicos()
    {
        return $this->hasMany(HistorialMedico::class, 'estudiante_id', 'id');
    }


    public function academicos()
    {
        return $this->hasMany(HistorialAcademico::class, 'estudiante_id', 'id');
    }

    public function familia()
    {
        return $this->belongsTo(Familia::class, 'id', 'estudiante_id');
    }

    public function tareaEstudiantes()
    {
        return $this->hasMany(TareaEstudiante::class, 'estudiante_id', 'id');
    }

    public function cuadrosNotas()
    {
        return $this->hasMany(CuadroNota::class);
    }

    public function gestionMateriasDesdeTareas()
    {
        return GestionMateria::whereIn('id', function ($query) {
            $query->select('materias.materia_id')
                ->from('materias')
                ->join('tb_tareas', 'tb_tareas.materia_id', '=', 'materias.id')
                ->join('tb_tarea_estudiantes', 'tb_tarea_estudiantes.tarea_id', '=', 'tb_tareas.id')
                ->where('tb_tarea_estudiantes.estudiante_id', $this->id);
        })->get();
    }





    public function inscripciones()
    {
        return $this->hasMany(Matriculacion::class, 'estudiante_id', 'id'); // Relación con Inscripcion
    }



    public function convenios()
    {
        return $this->hasManyThrough(
            Convenio::class,    // Modelo de destino
            Matriculacion::class, // Modelo intermedio
            'estudiante_id',      // Clave foránea en la tabla `matriculaciones`
            'inscripcion_id',     // Clave foránea en la tabla `convenios`
            'id',                 // Clave local en la tabla `estudiantes`
            'id'                  // Clave local en la tabla `matriculaciones`
        );
    }


    public function materiasCursos()
    {
        return $this->hasManyThrough(
            MateriaCurso::class,   // Modelo destino
            Cgshge::class,         // Modelo intermedio
            'id',                  // Clave local en Estudiante que se relaciona con Cgshge (cgshges_id en Estudiante)
            'gshges_id',           // Clave en MateriaCurso que se relaciona con Cgshge
            'cgshges_id',          // Clave en Estudiante para buscar en Cgshge
            'id'                   // Clave en Cgshge para buscar en MateriaCurso
        );
    }

    public function materiasCursos2()
    {
        return $this->hasManyThrough(MateriaCurso::class, Materia::class, 'materia_id', 'estudiante_id');
    }

    public function cuotasPendientes()
    {
        return $this->hasManyThrough(
            Cuota::class,
            Convenio::class,
            'inscripcion_id', // FK en Convenio hacia Matriculacion
            'convenio_id',    // FK en Cuota hacia Convenio
            'id',             // clave en Matriculacion
            'id'              // clave en Convenio
        )->where('cuotas.estado', 'Pendiente'); // <== CAMBIO AQUÍ
    }

}
