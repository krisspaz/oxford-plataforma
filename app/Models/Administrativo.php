<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrativo extends Model
{
    use HasFactory;

    // Nombre de la tabla asociada
    protected $table = 'administrativos';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'persona_id',
        'estado_id',

        'fotografia_administrativo',
        'cargo_id',

    ];

    /**
     * Relación con el modelo Persona
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Relación con el modelo Estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }


    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }
}
