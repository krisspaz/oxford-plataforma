<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioDetalle extends Model
{
    use HasFactory;

    protected $table = 'convenios_detalles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'convenio_id',
        'productos_seleccionados_id',
        'cantidad_cuotas',
        'monto_total',
    ];

    /**
     * Relationship with the Convenio model.
     */
    public function convenio()
    {
        return $this->belongsTo(Convenio::class, 'id', 'convenio_id');
    }

    /**
     * Relationship with the ProductoSeleccionado model.
     */
    public function productosSeleccionados()
    {
        return $this->belongsTo(ProductoSeleccionado::class, 'productos_seleccionados_id', 'id');
    }
}
