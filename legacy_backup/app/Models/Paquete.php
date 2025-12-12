<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory;

    protected $table = 'paquetes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'estado_id'
    ];

    public function cgshges()
    {
        return $this->belongsTo(Cgshge::class, 'curso_id', 'curso_id');
    }

    public function detalles()
    {
        return $this->hasMany(PaqueteDetalle::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'paquete_cursos', 'paquete_id', 'curso_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
