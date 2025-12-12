<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoMetodo extends Model
{
    use HasFactory;

    protected $table = 'pago_metodos';

    protected $fillable = [
        'pago_id',
        'metodo_pago',
        'monto',
        'detalles',
    
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }

    protected $casts = [
        'detalles' => 'array', // Convierte automáticamente el JSON en un array y viceversa
    ];


}
