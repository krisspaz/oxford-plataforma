<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'status';

    // Definir los campos que se pueden llenar masivamente
    protected $fillable = ['status_name'];

    // Puedes definir relaciones si es necesario, por ejemplo:
    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'status_id');
    }

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'status_id');
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'status_id');
    }
}
