<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturasEmitida extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'facturas_emitidas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pago_id',
        'nit',
        'guid',
        'serie',
        'numero',
        'link',
        'anular',
        'motivo',
        
    ];

    /**
     * Define a relationship to the Pago model.
     */
    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }

    


}
