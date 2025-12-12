<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PvEstudianteContrato;
use App\Models\CMSUser;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class PvEstudianteContratoController extends Controller
{
    /**
     * Mostrar una lista de los contratos de estudiantes.
     */
    public function index()
    {

        $contratos = null; // Inicializamos la variable

        if (CRUDBooster::myId()) {
            $user = CMSUser::find(CRUDBooster::myId()); // Obtén el usuario actual

            if ($user && in_array(strtolower($user->cmsPrivilege->name), ['administrativo', 'secretaria', 'super administrator'])) {
                // Si el usuario es ADMINISTRATIVO o SECRETARIA
                $contratos = PvEstudianteContrato::with(['estudiante', 'contrato'])->get();
            } elseif ($user) {
                // Si es otro tipo de usuario, aplicar filtro por relación con la familia
                $contratos = PvEstudianteContrato::with(['estudiante', 'contrato.inscripcion'])
                    ->whereHas('estudiante.familia', function ($query) {
                        $query->whereHas('padre', function ($q) {
                            $q->where('cms_users_id', CRUDBooster::myId());
                        })
                        ->orWhereHas('madre', function ($q) {
                            $q->where('cms_users_id', CRUDBooster::myId());
                        })
                        ->orWhereHas('encargado', function ($q) {
                            $q->where('cms_users_id', CRUDBooster::myId());
                        });
                    })
                    ->get();

                return view('estudiantes.contratos.padrecontrato', compact('contratos'));
            }
        }



        return view('estudiantes.contratos.index', compact('contratos'));



    }





    public function uploadSignedContract(Request $request, $id)
    {
        $request->validate([
            'contrato_firmado' => 'required|file|mimes:pdf|max:2048',
        ]);

        $contrato = PvEstudianteContrato::findOrFail($id);

        if ($request->hasFile('contrato_firmado')) {
            $file = $request->file('contrato_firmado');
            $path = $file->store('contratos_firmados', 'public'); // Guardar en carpeta 'public/contratos_firmados'

            // Actualizar la columna contrato_firmado con la ruta del archivo
            $contrato->contrato_firmado = $path;
            $contrato->estado = 'Vigente';  // Cambiar estado si es necesario
            $contrato->save();

            return redirect()->route('estudiante_contratos.index')->with('success', 'Contrato firmado subido exitosamente.');
        }

        return back()->withErrors('Hubo un error al subir el archivo.');
    }
}
