<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecibInternooEmitido extends Model
{
    use HasFactory;

    protected $table = 'recibosinternos_emitidos';

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
    ];

    /**
     * Define a relationship to the Pago model.
     */
    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
