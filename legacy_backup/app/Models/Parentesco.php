<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parentesco extends Model
{
    use HasFactory;

    protected $table = 'tb_parentescos';
    protected $fillable = ['parentesco', 'estado_id'];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}
