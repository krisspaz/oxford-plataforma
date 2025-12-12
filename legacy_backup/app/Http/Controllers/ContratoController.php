<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Familia;
use App\Models\Contrato;
use App\Models\Matriculacion;
use Illuminate\Support\Facades\Storage;
use App\Models\PvEstudianteContrato;
use App\Models\Estudiante; // Ajusta el modelo según tu estructura

class ContratoController extends Controller
{
    public function generarContrato($id)
    {

        // Cargar el estudiante junto con sus relaciones necesarias
        $estudiante = Estudiante::with([
            'familia.padre',
            'familia.madre',
            'familia.encargado',
           'cgshges.niveles.costos', // Incluye las relaciones necesarias
        ])->findOrFail($id->estudiante->id);

        // Verificar que la familia exista
        $familia = $estudiante->familia;
        if (!$familia) {
            return response()->json(['error' => 'El estudiante no tiene una familia asociada.'], 404);
        }

        // Determinar el representante: encargado > padre > madre
        $representante = $familia->encargado ?? $familia->padre ?? $familia->madre;
        if (!$representante) {
            return response()->json(['error' => 'No se encontró un representante asociado al estudiante.'], 404);
        }


        $proximoId = (Contrato::max('id') ?? 0) + 1;
        // Datos necesarios para la plantilla del contrato
        $data = [
            'estudiante' => $estudiante,
            'representante' => $representante,
            'centro' => [
                'nombre' => 'OXFORD BILINGUAL SCHOOL',
                'direccion' => '2da. Calle 16-94, Zona 4',
                'municipio' => 'Guatemala',
                'departamento' => 'Guatemala',
            ],

            'contrato_id' => $proximoId,
        ];

        // Generar el PDF usando una plantilla
        $pdf = \PDF::loadView('contratos.plantilla', $data);

        // Obtener el contenido del PDF
        $contenidoPdf = $pdf->output();

        // Descargar el PDF con un nombre dinámico
        $nombreArchivo = 'Contrato_' .$estudiante->carnet."_".$estudiante->persona->nombres . '.pdf';
        // Descargar el archivo PDF

        // Guardar el archivo en el directorio 'public/contratos'
        $path = 'contratos/' . $nombreArchivo;
        Storage::disk('public')->put($path, $contenidoPdf); // Guardar en storage/public



        $contrato = Contrato::create([
           'numero_contrato' => 'C-'.$proximoId ,
           'inscripcion_id' => $id->id,
           'persona_id' => $representante->id, // Ajustar según la relación con usuarios
           'fecha_inicio' => now(),
           'estado' => 'activo',
           'ciclo_escolar' => $id->ciclo_escolar,
           'archivo' => $path,
           'descripcion' => 'Contrato generado automáticamente para el estudiante ' . $estudiante->nombre,
        ]);

        $PvEstudianteContrato =  PvEstudianteContrato::create([
         'estudiante_id' => $estudiante->id,
         'contrato_id' => $contrato->id,
         'estado' => 'Vigente',

        ]);
        return response()->streamDownload(
            fn () => print($pdf->output()),
            $nombreArchivo,
            ['Content-Type' => 'application/pdf']
        );

    }


    public function recrearcontrato($inscripcion_id, $numero)
    {
        // Cargar inscripción con estudiante y familia
        $inscripcion = Matriculacion::with([
            'estudiante',
            'estudiante.familia.padre',
            'estudiante.familia.madre',
            'estudiante.familia.encargado',
            'cgshges.niveles.costos',
        ])->findOrFail($inscripcion_id);

        $estudiante = $inscripcion->estudiante;
        $familia = $estudiante->familia;

        if (!$familia) {
            return response()->json(['error' => 'El estudiante no tiene una familia asociada.'], 404);
        }

        $representante = $familia->encargado ?? $familia->padre ?? $familia->madre;
        if (!$representante) {
            return response()->json(['error' => 'No se encontró un representante asociado al estudiante.'], 404);
        }

        $proximoId = $numero;

        $data = [
            'estudiante' => $estudiante,
            'representante' => $representante,
            'centro' => [
                'nombre' => 'OXFORD BILINGUAL SCHOOL',
                'direccion' => '2da. Calle 16-94, Zona 4',
                'municipio' => 'Guatemala',
                'departamento' => 'Guatemala',
            ],
            'contrato_id' => $proximoId,
        ];

        // Generar PDF
        $pdf = \PDF::loadView('contratos.plantilla', $data);
        $contenidoPdf = $pdf->output();

        // Nombre y ruta del archivo
        $nombreArchivo = 'Contrato_' . $estudiante->carnet . "_" . $estudiante->persona->nombres . '.pdf';
        $path = 'contratos/' . $nombreArchivo;

        // ✅ Asegurar que la carpeta exista
        if (!Storage::disk('public')->exists('contratos')) {
            Storage::disk('public')->makeDirectory('contratos');
        }

        // ✅ Guardar PDF en storage
        $guardado = Storage::disk('public')->put($path, $contenidoPdf);
        if (!$guardado) {
            return response()->json(['error' => 'No se pudo guardar el archivo en el almacenamiento.'], 500);
        }

        // Buscar contrato existente y actualizar
        $contrato = Contrato::where('inscripcion_id', $inscripcion_id)->first();
        if ($contrato) {
            $contrato->update([
                'archivo' => $path, // Reemplaza el archivo
            ]);
        } else {
            return response()->json(['error' => 'No se encontró un contrato asociado a esta inscripción.'], 404);
        }

        // Descargar el PDF
        return response()->streamDownload(
            fn () => print($pdf->output()),
            $nombreArchivo,
            ['Content-Type' => 'application/pdf']
        );
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

            return redirect()->route('ajustes_contrato.index')->with('success', 'Contrato Firmado subido exitosamente.');

        }

        return back()->withErrors('Hubo un error al subir el archivo.');
    }


}
