<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'contratos';

    /**
     * Campos que pueden ser asignados masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'numero_contrato',
        'inscripcion_id',
        'persona_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'archivo',
        'descripcion',
        'ciclo_escolar',
    ];

    /**
     * Relación con el modelo Usuario.
     * Un contrato pertenece a un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
  

    public function inscripcion()
    {
        return $this->belongsTo(Matriculacion::class, 'inscripcion_id', 'id');
    }
     
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Obtener el estado del contrato de manera legible.
     *
     * @return string
     */
    public function getEstadoLabelAttribute()
    {
        switch ($this->estado) {
            case 'activo':
                return 'Activo';
            case 'finalizado':
                return 'Finalizado';
            case 'cancelado':
                return 'Cancelado';
            default:
                return 'Desconocido';
        }
    }
}
