<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre_sucursal',
        'municipio_id',
        'direccion',
        'status_id',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

       

}
