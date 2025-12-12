<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoInscripcion extends Model
{
    use HasFactory;

    protected $table = 'documentosinscripciones';

    protected $fillable = [
        'estudiante_id',
        'tipo_documento',
        'nombre_documento',
        'documento',
        'fexpiracion',
        'estado_id',
        'inscripcion_id',
    ];

    // Definir las relaciones
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function inscripcion()
    {
        return $this->belongsTo(Matriculacion::class);
    }

}
