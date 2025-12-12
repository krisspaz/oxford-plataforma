<?php



namespace App\Http\Controllers;

use App\Models\Encargado;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Parentesco;
use App\Models\IdentificacionDocumento;
use Illuminate\Http\Request;

class EncargadoController extends Controller
{
    public function index()
    {
        $encargados = Encargado::all();
        return view('encargados.index', compact('encargados'));
    }

    public function create()
    {
        $departamentos = Departamento::all();
        $parentescos = Parentesco::all();
        $identificacionDocumentos = IdentificacionDocumento::all();
        return view('encargados.create', compact('departamentos', 'parentescos', 'identificacionDocumentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'parentesco_id' => 'required',
            'nombre' => 'required',
            'apellido' => 'required',
            'identificacion_documentos_id' => 'required',
            'num_documento' => 'required',
            'fecha_nacimiento' => 'required|date',
            'profesion' => 'required',
            'telefono' => 'required',
            'municipio_id' => 'required',
            'direccion' => 'required',
        ]);

        Encargado::create($request->all());

        return redirect()->route('encargados.index')->with('success', 'Encargado creado exitosamente.');
    }

    public function show(Encargado $encargado)
    {
        return view('encargados.show', compact('encargado'));
    }

    public function edit(Encargado $encargado)
    {
        $departamentos = Departamento::all();
        $parentescos = Parentesco::all();
        $municipios = Municipio::where('departamento_id', $encargado->municipio->departamento_id)->get();
        $identificacionDocumentos = IdentificacionDocumento::all();
        return view('encargados.edit', compact('encargado', 'departamentos', 'parentescos', 'municipios', 'identificacionDocumentos'));
    }

    public function update(Request $request, Encargado $encargado)
    {
        $request->validate([
            'parentesco_id' => 'required',
            'nombre' => 'required',
            'apellido' => 'required',
            'identificacion_documentos_id' => 'required',
            'num_documento' => 'required',
            'fecha_nacimiento' => 'required|date',
            'profesion' => 'required',
            'telefono' => 'required',
            'municipio_id' => 'required',
            'direccion' => 'required',
        ]);

        $encargado->update($request->all());

        return redirect()->route('encargados.index')->with('success', 'Encargado actualizada exitosamente.');
    }

    public function destroy(Encargado $encargado)
    {
        $encargado->delete();
        return redirect()->route('encargados.index')->with('success', 'Encargado eliminada exitosamente.');
    }

    public function getMunicipios($departamento_id)
    {
        $municipios = Municipio::where('departamento_id', $departamento_id)->get();
        return response()->json($municipios);
    }
}
