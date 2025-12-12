<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilegio extends Model
{
    use HasFactory;
    protected $table = 'cms_privileges';

    protected $fillable = [
        'name',
        'is_superadmin',
        'theme_color'
    ];
}
