<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;


    protected $table = 'tb_estados';

    protected $fillable = ['estado'];

    public function familias()
    {
        return $this->hasMany(Familia::class, 'estado_id');
    }

    public function gradoCarreras()
    {
        return $this->hasMany(GradoCarrera::class, 'estado_id');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'estado_id');
    }

    public function jornadaDiaHorarios()
    {
        return $this->hasMany(JornadaDiaHorario::class, 'estado_id');
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'estado_id');
    }

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'estado_id');
    }
}
