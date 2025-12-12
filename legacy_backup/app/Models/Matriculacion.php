<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matriculacion extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'inscripciones';

    // Campos asignables de forma masiva
    protected $fillable = [
        'estudiante_id',
        'cgshges_id',
        'paquete_id',
        'fecha_inscripcion',
        'ciclo_escolar',
        'estado_id',
        'cms_users_id'
    ];

    /**
     * Relación con el modelo Estudiante.
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id');
    }

    /**
     * Relación con el modelo CGSHGES.
     */
    public function cgshges()
    {
        return $this->belongsTo(Cgshge::class, 'cgshges_id');
    }

    /**
     * Relación con el modelo Paquete.
     */
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'paquete_id');
    }

    /**
     * Relación con el modelo Estado.
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    /**
     * Relación con el modelo CMSUser (Usuario del sistema).
     */
    public function cmsUser()
    {
        return $this->belongsTo(CMSUser::class, 'cms_users_id');
    }

    public function productosSeleccionados()
    {
        return $this->hasMany(ProductoSeleccionado::class, 'inscripcion_id', 'id');
    }

    public function convenio()
    {
        return $this->hasOne(Convenio::class, 'inscripcion_id', 'id');
    }

}
