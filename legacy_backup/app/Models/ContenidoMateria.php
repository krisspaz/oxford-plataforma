<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContenidoMateria extends Model
{
    use HasFactory;

    protected $table = 'contenido_materias';

    /**
     * Atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'materia_id',
        'titulo',
        'descripcion',
        'bimestre_id',
        'tipo_contenido',
        'archivo',
        'docente_id',
    ];

    /**
     * Relación con la tabla de materias.
     */
   

  

    public function materiaCursos()
    {
        return $this->belongsTo(MateriaCurso::class, 'materia_id');
    }




    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    /**
     * Relación con la tabla de bimestres.
     */
  

    public function bimestre()
    {
        return $this->belongsTo(Bimestre::class, 'bimestre_id');
    }

    /**
     * Relación con la tabla de docentes.
     */
    
    

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }
}
