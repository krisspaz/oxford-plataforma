<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dia extends Model
{
    use HasFactory;

    protected $table = 'tb_dias';
    
    protected $fillable = [
        'nombre',
        'estado_id',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function jornadaDiaHorarios()
    {
        return $this->hasMany(JornadaDiaHorario::class, 'dia_id');
    }
}
