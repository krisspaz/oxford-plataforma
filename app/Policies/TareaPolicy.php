<?php

namespace App\Policies;

use App\Models\Tarea;
use App\Models\User;

class TareaPolicy
{
    /**
     * Verificar si el usuario puede subir un archivo para esta tarea.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Tarea $tarea
     * @return bool
     */
    public function upload(User $user, Tarea $tarea)
    {
        // Lógica para verificar si el usuario tiene permisos para subir el archivo
        return $user->id === $tarea->id; // Este es solo un ejemplo, ajústalo según tus necesidades
    }
}
