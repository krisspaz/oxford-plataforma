<?php

namespace App\Http\Controllers;

use App\Models\DocumentoInscripcion;
use App\Models\Estado;
use App\Models\Matriculacion;
use App\Models\Estudiante;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DocumentoInscripcionController extends Controller
{
    public function index(Request $request)
    {
        $incluirExpirados = $request->has('incluir_expirados');
        $hoy = Carbon::now()->startOfDay();

        $query = DocumentoInscripcion::with(['estudiante.persona', 'estado']);

        if (! $incluirExpirados) {
            $query->where(function ($q) use ($hoy) {
                $q->whereNull('fexpiracion')
                  ->orWhereDate('fexpiracion', '>=', $hoy);
            });
        }

        $documentos = $query->get();

        return view('documentos.index', compact('documentos', 'incluirExpirados'));
    }

    public function create()
    {
        $estudiantes = Estudiante::all();
        $estados = Estado::all();
        return view('documentos.create', compact('estudiantes', 'estados'));
    }

    public function store(Request $request)
    {


        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'tipo_documento' => 'required|string|max:255',
            'nombre_documento' => 'required|string|max:255',
            'documento' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'fexpiracion' => 'nullable|date',
            'estado_id' => 'required|exists:tb_estados,id',
            'inscripcion_id' => 'required|exists:inscripciones,id',
        ]);

        // Guardar archivo
        $ruta = $request->file('documento')->store('documentos', 'public');

        DocumentoInscripcion::create([
            'estudiante_id' => $request->estudiante_id,
            'tipo_documento' => $request->tipo_documento,
            'nombre_documento' => $request->nombre_documento,
            'documento' => $ruta,
            'fexpiracion' => $request->fexpiracion,
            'estado_id' => $request->estado_id,
            'inscripcion_id' => $request->inscripcion_id,
        ]);

        return redirect()->route('documentos.index')->with('success', 'Documento creado correctamente.');
    }

    public function show($id)
    {
        $documento = DocumentoInscripcion::with(['estudiante', 'estado'])->findOrFail($id);
        return view('documentos.show', compact('documento'));
    }

    public function edit($id)
    {
        $documento = DocumentoInscripcion::findOrFail($id);
        $estudiantes = Estudiante::all();
        $estados = Estado::all();
        return view('documentos.edit', compact('documento', 'estudiantes', 'estados'));
    }

    public function update(Request $request, $id)
    {

        //dd($request->all(),$id);
        $documento = DocumentoInscripcion::findOrFail($id);

        $request->validate([
            'estudiante_id' => 'exists:estudiantes,id',
            'tipo_documento' => 'string|max:255',
            'nombre_documento' => 'string|max:255',
            'documento' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'fexpiracion' => 'nullable|date',
            'estado_id' => 'exists:tb_estados,id',
        ]);

        if ($request->hasFile('documento')) {
            $ruta = $request->file('documento')->store('documentos', 'public');
            $documento->documento = $ruta;
        }

        $documento->update([
            'estudiante_id' => $request->estudiante_id,
            'tipo_documento' => $request->tipo_documento,
            'nombre_documento' => $request->nombre_documento,
            'fexpiracion' => $request->fexpiracion,
            'estado_id' => $request->estado_id,
        ]);

        return redirect()->route('documentos.index')->with('success', 'Documento actualizado correctamente.');
    }

    public function destroy($id)
    {
        $documento = DocumentoInscripcion::findOrFail($id);
        $documento->delete();
        return redirect()->route('documentos.index')->with('success', 'Documento eliminado correctamente.');
    }

    public function obtenerCiclos($estudiante_id)
    {

        // dd($estudiante_id);
        $ciclos = Matriculacion::where('estudiante_id', $estudiante_id)
            ->pluck('ciclo_escolar')
            ->unique()
            ->values();

        return response()->json($ciclos);
    }

    public function obtenerInscripciones($estudiante_id, $ciclo_escolar)
    {
        $inscripciones = Matriculacion::where('estudiante_id', $estudiante_id)
            ->where('ciclo_escolar', $ciclo_escolar)
            ->get(['id', 'ciclo_escolar']);

        return response()->json($inscripciones);
    }

}
