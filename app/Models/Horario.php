<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'tb_horarios';
    
    protected $fillable = [
        'inicio',
        'fin',
        'estado_id',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function jornadaDiaHorarios()
    {
        return $this->hasMany(JornadaDiaHorario::class, 'horario_id');
    }
}
