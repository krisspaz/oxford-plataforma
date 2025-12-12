<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'tb_alumnos';

    protected $fillable = [
        'codigo',
        'carne',
        'nombre',
         'apellidos',
         'genero',
         'cui',
        'fecha_nacimiento',
        'municipio_id',
        'direccion',
        'telefono',
        'estado_id'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
