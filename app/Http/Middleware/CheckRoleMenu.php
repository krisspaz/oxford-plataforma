<?php

namespace App\Http\Middleware;

use Closure;
use CRUDBooster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckRoleMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está logueado
        if (!CRUDBooster::myId()) {
            return redirect()->route('getLogin');
        }

        $userRoleId = CRUDBooster::myPrivilegeId();
        $currentPath = '/' . trim($request->path(), '/'); // Asegura que tenga /



        // Buscar el menú por path
        $menu = DB::table('cms_menus')->where('path', $currentPath)->first();


        if (!$menu) {
            return $next($request); // Si no hay menú definido, permitir acceso
        }



        // Verificar si el rol tiene acceso a este menú
        $hasAccess = DB::table('cms_menus_privileges')
            ->where('id_cms_menus', $menu->id)
            ->where('id_cms_privileges', $userRoleId)
            ->exists();

        // dd($menu->id,$userRoleId,$hasAccess);

        if (!$hasAccess) {
            return response()->view('errors.no-access', [
                'message' => 'No tienes acceso a este recurso.'
            ], 403);
        }

        return $next($request);
    }

}
