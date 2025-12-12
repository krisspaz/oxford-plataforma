<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bimestre extends Model
{
    use HasFactory;

    protected $table = 'bimestres';

    protected $fillable = [
        'nombre',
        'ciclo_escolar',
        'fecha_inicio',
        'punteo_maximo',
          'porcentaje',
        'fecha_culminacion',
    ];


    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'ciclo_escolar', 'id');
    }


    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'bimestre_id', 'id');
    }

    public function scopeActual($query)
    {
        $hoy = Carbon::now();
        return $query->where('fecha_culminacion', '>=', $hoy)
                     ->orderBy('fecha_inicio', 'asc'); // Asegúrate de obtener el más próximo
    }
}
