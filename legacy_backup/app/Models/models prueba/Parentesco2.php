<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parentesco extends Model
{
    // Define la tabla que se usará por este modelo
    protected $table = 'parentescos';

    // Los atributos que se pueden asignar en masa
    protected $fillable = ['parentesco'];

    // Relación uno a muchos con la tabla padres
    public function padres()
    {
        return $this->hasMany(Padre::class, 'parentesco_id');
    }
}
