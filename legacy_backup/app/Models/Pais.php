<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'tb_paises';
    protected $fillable = ['nombre'];
}
