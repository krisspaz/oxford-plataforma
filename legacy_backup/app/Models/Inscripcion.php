<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;
    protected $table = 'inscripciones';

    protected $fillable = [
        'estudiante_id',
        'cgshges_id',
        'paquete_id',
        'fecha_inscripcion',
        'estado_id',
        'cms_users_id',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'paquete_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'cms_users_id');
    }

    public function convenio()
    {
        return $this->belongsTo(User::class, 'inscripcion_id', 'id');
    }

    public function productosSeleccionados()
    {
        return $this->hasMany(ProductoSeleccionado::class);
    }


}
