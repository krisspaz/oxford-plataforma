<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JornadaDiaHorario extends Model
{
    use HasFactory;

    protected $table = 'pv_jornada_dia_horarios';
    
    protected $fillable = [
        'jornada_id',
        'dia_id',
        'horario_id',
        'estado_id',
    ];

    public function jornada()
    {
        return $this->belongsTo(Jornada::class, 'jornada_id');
    }

    public function dia()
    {
        return $this->belongsTo(Dia::class, 'dia_id');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
