<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PvPadresTutores extends Model
{
    use HasFactory;

    protected $table = 'pv_padres_tutores';

    protected $fillable = [
        'codigofamiliar',
        'padre_id',
        'madre_id',
        'encargado_id',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $year = date('Y');
            $month = date('m');
            $lastRecord = self::whereYear('created_at', $year)
                              ->whereMonth('created_at', $month)
                              ->latest('id')
                              ->first();
            $lastCode = $lastRecord ? intval(substr($lastRecord->codigofamiliar, 8, 5)) : 0;
            $newCode = str_pad($lastCode + 1, 5, '0', STR_PAD_LEFT);
            $model->codigofamiliar = $year . '-' . $month . '-' . $newCode; // Ajusta el último segmento según tus necesidades
        });
    }

    public function padre()
    {
        return $this->belongsTo(Padre::class, 'padre_id');
    }

    public function madre()
    {
        return $this->belongsTo(Madre::class, 'madre_id');
    }

    public function encargado()
    {
        return $this->belongsTo(Encargado::class, 'encargado_id');
    }
}
