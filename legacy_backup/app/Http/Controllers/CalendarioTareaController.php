<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\CMSUser;
use App\Models\Curso;
use App\Models\Familia;
use Illuminate\Support\Facades\Storage;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Http\Request;

class CalendarioTareaController extends Controller
{

    // Subir archivo
    public function subirArchivo(Request $request)
    {
        $request->validate([
            'tarea_estudiante_id' => 'required|exists:tarea_estudiantes,id',
            'archivo' => 'required|file|max:10240', // 10MB
        ]);

        $tareaEstudiante = TareaEstudiante::findOrFail($request->tarea_estudiante_id);

        if ($tareaEstudiante->archivo) {
            // Eliminar archivo anterior
            Storage::delete($tareaEstudiante->archivo->path);
            $tareaEstudiante->archivo()->delete();
        }

        $path = $request->file('archivo')->store('tareas');

        $tareaEstudiante->archivo()->create([
            'nombre' => $request->file('archivo')->getClientOriginalName(),
            'path' => $path,
        ]);

        return response()->json(['success' => true, 'message' => 'Archivo subido correctamente.']);
    }

    // Descargar archivo
    public function descargarArchivo($tareaEstudianteId)
    {
        $tareaEstudiante = TareaEstudiante::findOrFail($tareaEstudianteId);
        $archivo = $tareaEstudiante->archivo;

        if (!$archivo) {
            abort(404);
        }

        return Storage::download($archivo->path, $archivo->nombre);
    }

    // Eliminar archivo
    public function eliminarArchivo($tareaEstudianteId)
    {
        $tareaEstudiante = TareaEstudiante::findOrFail($tareaEstudianteId);
        $archivo = $tareaEstudiante->archivo;

        if ($archivo) {
            Storage::delete($archivo->path);
            $archivo->delete();
        }

        return response()->json(['success' => true, 'message' => 'Archivo eliminado.']);
    }


    public function index()
    {
        return view('calendario.tareas'); // vista Blade
    }

    public function calendariopadres()
    {



        return view('calendario.padrestareas'); // vista Blade
    }

    public function eventos(Request $request)
    {
        $userId = CRUDBooster::myId();
        $user = CMSUser::with('cmsPrivilege')->find($userId);
        $añoActual = now()->year;
        $persona = Persona::where('cms_users_id', $userId)->first();
        $estudiante = $persona ? Estudiante::where('persona_id', $persona->id)->first() : null;

        // 1. Administrativos: ven todas las tareas
        if ($user && strtolower($user->cmsPrivilege->name) != 'docente') {
            $tareas = Tarea::with([
                'materia.gestionMateria',
                'materia.cgshges',
                'tareaEstudiantes.estudiante'
            ])->whereYear('created_at', $añoActual)->get();

            $agrupadas = $tareas->groupBy(function ($tarea) {
                $materiaNombre = $tarea->materia->gestionMateria->nombre ?? 'Sin materia';
                $grado = $tarea->materia->cgshges->grados->nombre ?? 'Sin grado';
                return $materiaNombre . ' (' . $grado . ')' . '|' . $tarea->fexpiracion;
            });

            $eventos = $agrupadas->map(function ($grupo, $key) {
                [$materiaNombre, $fecha] = explode('|', $key);
                $tareasHtml = $grupo->map(function ($tarea) {
                    return " {$tarea->titulo}</a>";
                })->implode('<br>');

                return [
                    'title' => $materiaNombre,
                    'start' => $fecha,
                    'extendedProps' => [
                        'tareas' => $tareasHtml,
                    ],
                ];
            })->values();

            return response()->json($eventos);
        }

        // 2. Docentes: exactamente igual que tu versión actual
        elseif ($user && strtolower($user->cmsPrivilege->name) === 'docente') {


            $tareas = Tarea::with([
                'materia.gestionMateria',
                'materia.cgshges',
                'tareaEstudiantes.estudiante',
                'docente.persona',
                'estado',
                'bimestre'
            ])
            ->whereYear('created_at', $añoActual)
            ->whereHas('docente.persona', function ($query) use ($userId) {
                $query->where('cms_users_id', $userId);
            })
            ->get();


            $agrupadas = $tareas->groupBy(function ($tarea) {
                $materiaNombre = $tarea->materia->gestionMateria->nombre ?? 'Sin materia';
                $grado = $tarea->materia->cgshges->grados->nombre ?? 'Sin grado';
                return $materiaNombre . ' (' . $grado . ')' . '|' . $tarea->fexpiracion;
            });

            $eventos = $agrupadas->map(function ($grupo, $key) {
                [$materiaNombre, $fecha] = explode('|', $key);
                $tareasHtml = $grupo->map(function ($tarea) {
                    $url = route('docentes.tareas.show', $tarea->id);
                    return "<a href='{$url}' target='_blank' style='text-decoration:none;color:#3366cc;'>• {$tarea->titulo}</a>";
                })->implode('<br>');

                return [
                    'title' => $materiaNombre,
                    'start' => $fecha,
                    'extendedProps' => [
                        'tareas' => $tareasHtml,
                    ],
                ];
            })->values();

            return response()->json($eventos);
        }

        // 3. Estudiantes: con modal flotante y subida de archivo
        elseif ($estudiante) {
            $tareas = Tarea::with([
                'materia.gestionMateria',
                'materia.cgshges',
                'tareaEstudiantes.estudiante',
                'docente.persona',
                'estado',
                'bimestre'
            ])
            ->whereYear('created_at', $añoActual)
            ->whereHas('tareaEstudiantes', function ($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id);
            })
            ->get();

            $agrupadas = $tareas->groupBy(function ($tarea) {
                $materiaNombre = $tarea->materia->gestionMateria->nombre ?? 'Sin materia';
                $grado = $tarea->materia->cgshges->grados->nombre ?? 'Sin grado';
                return $materiaNombre . ' (' . $grado . ')' . '|' . $tarea->fexpiracion;
            });

            $eventos = $agrupadas->map(function ($grupo, $key) use ($estudiante) {
                [$materiaNombre, $fecha] = explode('|', $key);

                $tareasHtml = $grupo->map(function ($tarea) {
                    return "<span style='text-decoration:none;color:#3366cc;' data-tarea-id='{$tarea->id}'>• {$tarea->titulo}</span>";
                })->implode('<br>');

                // Obtener el registro TareaEstudiante correspondiente
                $tareaEstudiante = $grupo->first()->tareaEstudiantes
                    ->where('estudiante_id', $estudiante->id)
                    ->first();





                return [
                    'title' => $materiaNombre,
                    'start' => $fecha,
                    'extendedProps' => [
                        'tareas' => $tareasHtml,
                        'tarea_id' => $grupo->first()->id,
                        'tarea_estudiante_id' => $tareaEstudiante ? $tareaEstudiante->id : null,


                      'estado' => $tareaEstudiante ? $tareaEstudiante->estado : 'pendiente',
                         'descripcion' => $tareaEstudiante ? $tareaEstudiante->tarea->descripcion : '',
                        'fexpiracion' => $grupo->first()->fexpiracion,
                        'archivo' => $grupo->first()->archivo ?? null
                    ],
                ];
            })->values();



            return response()->json($eventos);

        }



        return response()->json([]);
    }


    public function eventospadres(Request $request)
    {
        $userId = CRUDBooster::myId();
        $user = CMSUser::with('cmsPrivilege')->find($userId);
        $añoActual = now()->year;

        // Obtener persona asociada al usuario autenticado
        $persona = Persona::where('cms_users_id', $userId)->first();
        if (!$persona) {
            return redirect()->back()->with('error', 'No cuentas con privilegios para acceder.');
        }

        // Obtener la familia asociada
        $familia = Familia::where(function ($query) use ($persona) {
            $query->where('padre_persona_id', $persona->id)
                  ->orWhere('madre_persona_id', $persona->id)
                  ->orWhere('encargado_persona_id', $persona->id);
        })->first();

        if (!$familia) {
            return redirect()->back()->with('error', 'No cuentas con privilegios para acceder.');
        }

        // Estudiantes asociados por código familiar
        $estudiantes = Estudiante::whereIn(
            'id',
            Familia::where('codigo_familiar', $familia->codigo_familiar)
                   ->pluck('estudiante_id')
        )->with('persona')->get();

        if ($estudiantes->isEmpty()) {
            return response()->json([]);
        }



        $estudiantesIds = $estudiantes->pluck('id')->toArray();


        // Obtener tareas asignadas a los estudiantes de la familia
        $tareas = Tarea::with([
            'materia.gestionMateria',
            'materia.cgshges.grados',
            'tareaEstudiantes.estudiante.persona',
            'docente.persona',
            'estado',
            'bimestre'
        ])
        ->whereYear('created_at', $añoActual)
        ->whereHas('tareaEstudiantes', function ($query) use ($estudiantesIds) {
            $query->whereIn('estudiante_id', $estudiantesIds);
        })
        ->get();


        // Agrupar tareas por estudiante -> curso -> fecha
        $agrupadas = collect();



        foreach ($tareas as $tarea) {

            foreach ($tarea->tareaEstudiantes as $te) {

                // Solo procesar estudiantes que pertenecen a la familia
                if (!in_array($te->estudiante_id, $estudiantesIds)) {
                    continue; // saltar si no es estudiante de la familia
                }

                $estudianteNombre = optional($te->estudiante->persona)->nombres . ' ' .
                                optional($te->estudiante->persona)->apellidos;
                $materia = $tarea->materia->gestionMateria->nombre ?? 'Sin materia';
                $grado = $tarea->materia->cgshges->grados->nombre ?? 'Sin grado';
                $fecha = $tarea->fexpiracion;

                $clave = "{$estudianteNombre}|{$materia}|{$grado}|{$fecha}";


                if (!$agrupadas->has($clave)) {
                    $agrupadas->put($clave, collect());
                }

                $agrupadas[$clave]->push($tarea);
            }

        }

        // Construcción de eventos
        $eventos = $agrupadas->map(function ($grupo, $clave) {
            [$estudianteNombre, $materia, $grado, $fecha] = explode('|', $clave);

            $tareasHtml = $grupo->map(function ($tarea) {
                $materia = $tarea->materia->gestionMateria->nombre ?? 'Sin materia';
                $url = url('admin/tareas/familiaalumnosguia');
                return "<a href='{$url}' target='_blank' style='text-decoration:none;color:#3366cc;'>
                • <span style='text-decoration:underline;font-weight:bold'>{$materia}</span>: {$tarea->titulo}
            </a>";
            })->implode('<br>');

            return [
                'title' => "{$estudianteNombre}  Materia: {$materia}  Grado: { $grado}",
                'start' => $fecha,
                'extendedProps' => [
                    'materia' => $materia,
                    'grado' => $grado,
                    'tareas' => $tareasHtml,
                ],
            ];
        })->values();

        return response()->json($eventos);
    }



}
