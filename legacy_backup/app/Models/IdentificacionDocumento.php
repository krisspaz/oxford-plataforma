<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentificacionDocumento extends Model
{
    use HasFactory;

    protected $table = 'tb_identificacion_documentos';

    protected $fillable = ['nombre', 'descripcion', 'max_digitos'];
}
