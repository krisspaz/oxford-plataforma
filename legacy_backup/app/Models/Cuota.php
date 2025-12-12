<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    use HasFactory;

    protected $table = 'cuotas';

    protected $dates = ['fecha_vencimiento'];

    protected $fillable = ['convenio_id',
    'monto_cuota',
    'comentario',
    'baja',
    'productos_seleccionados_id',
    'fecha_vencimiento',
    'estado'];



    public function convenio()
    {
        return $this->belongsTo(Convenio::class, 'convenio_id');  // Relación de "pertenece a" con el modelo Convenio
    }

    public function productoSeleccionado()
    {
        return $this->belongsTo(ProductoSeleccionado::class, 'productos_seleccionados_id');
    }


    public function pagos()
    {
        return $this->belongsToMany(Pago::class, 'pago_cuotas', 'cuota_id', 'pago_id');
    }


}
