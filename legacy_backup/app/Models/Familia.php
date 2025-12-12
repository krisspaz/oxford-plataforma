<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    use HasFactory;

    protected $table = 'familias';

    protected $fillable = [
        'nombre_familiar',
        'codigo_familiar',
        'padre_persona_id',
        'madre_persona_id',
        'encargado_persona_id',
        'estudiante_id',
        'estado_id',
    ];


    public function muchosestudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id', 'estudiante_id');
    }

    public function estudiantes2()
    {
        return $this->hasMany(Estudiante::class, 'id', 'estudiante_id');
    }


    // Definir las relaciones

    public function padre2()
    {
        return $this->hasOne(Persona::class, 'id', 'padre_persona_id'); // Especificamos las claves
    }

    public function madre2()
    {
        return $this->hasOne(Persona::class, 'id', 'madre_persona_id');
    }

    public function estudiantes3()
    {
        return $this->hasMany(Estudiante::class, 'id', 'estudiante_id');
    }



    public function padre()
    {
        return $this->belongsTo(Persona::class, 'padre_persona_id');
    }

    public function madre()
    {
        return $this->belongsTo(Persona::class, 'madre_persona_id');
    }

    public function encargado()
    {
        return $this->belongsTo(Persona::class, 'encargado_persona_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }



    public function estudiantes()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function eestudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id', 'estudiante_id');
    }


    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function usuario()
    {
        return $this->belongsTo(CMSUser::class, 'cms_users_id');
    }

    public function convenios()
    {
        return $this->hasMany(Convenio::class, 'estudiante_id');
    }

    public function estudiantesenfamilia()
    {
        return $this->hasMany(Familia::class, 'estudiante_id', 'id');
        //en la tabla familia existe el campo estudiante_id
    }

    public static function estudiantesPorCodigo($codigo)
    {
        return self::where('codigo_familiar', $codigo)->with('estudiante.persona')->get();
    }

}
