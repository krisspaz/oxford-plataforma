<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    use HasFactory;

    protected $table = 'tb_telefonos';

    protected $fillable = [
        'telefono',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function sucursals()
    {
        return $this->belongsToMany(Sucursal::class, 'sucursal_telefono');
    }
}
