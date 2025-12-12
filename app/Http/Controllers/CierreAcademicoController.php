<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Estados;

class CierreAcademicoController extends Controller
{
    /**
     * Muestra las gestiones activas.
     */
    public function index()
    {
        $ngestiones = DB::table('gestionesacademicas')
            ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
            ->where(function ($query) {
                $query->where('tb_estados.estado', 'Activo')
                      ->orWhere('tb_estados.estado', 'ACTIVO');
            })
            ->select('gestionesacademicas.*')
            ->get();

        return view('cierre_academico.index', compact('ngestiones'));
    }

    /**
     * Mostrar formulario de cierre académico.
     */
    public function create()
    {
        $gestiones = DB::table('gestionesacademicas')
            ->join('tb_estados', 'gestionesacademicas.estado_id', '=', 'tb_estados.id')
            ->where(function ($query) {
                $query->where('tb_estados.estado', 'Activo')
                      ->orWhere('tb_estados.estado', 'ACTIVO');
            })
            ->select('gestionesacademicas.*')
            ->get();

        return view('cierre_academico.create', compact('gestiones'));
    }

    /**
     * Procesar el cierre académico y migración.
     */
    public function store(Request $request)
    {
        $request->validate([
            'gestion_id' => 'required|exists:gestionesacademicas,id',
            'migrar' => 'nullable|boolean',
            'migrar_a' => 'nullable|exists:gestionesacademicas,id'
        ]);

        DB::beginTransaction();

        try {
            $gestionId = $request->gestion_id;
            $migrar = $request->has('migrar');
            $migrarA = $request->migrar_a;

            // 🔹 Obtener gestión actual
            $gestion = DB::table('gestionesacademicas')->where('id', $gestionId)->first();
            if (!$gestion) {
                return back()->with('error', 'La gestión académica no existe.');
            }

            // 🔹 Estado "Cerrado"
            $estadoCerrado = DB::table('tb_estados')->where('estado', 'Cerrado')->first();
            if (!$estadoCerrado) {
                return back()->with('error', 'No se encontró el estado "Cerrado" en la tabla de estados.');
            }

            // 🔹 Actualizar gestión actual a "Cerrado"
            DB::table('gestionesacademicas')
                ->where('id', $gestionId)
                ->update(['estado_id' => $estadoCerrado->id]);

            // 🔹 Si se desea migrar la estructura académica
            if ($migrar && $migrarA) {

                // Obtener estructura actual
                $estructuraActual = DB::table('pv_cgshges')
                    ->where('gestion_id', $gestionId)
                    ->get();

                if ($estructuraActual->isEmpty()) {
                    return back()->with('error', 'No se encontró estructura académica para migrar.');
                }

                // Guardar los nuevos IDs para relacionar viejos con nuevos
                $idMapCgsh = [];

                foreach ($estructuraActual as $item) {
                    $nuevoId = DB::table('pv_cgshges')->insertGetId([
                        'gestion_id' => $migrarA,
                        'nivel_id' => $item->nivel_id,
                        'curso_id' => $item->curso_id,
                        'grado_id' => $item->grado_id,
                        'seccion_id' => $item->seccion_id,
                        'jornada_id' => $item->jornada_id,
                        'estado_id' => $item->estado_id,
                        'grado2_id' => $item->grado2_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $idMapCgsh[$item->id] = $nuevoId; // viejo → nuevo
                }

                // 🔸 Obtener estados Activo/Inactivo
                $estadoActivo = DB::table('tb_estados')->where('estado', 'Activo')->value('id') ?? 1;
                $estadoInactivo = DB::table('tb_estados')->where('estado', 'Inactivo')->value('id') ?? 2;

                // 🔸 Migrar MATERIAS
                $materias = DB::table('materias')
                    ->whereIn('cgshe_id', array_keys($idMapCgsh))
                    ->get();

                if ($materias->isNotEmpty()) {
                    // Marcar materias viejas como inactivas
                    DB::table('materias')
                        ->whereIn('cgshe_id', array_keys($idMapCgsh))
                        ->update(['estado_id' => $estadoInactivo]);

                    $idMapMaterias = [];

                    foreach ($materias as $m) {
                        $nuevoId = DB::table('materias')->insertGetId([
                            'materia_id' => $m->materia_id,
                            'cgshe_id' => $idMapCgsh[$m->cgshe_id],
                            'estado_id' => $estadoActivo,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $idMapMaterias[$m->id] = $nuevoId;
                    }

                    // 🔸 Migrar MATERIAS_CURSOS
                    $materiasCursos = DB::table('materias_cursos')
                        ->whereIn('materia_id', array_keys($idMapMaterias))
                        ->get();

                    if ($materiasCursos->isNotEmpty()) {
                        // Inactivar las viejas
                        DB::table('materias_cursos')
                            ->whereIn('materia_id', array_keys($idMapMaterias))
                            ->update(['estado_id' => $estadoInactivo]);

                        foreach ($materiasCursos as $mc) {
                            DB::table('materias_cursos')->insert([
                                'docente_id' => $mc->docente_id,
                                'materia_id' => $idMapMaterias[$mc->materia_id],
                                'gshges_id' => $idMapCgsh[$mc->gshges_id] ?? null,
                                'estado_id' => $estadoActivo,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            $mensaje = $migrar
                ? '✅ La gestión fue cerrada y toda la estructura académica (materias y cursos) fue migrada correctamente.'
                : '✅ La gestión fue cerrada correctamente.';

            return redirect()->back()->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Ocurrió un error durante el cierre: ' . $e->getMessage());
        }
    }
}
