<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    use HasFactory;

    protected $table = 'niveles';

    protected $fillable = ['nivel', 'estado_id', 'gestion_id'];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function gestion()
    {
        return $this->belongsTo(gestionesacademicas::class, 'gestion_id');
    }

    public function costos()
    {
        return $this->hasMany(CostosNiveles::class, 'nivel_id');
    }
}
