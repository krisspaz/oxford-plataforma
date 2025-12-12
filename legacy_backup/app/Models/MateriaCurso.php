<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriaCurso extends Model
{
    use HasFactory;

    // Definir la tabla asociada a este modelo (si no sigue la convención de pluralización)
    protected $table = 'materias_cursos';

    // Los atributos que son asignables en masa
    protected $fillable = [
        'docente_id',
        'materia_id',
        'gshges_id',
        'estado_id',


    ];

    /**
     * Relación con el modelo Docente
     * Un registro de materias_cursos pertenece a un docente.
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }






    /**
     * Relación con el modelo Materia
     * Un registro de materias_cursos pertenece a una materia.
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }



    /**
     * Relación con el modelo Curso
     * Un registro de materias_cursos pertenece a un curso.
     */




    public function cgshges()
    {
        return $this->belongsTo(Cgshge::class, 'gshges_id', 'id');
    }





    public function contenidos()
    {
        return $this->hasMany(ContenidoMateria::class, 'materia_id', 'materia_id');
    }

    /**
     * Relación con el modelo Estado
     * Un registro de materias_cursos pertenece a un estado.
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
