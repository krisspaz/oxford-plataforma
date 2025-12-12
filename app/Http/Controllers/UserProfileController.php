<?php

namespace App\Http\Controllers;

use crocodicstudio\crudbooster\helpers\CRUDBooster; // Asegúrate de importar CRUDBooster aquí

class UserProfileController extends Controller
{
    public function profile()
    {
        // Obtiene el usuario autenticado en CRUD Booster
        $user = CRUDBooster::me();

        // Retorna la vista con los datos del usuario
        return view('user.profile', compact('user'));
    }
}
