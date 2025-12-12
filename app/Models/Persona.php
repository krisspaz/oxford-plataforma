<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    // Nombre de la tabla asociada
    protected $table = 'personas';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'parentesco_id',
        'nombres',
        'apellidos',
        'genero',
        'profesion',

        'estado_civil',
        'apellido_casada',

        'identificacion_documentos_id',
        'num_documento',
        'fecha_nacimiento',
        'email',
        'telefono',
        'direccion',
        'fecha_defuncion',
        'cms_users_id'
    ];

    protected $casts = [
    'fecha_nacimiento' => 'date',
    'fecha_defuncion' => 'date', // si aplica
];

    /**
     * Relación con la tabla `tb_parentescos`
     */
    public function parentesco()
    {
        return $this->belongsTo(Parentesco::class, 'parentesco_id');
    }

    /**
     * Relación con la tabla `tb_identificacion_documentos`
     */
    public function identificacionDocumento()
    {
        return $this->belongsTo(IdentificacionDocumento::class, 'identificacion_documentos_id');
    }

    /**
     * Relación con la tabla `cms_users`
     */
    public function cmsUser()
    {
        return $this->belongsTo(CMSUser::class, 'cms_users_id');
    }
}
