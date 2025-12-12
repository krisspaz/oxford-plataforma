<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encargado extends Model
{
    use HasFactory;

    protected $table = 'tb_encargados';

    protected $fillable = [
        'parentesco_id',
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

    public function parentesco()
    {
        return $this->belongsTo(Parentesco::class, 'parentesco_id');
    }
}
