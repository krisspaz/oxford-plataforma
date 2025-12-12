<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\Bimestre;
use App\Models\ContenidoMateria;

use Illuminate\Support\Facades\Storage;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class ContenidoMateriaController extends Controller
{
    public function index()
    {
        $userId = CRUDBooster::myId();

        // Verificamos si hay sesión de usuario
        if (!$userId) {
            abort(403, 'Acceso no autorizado.');
        }

        // Buscamos el docente relacionado al usuario autenticado
        $docente = Docente::whereHas('persona', function ($query) use ($userId) {
            $query->where('cms_users_id', $userId);
        })->first();

        if (!$docente) {
            abort(404, 'No se encontró un docente vinculado al usuario.');
        }

        $añoActual = now()->year;

        // Solo traer los contenidos que pertenecen a las materias del docente
        $contenidoMaterias = ContenidoMateria::with([
                'materiaCursos.cgshges',
                'materiaCursos.materia',
                'bimestre',
                'docente.persona'
            ])
            ->whereYear('created_at', $añoActual)
            ->where('docente_id', $docente->id)
            ->get();

        return view('contenido_materias.index', compact('contenidoMaterias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 🔹 Obtener el usuario autenticado
        $userId = CRUDBooster::myId();

        // Verifica que el usuario esté autenticado
        if (!$userId) {
            return redirect()->back()->with('error', 'Usuario no autenticado.');
        }

        // 🔹 Obtener el docente asociado al usuario autenticado
        $docente = Docente::whereHas('persona', function ($query) use ($userId) {
            $query->where('cms_users_id', $userId);
        })->first();

        if (!$docente) {
            return redirect()->back()->with('error', 'No se encontró el docente asociado al usuario.');
        }

        // 🔹 Cargar relaciones, pero solo materias activas y cursos activos
        $docente = Docente::with([
            'persona',
            'materiasCursos' => function ($query) {
                $query->whereHas('estado', function ($q) {
                    $q->where('estado', 'Activo'); // Solo MateriaCurso activo
                });
            },
            'materiasCursos.materia' => function ($query) {
                $query->whereHas('estado', function ($q) {
                    $q->where('estado', 'Activo'); // Solo Materia activa
                });
            },
            'materiasCursos.materia.gestionMateria',
            'materiasCursos.cgshges'
        ])->find($docente->id);

        // 🔹 Preparar datos para la vista
        $materias = $docente;
        $curso = $docente;

        $bimestreActual = Bimestre::actual()->first();
        $bimestres = Bimestre::all();



        return view('contenido_materias.create', compact(
            'materias',
            'bimestreActual',
            'bimestres',
            'docente'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $tipoContenido = $request->input('tipo_contenido');

        $rules = [
            'titulo' => 'required',
            'descripcion' => 'required',
            'materia_id' => 'required|array',
            'materia_id.*' => 'exists:materias_cursos,id',
            'bimestre_id' => 'required|exists:bimestres,id',
            'tipo_contenido' => 'required|in:video,audio,documento,imagen,link',
            'docente_id' => 'required|exists:docentes,id',
        ];

        if ($tipoContenido === 'link') {
            $rules['link'] = 'required|url';
        } else {
            $rules['archivo'] = 'required|file|mimes:jpg,jpeg,png,mp4,mp3,pdf,doc,docx|max:20480';
        }

        $validatedData = $request->validate($rules);

        // Determinar el contenido (link o archivo subido)
        if ($tipoContenido === 'link') {
            $archivo = $request->link;
        } elseif ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo')->store('contenido_materias', 'public');
        }

        // Crear un registro por cada materia seleccionada
        foreach ($request->materia_id as $materiaId) {
            ContenidoMateria::create([
                'titulo' => $validatedData['titulo'],
                'descripcion' => $validatedData['descripcion'],
                'materia_id' => $materiaId,
                'bimestre_id' => $validatedData['bimestre_id'],
                'tipo_contenido' => $validatedData['tipo_contenido'],
                'archivo' => $archivo,
                'docente_id' => $validatedData['docente_id'],
            ]);
        }

        return redirect()->route('contenido_materias.index')->with('success', 'Contenido guardado exitosamente.');
    }


    /**
    * Display the specified resource.
    */
    public function show(ContenidoMateria $contenidoMateria)
    {
        return view('contenido_materias.show', compact('contenidoMateria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContenidoMateria $contenidoMateria)
    {
        // 🔹 Obtener el usuario autenticado
        $userId = CRUDBooster::myId();

        // Verificar que el usuario esté autenticado
        if (!$userId) {
            return redirect()->back()->with('error', 'Usuario no autenticado.');
        }

        // 🔹 Buscar el docente vinculado al usuario
        $docentes = Docente::whereHas('persona', function ($query) use ($userId) {
            $query->where('cms_users_id', $userId);
        })->first();

        if (!$docentes) {
            return redirect()->back()->with('error', 'No se encontró el docente asociado al usuario.');
        }

        // 🔹 Cargar solo materias y asignaciones activas
        $docentes = Docente::with([
            'materiasCursos' => function ($query) {
                $query->whereHas('estado', function ($q) {
                    $q->where('estado', 'Activo'); // Solo registros activos en MateriaCurso
                });
            },
            'materiasCursos.materia' => function ($query) {
                $query->whereHas('estado', function ($q) {
                    $q->where('estado', 'Activo'); // Solo materias activas
                });
            },
            'materiasCursos.materia.gestionMateria',
            'materiasCursos.cgshges'
        ])->find($docentes->id);

        // 🔹 Obtener solo las materias activas
        $materias = $docentes->materiasCursos;



        // 🔹 Bimestres
        $bimestreActual = Bimestre::actual()->first();
        $bimestres = Bimestre::all();

        return view('contenido_materias.edit', compact(
            'contenidoMateria',
            'materias',
            'bimestreActual',
            'bimestres',
            'docentes'
        ));
    }

    /**
    * Update the specified resource in storage.
    */
    public function update(Request $request, ContenidoMateria $contenidoMateria)
    {

        $tipoContenido = $request->input('tipo_contenido');

        $rules = [
            'titulo' => 'required',
            'descripcion' => 'required',
            'materia_id' => 'required|exists:materias_cursos,id',
            'bimestre_id' => 'required|exists:bimestres,id',
            'tipo_contenido' => 'required|in:video,audio,documento,imagen,link',
            'docente_id' => 'required|exists:docentes,id',
        ];

        if ($tipoContenido === 'link') {
            $rules['link'] = 'url';
        } else {
            $rules['archivo'] = 'file|mimes:jpg,jpeg,png,mp4,mp3,pdf,doc,docx|max:20480';
        }

        $validatedData = $request->validate($rules);

        // Verificar el tipo de contenido
        if ($request->tipo_contenido === 'link') {
            // Si es un enlace, simplemente asignarlo al campo 'archivo'
            $validatedData['archivo'] = $request->link;
        } elseif ($request->hasFile('archivo')) {
            // Eliminar el archivo existente, si existe
            if ($contenidoMateria->archivo && \Storage::disk('public')->exists($contenidoMateria->archivo)) {
                \Storage::disk('public')->delete($contenidoMateria->archivo);
            }

            // Guardar el nuevo archivo en el almacenamiento público
            $path = $request->file('archivo')->store('contenido_materias', 'public');
            $validatedData['archivo'] = $path;
        } else {
            // Si no se carga un archivo, conservar el existente
            $validatedData['archivo'] = $contenidoMateria->archivo;
        }





        // Actualizar el registro en la base de datos
        $contenidoMateria->update($validatedData);

        return redirect()->route('contenido_materias.index')->with('success', 'Contenido actualizado exitosamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContenidoMateria $contenidoMateria)
    {
        $contenidoMateria->delete();

        return redirect()->route('contenido_materias.index')->with('success', 'Contenido eliminado correctamente.');
    }

    public function testArchivo()
    {
        return Storage::disk('public')->exists('contenido_materias/hTNpJreV0he8DnQ2ZEjTSQiMzboOkYxbG5xJ0Qmg.mp4')
            ? 'El archivo sí existe en el storage'
            : 'El archivo NO existe en el storage';
    }
}
