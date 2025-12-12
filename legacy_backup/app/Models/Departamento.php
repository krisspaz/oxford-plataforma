<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'tb_departamentos';
    
    protected $fillable = [
        'departamento',
        'estado_id',
    ];

  

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'departamento_id');
    }
}
