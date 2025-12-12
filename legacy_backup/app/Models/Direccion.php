<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'tb_direccion';

    protected $fillable = [
        'municipio_id',
        'direccion',
        'telefono_casa',
        'telefono_mobil',
        'pais_id',
        
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function paisOrigen()
    {
        return $this->belongsTo(Pais::class, 'pais_origen_id');
    }

}
