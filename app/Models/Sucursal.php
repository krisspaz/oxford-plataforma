<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'tb_sucursals';

    protected $fillable = [
        'nombre_sucursal', 'municipio_id', 'direccion', 'estado_id'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function telefonos()
    {
        return $this->belongsToMany(Telefono::class, 'sucursal_telefono');
    }
}
