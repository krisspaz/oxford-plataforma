<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PvTbgradosTbniveles extends Model
{
    use HasFactory;

    protected $table = 'pv_tbgrados_tbniveles';

    protected $fillable = [
        'grado_id', 'nivel_id', 'estado_id'
    ];

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
