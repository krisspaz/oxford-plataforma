<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    use HasFactory;
    protected $table = 'gestionesacademicas';

    protected $fillable = ['gestion', 'ciclo_escolar',  'resolucion_DIACO',  'resolucion_Ministerial'];

     
    public function sucursales()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
