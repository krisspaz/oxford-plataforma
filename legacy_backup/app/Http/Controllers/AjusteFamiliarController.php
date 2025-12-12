<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use App\Models\Persona;
use App\Models\Estudiante;
use App\Models\Estado;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AjusteFamiliarController extends Controller
{

    public function index()
    {
        $familias = Familia::with(['padre', 'madre', 'encargado', 'estudiante', 'estado'])->get();
        return view('ajuste_familiar.index', compact('familias'));
    }

    public function create()
    {
        $personas = Persona::all();
        $estudiantes = Estudiante::all();
        $estados = Estado::all();

        $año = date('Y');

        // Calcular el correlativo (puedes ajustar esto según tus necesidades, aquí simplemente cuenta el número total de registros)
        $correlativo = str_pad(Familia::count() + 1, 5, '0', STR_PAD_LEFT);

        // Crear un placeholder temporal para el ID, que será reemplazado después de crear el registro
        $codigoFamiliarTemporal = "F-{$año}-XXXX{$correlativo}";

        return view('ajuste_familiar.create', compact('personas', 'estudiantes', 'estados', 'codigoFamiliarTemporal'));
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'nombre_familiar' => 'required|string|max:255',
            'padre_persona_id' => 'nullable|exists:personas,id',
            'madre_persona_id' => 'nullable|exists:personas,id',
            'encargado_persona_id' => 'nullable|exists:personas,id',
            'estudiante_id' => 'nullable|exists:estudiantes,id',
            'estado_id' => 'required|exists:tb_estados,id',
        ]);





        $this->crearfamilia(
            $request->nombre_familiar,
            $request->padre_persona_id,
            $request->madre_persona_id,
            $request->encargado_persona_id,
            $request->estudiante_id,
            $request->estado_id
        );


        return redirect()->route('ajuste-familiar.index')->with('success', 'Familia registrada correctamente.');
    }

    public function edit(Familia $ajuste_familiar)
    {
        $personas = Persona::all();
        $estudiantes = Estudiante::all();
        $estados = Estado::all();

        // Todos los estudiantes que tienen el mismo código familiar
        $estudiantesFamilia = Familia::estudiantesPorCodigo($ajuste_familiar->codigo_familiar);

        return view('ajuste_familiar.edit', compact(
            'ajuste_familiar',
            'personas',
            'estudiantes',
            'estados',
            'estudiantesFamilia'
        ));
    }


    public function update(Request $request, Familia $ajuste_familiar)
    {
        $request->validate([
            'nombre_familiar'     => 'required|string|max:255',
            'codigo_familiar'     => 'required|string|max:100',
            'padre_persona_id'    => 'nullable|exists:personas,id',
            'madre_persona_id'    => 'nullable|exists:personas,id',
            'encargado_persona_id'=> 'nullable|exists:personas,id',
            'estado_id'           => 'required|exists:tb_estados,id',
            // no validamos estudiantes_ids como array porque puede llegar como string "1,2,3"
        ]);

        // --- Normalizar el input estudiantes_ids ---
        $raw = $request->input('estudiantes_ids', ''); // puede ser "1,2,3" o ""
        $seleccionados = [];

        if (is_string($raw)) {
            $raw = trim($raw);
            if ($raw !== '') {
                $seleccionados = array_filter(explode(',', $raw), function ($v) { return $v !== '' && is_numeric($v); });
                $seleccionados = array_map('intval', $seleccionados);
            }
        } elseif (is_array($raw)) {
            $seleccionados = array_map('intval', array_filter($raw));
        }

        // Códigos anterior y nuevo
        $codigoAnterior = $ajuste_familiar->codigo_familiar;
        $codigoNuevo = $request->input('codigo_familiar', $codigoAnterior);

        DB::transaction(function () use ($request, $ajuste_familiar, $codigoAnterior, $codigoNuevo, $seleccionados) {

            // 0) Actualizar datos del registro principal de la familia (el $ajuste_familiar)
            $ajuste_familiar->update([
                'nombre_familiar' => $request->nombre_familiar,
                'codigo_familiar' => $codigoNuevo,
                'padre_persona_id'=> $request->padre_persona_id,
                'madre_persona_id'=> $request->madre_persona_id,
                'encargado_persona_id' => $request->encargado_persona_id,
                'estado_id'       => $request->estado_id,
            ]);

            // 1) Borrar placeholder(s) con estudiante_id IS NULL del código anterior
            \App\Models\Familia::where('codigo_familiar', $codigoAnterior)
                ->whereNull('estudiante_id')
                ->delete();

            // 2) Obtener IDs actuales (existentes) para el código anterior
            $actuales = \App\Models\Familia::where('codigo_familiar', $codigoAnterior)
                ->pluck('estudiante_id')
                ->filter() // elimina null por si queda alguno
                ->map(fn ($v) => (int)$v)
                ->toArray();

            // 3) Determinar a eliminar y a agregar en base a la selección del usuario
            $aEliminar = array_diff($actuales, $seleccionados);   // estaban antes y ahora no -> borrar
            $aAgregar  = array_diff($seleccionados, $actuales);   // nuevos seleccionados -> crear

            // 4) Eliminar únicamente las filas de los estudiantes desmarcados (para el códigoAnterior)
            if (!empty($aEliminar)) {
                \App\Models\Familia::where('codigo_familiar', $codigoAnterior)
                    ->whereIn('estudiante_id', $aEliminar)
                    ->delete();
            }

            // 5) Crear nuevas filas para estudiantes agregados con el código nuevo
            foreach ($aAgregar as $estudianteId) {
                \App\Models\Familia::create([
                    'nombre_familiar' => $request->nombre_familiar,
                    'codigo_familiar' => $codigoNuevo,
                    'padre_persona_id'=> $request->padre_persona_id,
                    'madre_persona_id'=> $request->madre_persona_id,
                    'encargado_persona_id' => $request->encargado_persona_id,
                    'estado_id'       => $request->estado_id,
                    'estudiante_id'   => $estudianteId,
                ]);
            }

            // 6) Para los estudiantes que permanecen (intersección), actualizar sus filas para reflejar cambios (p.ej. cambio de código)
            $permanecen = array_intersect($actuales, $seleccionados);
            if (!empty($permanecen)) {
                \App\Models\Familia::whereIn('estudiante_id', $permanecen)
                    ->where('codigo_familiar', $codigoAnterior)
                    ->update([
                        'nombre_familiar' => $request->nombre_familiar,
                        'codigo_familiar' => $codigoNuevo,
                        'padre_persona_id'=> $request->padre_persona_id,
                        'madre_persona_id'=> $request->madre_persona_id,
                        'encargado_persona_id' => $request->encargado_persona_id,
                        'estado_id'       => $request->estado_id,
                    ]);
            }

            // 7) Si el código cambió y había filas con el mismo códigoAnterior pero sin estudiante_id (ya las borramos),
            //    y además quieres actualizar cualquier fila restante con codigoAnterior para pasar a codigoNuevo,
            //    puedes ejecutar también esta línea (opcional):
            \App\Models\Familia::where('codigo_familiar', $codigoAnterior)
                ->update([
                    'codigo_familiar' => $codigoNuevo,
                    'nombre_familiar' => $request->nombre_familiar,
                    'padre_persona_id'=> $request->padre_persona_id,
                    'madre_persona_id'=> $request->madre_persona_id,
                    'encargado_persona_id' => $request->encargado_persona_id,
                    'estado_id'       => $request->estado_id,
                ]);
            // Nota: la línea anterior fusiona/renombra las filas restantes del código anterior a codigoNuevo.
            // Si no quieres esto, bórrala o coméntala.
        });

        return redirect()->route('ajuste-familiar.index')->with('success', 'Familia actualizada correctamente.');
    }







    public function show(Familia $ajuste_familiar)
    {
        $ajuste_familiar->load(['padre', 'madre', 'encargado', 'estudiante', 'estado']);
        return view('ajuste_familiar.show', compact('ajuste_familiar'));
    }

    public function destroy(Familia $ajuste_familiar)
    {
        $ajuste_familiar->delete();
        return redirect()->route('ajuste-familiar.index')->with('success', 'Registro eliminado.');
    }

    public function crearfamilia($nombrefamiliar, $padreId, $madreId, $encargadoId, $estudianteId, $estadoId)
    {
        // Obtener el año actual
        $año = date('Y');

        // Calcular el correlativo (puedes ajustar esto según tus necesidades, aquí simplemente cuenta el número total de registros)
        $correlativo = str_pad(Familia::count() + 1, 5, '0', STR_PAD_LEFT);

        // Crear un placeholder temporal para el ID, que será reemplazado después de crear el registro
        $codigoFamiliarTemporal = "F-{$año}-XXXX{$correlativo}";

        // Crear la familia (sin el código final aún)
        $familia = Familia::create([
            'nombre_familiar' => $nombrefamiliar,
            'codigo_familiar' => $codigoFamiliarTemporal, // Temporal
            'padre_persona_id' => $padreId,
            'madre_persona_id' => $madreId,
            'encargado_persona_id' => $encargadoId,
            'estudiante_id' => $estudianteId,
            'estado_id' => $estadoId,
        ]);

        // Verificar si la creación de la familia falló
        if (!$familia->id) {
            return ['error' => 'Hubo un problema al crear la familia.'];
        }

        // Generar el código familiar con el ID real
        $codigoFamiliar = "F-{$año}-{$familia->id}{$correlativo}";

        // Actualizar el registro con el código correcto
        $familia->codigo_familiar = $codigoFamiliar;
        $familia->save();

        // Retornar el ID del registro creado
        return $familia->id;
    }


}
