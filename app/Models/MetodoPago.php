<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    use HasFactory;

    protected $table = 'pago_metodos';

    // Campos asignables de forma masiva
    protected $fillable = [
        'pago_id',
        'metodo_pago',
        'monto'
       
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }

}
