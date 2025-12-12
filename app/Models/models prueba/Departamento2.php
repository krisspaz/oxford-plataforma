<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';
    protected $fillable = ['departamento', 'status_id'];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }

    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
   

}
