<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\Municipio;
use App\Models\Departamento;
use App\Models\Status;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    // Mostrar una lista de los recursos
    public function index()
    {
       

        $sucursales = Sucursal::with('municipio', 'status')->get();
        return view('sucursales.index', compact('sucursales'));
    }

    // Mostrar el formulario para crear un nuevo recurso
    public function create()
    {
        $departamentos = Departamento::all();
        $statuses = Status::all();
        return view('sucursales.create', compact('departamentos', 'statuses'));
    }

    // Almacenar un nuevo recurso en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'nombre_sucursal' => 'required|string|max:255',
            'municipio_id' => 'required|exists:municipios,id',
            'direccion' => 'required|string|max:255',
            'status_id' => 'required|exists:status,id',
        ]);

        Sucursal::create($request->all());
        return redirect()->route('sucursales.index')
                         ->with('success', 'Sucursal creada exitosamente.');
    }

    // Mostrar los detalles de un recurso específico
    public function show(Sucursal $sucursale)
    {
          
        //  $sucursal->load('municipio', 'status');
        return view('sucursales.show', compact('sucursale'));
    }

        

    // Mostrar el formulario para editar un recurso específico
    public function edit(Sucursal $sucursale)
    {
        $departamentos = Departamento::all(); // Obtener todos los departamentos
        $statuses = Status::all(); // Obtener todos los statuses
    
        return view('sucursales.edit', compact('sucursale', 'departamentos', 'statuses'));
    }
    
    
    

    // Actualizar un recurso específico en la base de datos
    public function update(Request $request, Sucursal $sucursale)
    {
        $request->validate([
            'nombre_sucursal' => 'required|string|max:255',
            'municipio_id' => 'required|exists:municipios,id',
            'direccion' => 'required|string|max:255',
            'status_id' => 'required|exists:status,id',
        ]);

        $sucursale->update($request->all());
        return redirect()->route('sucursales.index')
                         ->with('success', 'Sucursal actualizada exitosamente.');
    }

    // Eliminar un recurso específico de la base de datos
    public function destroy(Sucursal $sucursale)
    {
        $sucursale->delete();
        return redirect()->route('sucursales.index')
                         ->with('success', 'Sucursal eliminada exitosamente.');
    }

    // Obtener los municipios asociados a un departamento específico
    public function getMunicipiosByDepartamento(Request $request)
    {
        $departamentoId = $request->input('departamento_id');
        $municipios = Municipio::where('departamento_id', $departamentoId)->get();
        return response()->json($municipios);
    }
}
