<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SucursalTelefono extends Model
{
    use HasFactory;

    protected $table = 'pv_sucursal_telefonos';

    protected $fillable = [
        'sucursal_id',
        'telefono_id',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function telefono()
    {
        return $this->belongsTo(Telefono::class);
    }
}
