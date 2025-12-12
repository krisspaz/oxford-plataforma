<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    protected $fillable = ['curso'];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    public function gestion()
    {
        return $this->belongsTo(gestionesacademicas::class, 'gestion_id');
    }

    public function grados()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function paquetes()
    {
        return $this->belongsToMany(Paquete::class, 'paquete_cursos', 'curso_id', 'paquete_id');
    }

    public function materias()
    {
        return $this->hasMany(Materia::class, 'curso_id');
    }
}
