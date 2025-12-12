<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Padre extends Model
{
    protected $table = 'padres';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'direccion',
        'parentesco_id',
    ];

    // Relación uno a muchos con alumnos (como padre o madre o encargado)
    public function alumnosPadre()
    {
        return $this->hasMany(Alumno::class, 'padre_id');
    }

    public function alumnosMadre()
    {
        return $this->hasMany(Alumno::class, 'madre_id');
    }

    public function alumnosEncargado()
    {
        return $this->hasMany(Alumno::class, 'encargado_id');
    }

    // Relación inversa: muchos a uno con parentesco
    public function parentesco()
    {
        return $this->belongsTo(Parentesco::class, 'parentesco_id');
    }
}
