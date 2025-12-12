<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelSucursal extends Model
{
    use HasFactory;

    protected $table = 'pv_niveles_sucursals';

    protected $fillable = [
        'sucursal_id',
        'nivel_id',
        'estado_id',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}
