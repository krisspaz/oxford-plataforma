<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostosNiveles extends Model
{
    use HasFactory;

  

    protected $table = 'tb_costos_niveles';

    protected $fillable = [
        'nivel_id',
        'Inscripcion',
        'Mensualidad',
        'Ciclo',
        'Gestion_id',
    ];

    // Relaciones
   

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'Gestion_id');
    }
}
