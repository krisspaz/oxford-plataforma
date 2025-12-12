<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;
    protected $table = 'secciones';
    protected $fillable = [
        'seccion', 'capacidad'
    ];

    public function gestion()
    {
        return $this->belongsTo(gestionesacademicas::class, 'gestion_id');
    }
}
