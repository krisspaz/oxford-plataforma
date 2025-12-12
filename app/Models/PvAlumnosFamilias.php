<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PvAlumnosFamilias extends Model
{
    use HasFactory;

    protected $table = 'pv_alumnos_familias';
    protected $fillable = [
        'alumno_id',
        'padres_tutores_id',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function padresTutores()
    {
        return $this->belongsTo(PvPadresTutores::class, 'padres_tutores_id');
    }
}
