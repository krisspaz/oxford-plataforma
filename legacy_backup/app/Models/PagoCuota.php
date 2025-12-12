<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoCuota extends Model
{
    use HasFactory;

    protected $table = 'pago_cuotas';

    // Campos asignables de forma masiva
    protected $fillable = [
        'pago_id',
        'cuota_id'
       
    ];
}
