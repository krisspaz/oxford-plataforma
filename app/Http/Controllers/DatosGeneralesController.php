<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Padre;
use App\Models\Madre;
use App\Models\Encargado;
use App\Models\Parentesco;
use App\Models\IdentificacionDocumento;
use App\Models\Municipio;
use App\Models\PvPadresTutores;

class DatosGeneralesController extends Controller
{
    public function index()
    {
        // Mostrar lista de familias o registros
        $familias = PvPadresTutores::with(['padre', 'madre', 'encargado'])->get();
        return view('datosgenerales.index', compact('familias'));
    }

    public function create()
    {
        // Mostrar formulario para crear una nueva familia
        $parentescos = Parentesco::all();
        $documentos = IdentificacionDocumento::all();
        $municipios = Municipio::all();
        return view('datosgenerales.create', compact('parentescos', 'documentos', 'municipios'));
    }

    public function store(Request $request)
    {
        // Validar y almacenar los datos de los padres, madres, encargados y generar el código de familia
        $padre = Padre::create($request->input('padre'));
        $madre = Madre::create($request->input('madre'));
        $encargado = Encargado::create($request->input('encargado'));

        $codigoFamiliar = $this->generarCodigoFamiliar();

        $familia = PvPadresTutores::create([
            'padre_id' => $padre->id,
            'madre_id' => $madre->id,
            'encargado_id' => $encargado->id,
            'codigofamiliar' => $codigoFamiliar,
        ]);

        return redirect()->route('datosgenerales.index')->with('success', 'Familia creada exitosamente.');
    }

    public function show($id)
    {
        // Mostrar detalles de una familia en específico
        $familia = PvPadresTutores::with(['padre', 'madre', 'encargado'])->findOrFail($id);
        return view('datosgenerales.show', compact('familia'));
    }

    public function edit($id)
    {
        // Mostrar formulario para editar una familia existente
        $familia = PvPadresTutores::findOrFail($id);
        $parentescos = Parentesco::all();
        $documentos = IdentificacionDocumento::all();
        $municipios = Municipio::all();
        return view('datosgenerales.edit', compact('familia', 'parentescos', 'documentos', 'municipios'));
    }

    public function update(Request $request, $id)
    {
        // Validar y actualizar los datos de la familia
        $familia = PvPadresTutores::findOrFail($id);

        $familia->padre->update($request->input('padre'));
        $familia->madre->update($request->input('madre'));
        $familia->encargado->update($request->input('encargado'));

        return redirect()->route('datosgenerales.index')->with('success', 'Familia actualizada exitosamente.');
    }

    public function destroy($id)
    {
        // Eliminar una familia
        $familia = PvPadresTutores::findOrFail($id);
        $familia->delete();
        return redirect()->route('datosgenerales.index')->with('success', 'Familia eliminada exitosamente.');
    }

    private function generarCodigoFamiliar()
    {
        // Lógica para generar un código único de familia
        $ultimoCodigo = PvPadresTutores::max('codigofamiliar');
        return str_pad($ultimoCodigo + 1, 6, '0', STR_PAD_LEFT);
    }
}
