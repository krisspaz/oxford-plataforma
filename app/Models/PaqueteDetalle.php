<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaqueteDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'paquete_id',
        'nombre',
        'descripcion',
        'precio',
        'tipo_comprobante',
        'tipo_producto',
    ];

    public function paquete()
    {
        return $this->belongsTo(Paquete::class);
    }

    public function productosSeleccionados()
    {
        return $this->hasMany(ProductoSeleccionado::class);
    }

    public function detallesProductos()
    {
        return $this->hasMany(DetalleProducto::class, 'paquete_detalle_id');
    }
}
