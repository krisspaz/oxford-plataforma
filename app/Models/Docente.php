<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    // Nombre de la tabla asociada
    protected $table = 'docentes';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'fotografia',
        'persona_id',
        'especialidad',
        'cedula',
        'estado_id',
    ];

    /**
     * Relación con el modelo Persona
     */
    public function persona()
    {
        return $this->belongsTo(\App\Models\Persona::class, 'persona_id');
    }

    /**
     * Relación con el modelo Estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    

    public function materiasCursos()
    {
        return $this->hasMany(MateriaCurso::class, 'docente_id'); // Relación con MateriaCurso
    }

}
