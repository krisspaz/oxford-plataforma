<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    use HasFactory;

    protected $table = 'tb_grados';

    protected $fillable = ['nombre',  'estado_id'];

   

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function gradoCarreras()
    {
        return $this->hasMany(GradoCarrera::class, 'grado_id');
    }
}
