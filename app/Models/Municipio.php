<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'tb_municipios';
    
    protected $fillable = [
        'municipio',
        'codigo_postal',
        'departamento_id',
        'estado_id',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

  

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}
