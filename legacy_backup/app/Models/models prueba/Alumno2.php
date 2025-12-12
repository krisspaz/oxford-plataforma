<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumnos';

    protected $fillable = [
        'codigo',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'grado',
        'padre_id',
        'madre_id',
        'encargado_id',
    ];

    protected $dates = ['fecha_nacimiento'];

    // Relación muchos a uno con padre
    public function padre()
    {
        return $this->belongsTo(Padre::class, 'padre_id');
    }

    // Relación muchos a uno con madre
    public function madre()
    {
        return $this->belongsTo(Padre::class, 'madre_id');
    }

    // Relación muchos a uno con encargado
    public function encargado()
    {
        return $this->belongsTo(Padre::class, 'encargado_id');
    }
}
