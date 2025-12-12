<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReciboEmitidoSatBitacora extends Model
{
    use HasFactory;

    protected $table = 'recibossat_emitidas_bitacoras';

    protected $fillable = [
        'pago_id',
        'nit',
        'guid',
        'serie',
        'numero',
        'link',
        'anular',
        'motivo',
        'anulada_en',
        'cms_users_id',
        'created_at',
        'updated_at',
    ];

    public function cmsUser()
    {
        return $this->belongsTo(CMSUser::class, 'cms_users_id', 'id');
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
