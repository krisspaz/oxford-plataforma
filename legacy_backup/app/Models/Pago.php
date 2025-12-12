<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'convenio_id',
        'tipo_pago',
        'monto',
        'exonerar',
        'fecha_pago'
    ];

    // Relación con el modelo Convenio
    public function convenio()
    {
        return $this->belongsTo(Convenio::class);
    }

    // Relación muchos a muchos con Cuotas
    public function cuotas()
    {
        return $this->belongsToMany(Cuota::class, 'pago_cuotas');
    }

    // Relación uno a muchos con PagoMetodo
    public function pagoMetodos()
    {
        return $this->hasMany(PagoMetodo::class);
    }



    public function facturaEmitida()
    {
        return $this->hasOne(FacturasEmitida::class);
    }



    public function reciboEmitido()
    {

        return $this->hasOne(ReciboEmitido::class, 'pago_id');

    }


    public function recibointernoEmitido()
    {

        return $this->hasOne(RecibInternooEmitido::class);


    }







}
