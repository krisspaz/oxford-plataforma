<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAcademico extends Model
{
    use HasFactory;

    protected $table = 'historial_academicos';

    protected $fillable = [
     
        'historial_data',
        'establecimiento',
        'estudiante_id',
       
           
    ];


    // Definir las relaciones

    protected $casts = [
        'historial_data' => 'array',
    ];
  
    
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

   
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
