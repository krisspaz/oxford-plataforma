<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoSeleccionado extends Model
{
   
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'productos_seleccionados';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'inscripcion_id',
        'detalle_id',
        'precio',
        
    ];

    /**
     * Relación con el modelo Inscripcion.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
  

    public function inscripcion()
    {
        return $this->belongsTo(Matriculacion::class, 'inscripcion_id', 'id');
    }
    
    /**
     * Relación con el modelo PaqueteDetalle.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
   
    public function detalle()
    {
        return $this->belongsTo(PaqueteDetalle::class, 'detalle_id', 'id');
    }
}
