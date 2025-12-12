<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleProducto extends Model
{
    use HasFactory;

    protected $table = 'detalle_productos';

    protected $fillable = [
        'paquete_detalle_id',
        'producto_id',
        'cantidad',
    ];

    /**
     * Relación con PaqueteDetalle.
     */
    public function paqueteDetalle()
    {
        return $this->belongsTo(PaqueteDetalle::class, 'paquete_detalle_id');
    }

    /**
     * Relación con Producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }


}
