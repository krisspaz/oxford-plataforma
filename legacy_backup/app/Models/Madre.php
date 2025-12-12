<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Madre extends Model
{
    use HasFactory;

    protected $table = 'tb_madres';
    protected $fillable = [
        'nombre',
        'apellido',
        'identificacion_documentos_id',
        'num_documento',
        'fecha_nacimiento',
        'profesion',
        'telefono',
        'municipio_id',
        'direccion'
    ];

    public function identificacionDocumento()
    {
        return $this->belongsTo(IdentificacionDocumento::class, 'identificacion_documentos_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }
}
