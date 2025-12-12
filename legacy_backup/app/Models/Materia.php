<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;
    // Definir la tabla asociada a este modelo (si no sigue la convención de pluralización)
    protected $table = 'materias';

    // Los atributos que son asignables en masa
    protected $fillable = [
        'materia_id',
        'cgshe_id',
        'estado_id',
    ];
 
    /**
     * Relación con el modelo Curso
     * Una materia pertenece a un curso.
     */
    public function gestionMateria()
    {
        return $this->belongsTo(GestionMateria::class, 'materia_id', 'id');
          
    }

    public function gestionMaterias()
    {
        return $this->hasMany(GestionMateria::class, 'materia_id', 'id');
          
    }

    public function materiasCursos()
    {
        return $this->hasMany(MateriaCurso::class, 'materia_id');
    }
  
    // Relación con la tabla 'pv_cgshges'
    public function cgshe()
    {
        return $this->belongsTo(Cgshge::class, 'cgshe_id');
    }
  
    // Relación con la tabla 'tb_estados'
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
 
    


    public function cgshges()
    {
        return $this->belongsTo(Cgshge::class, 'cgshe_id');
    }

    public function cuadrosNotas()
    {
        return $this->hasMany(CuadroNota::class);
    }
}
