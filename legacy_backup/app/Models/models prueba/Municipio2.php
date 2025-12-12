<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{

    protected $table = 'municipios';
    protected $fillable = ['municipio', 'departamento_id', 'status_id'];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
