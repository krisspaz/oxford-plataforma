<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cgshge extends Model
{
    use HasFactory;
    protected $table = 'pv_cgshges';

    protected $fillable = [
        'gestion_id',
        'nivel_id',
        'curso_id',
        'grado_id',
        'seccion_id',
        'jornada_id',
        'estado_id',
        'grado2_id',
    ];


    public function gestiones()
    {
        return $this->belongsTo(Gestion::class, 'gestion_id');
    }

    
    public function niveles()
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    public function cursos()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }
    
    public function grados()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function secciones()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function pv_jornada_dia_horarios()
    {
        return $this->belongsTo(JornadaDiaHorario::class, 'jornada_id');
    }

    public function jornadas()
    {
        return $this->belongsTo(JornadaDiaHorario::class, 'jornada_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function materiasCursos()
    {
        return $this->hasMany(MateriaCurso::class, 'grado_id', 'grado_id')
                    ->where('seccion_id', $this->seccion_id)
                    ->where('jornada_id', $this->jornada_id)
                    ->where('curso_id', $this->curso_id);
    }
    
    public function materiasCursosRelacionados()
    {
        return $this->hasMany(MateriaCurso::class, 'gshges_id', 'id');
    }

    public function materias()
    {
        return $this->hasMany(Materia::class, 'cgshe_id');
    }





}
