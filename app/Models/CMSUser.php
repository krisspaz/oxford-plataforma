<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CMSUser extends Model
{
    use HasFactory;

    protected $table = 'cms_users';

    // Opcionalmente, puedes definir campos que se pueden asignar masivamente
    protected $fillable = ['name', 'photo', 'email', 'password', 'id_cms_privileges',];

    public function cmsPrivilege()
    {
        return $this->belongsTo(CMSPrivilege::class, 'id_cms_privileges');
    }

    public function tareasAsignadas()
    {
        return $this->hasMany(Persona::class, 'cms_users_id', 'id');
    }
}
