<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    use HasFactory;

    protected $table = 'convenios';

    protected $fillable = [
        'inscripcion_id',
         'fecha_inicio',
         'fecha_fin',
         'estado'];

    public function inscripcion()
    {
        return $this->belongsTo(Matriculacion::class, 'inscripcion_id');
    }

    public function conveniodetalle()
    {
        return $this->belongsTo(ConvenioDetalle::class, 'id');  // Relación "uno a muchos"
    }

    public function detalles()
    {
        return $this->hasMany(ConvenioDetalle::class, 'convenio_id', 'id');
    }




    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'convenio_id', 'id');
    }

    // Relación con Estudiante a través de Matriculacion
    public function estudiante()
    {
        return $this->hasManyThrough(
            Estudiante::class,
            Matriculacion::class,
            'id',
            'id',
            'inscripcion_id',
            'estudiante_id'
        );
    }
    //este devuleve un solo estudiante
    public function estudiante2()
    {
        return $this->hasOneThrough(
            Estudiante::class,
            Matriculacion::class,
            'id',              // Clave primaria de Matriculacion
            'id',              // Clave primaria de Estudiante
            'inscripcion_id',  // Clave foránea en Convenio
            'estudiante_id'    // Clave foránea en Matriculacion
        );
    }




    public function pagos()
    {
        return $this->hasMany(Pago::class, 'convenio_id');
    }

    public function tieneCuotasPendientes()
    {
        return $this->cuotas()
            ->whereIn('estado', ['pendiente', 'vencida'])
            ->exists();
    }


}
