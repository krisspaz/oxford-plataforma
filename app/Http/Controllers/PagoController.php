<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Pago;
use App\Models\Familia;
use App\Models\Estudiante;
use App\Models\Factura;
use App\Models\Convenio;
use App\Models\Cuota;
use App\Models\Matriculacion;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\CMSUser;
use App\Models\Bimestre;
use App\Models\Gestion;
use App\Models\CmsNotification;
use CRUDBooster;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{


    public function descargarInsolvente($estudiante_id)
    {
        $hoy = now()->format('Y-m-d');
        $estudiante = Estudiante::with(['persona'])->findOrFail($estudiante_id);
        $matriculas = Matriculacion::where('estudiante_id', $estudiante->id)->get();
        $detalles = [];
        foreach ($matriculas as $matricula) {
            $convenio = Convenio::where('inscripcion_id', $matricula->id)
                ->with(['cuotas' => function ($q) use ($hoy) {
                    $q->where('estado', 'pendiente')
                      ->whereDate('fecha_vencimiento', '<', $hoy) // ✔ SOLO VENCIDAS
                      ->with('productoSeleccionado.detalle');
                }])
                ->first();
            if (!$convenio) {
                continue;
            }
            $cuotasPendientes = $convenio->cuotas; // Solo las cuotas pendientes y vencidas
            if ($cuotasPendientes->isEmpty()) {
                continue;
            }
            $detalles[] = [
                'ciclo' => $matricula->ciclo_escolar,
                'cuotas' => $cuotasPendientes
            ];
        }

        if (empty($detalles)) {
            return back()->with('error', 'Este estudiante no tiene pagos pendientes vencidos');
        }

        // Generar PDF individual usando la vista
        $pdf = \PDF::loadView('pagos.pdf_estudiante', [
            'estudiante' => $estudiante,
            'detalles'   => $detalles
        ]);

        return $pdf->stream("pendientes_{$estudiante->id}.pdf");
    }

    public function obtenerEstudiantesInsolventes()
    {
        $estudiantes = Estudiante::with([
            'persona',
            'convenios' => function ($query) {
                $query->where('estado', 'activo')
                    ->with(['cuotas' => function ($query) {
                        $query->where('estado', 'pendiente')
                            ->where('fecha_vencimiento', '<', now()); // solo cuotas vencidas
                    }]);
            },
            'convenios.inscripcion.productosSeleccionados.detalle'
        ])->get();

        $conveniosInsolventes = [];

        foreach ($estudiantes as $estudiante) {

            $matriculaciones = Matriculacion::where('estudiante_id', $estudiante->id)
                ->orderBy('ciclo_escolar')
                ->get();

            if ($matriculaciones->isEmpty()) {
                continue;
            }

            foreach ($matriculaciones as $matricula) {

                $convenio = Convenio::where('inscripcion_id', $matricula->id)
                    ->with(['cuotas.productoSeleccionado.detalle'])
                    ->first();

                if (!$convenio) {
                    continue;
                }

                // cuotas pendientes vencidas
                $cuotasPendientes = $convenio->cuotas
                    ->where('estado', 'pendiente')
                    ->where('fecha_vencimiento', '<', now());

                if ($cuotasPendientes->isEmpty()) {
                    continue;
                }

                $conveniosInsolventes[$estudiante->id]['estudiante'] = $estudiante;
                $conveniosInsolventes[$estudiante->id]['ciclos'][] = $matricula->ciclo_escolar;
                $conveniosInsolventes[$estudiante->id]['convenios'][] = [
                    'matricula' => $matricula,
                    'convenio'  => $convenio,
                    'cuotas'    => $cuotasPendientes
                ];
            }
        }

        return $conveniosInsolventes;
    }

    public function descargarPdfTodos()
    {
        $resultados = $this->obtenerEstudiantesInsolventes();

        if (empty($resultados)) {
            return back()->with('error', 'No hay estudiantes con pagos pendientes.');
        }

        $pdf = \PDF::loadView('pagos.pdf_todos', [
            'resultados' => $resultados
        ]);

        return $pdf->stream('pendientes_todos.pdf');
    }


    public function index()
    {
        // Obtener el año actual

        $estudiantes = Estudiante::all();
        $familias = Familia::all();

        $anioActual = now()->year;
        // Calcular los años requeridos
        $anios = [
            $anioActual,
            $anioActual + 1,
            $anioActual - 1,
            $anioActual - 2,
            $anioActual - 3,
        ];
        return view('pagos.buscar', compact('anios', 'estudiantes', 'familias'));
    }



    public function mostrarestudiante(Request $request)
    {
        $anioActual = now()->year;

        $anios = [
            $anioActual,
            $anioActual + 1,
            $anioActual - 1,
            $anioActual - 2,
            $anioActual - 3,
        ];

        $user = CMSUser::find(CRUDBooster::myId());
        $persona = $user->tareasAsignadas()->first();

        // Obtener lista de estudiantes según tipo de usuario
        if ($persona) {
            // Buscar si la persona pertenece a una familia
            $familia = Familia::where(function ($query) use ($persona) {
                $query->where('padre_persona_id', $persona->id)
                    ->orWhere('madre_persona_id', $persona->id)
                    ->orWhere('encargado_persona_id', $persona->id);
            })->first();

            if ($familia) {
                // Tiene familia → mostrar solo los estudiantes de esa familia
                $codigoFamiliar = $familia->codigo_familiar;
                $familias = Familia::where('codigo_familiar', $codigoFamiliar)->get();
                $estudiantes = Estudiante::whereIn('id', $familias->pluck('estudiante_id'))->get();
            } else {
                // No pertenece a familia → mostrar todos los estudiantes
                $estudiantes = Estudiante::all();
            }
        } else {
            // No tiene persona asignada → mostrar todos los estudiantes
            $estudiantes = Estudiante::all();
        }

        // Cargar las inscripciones según el año (si lo envía)
        $inscripciones = Matriculacion::query()
            ->when($request->anio_ciclo_escolar, function ($query, $anio) use ($estudiantes) {
                $query->whereIn('estudiante_id', $estudiantes->pluck('id'))
                      ->where('ciclo_escolar', $anio);
            })
            ->get();

        $bimestres = Bimestre::all();
        $ciclos = Gestion::all();

        return view('pagos.buscarfinanciero', compact('estudiantes', 'bimestres', 'ciclos', 'anios'));
    }

    public function insolventes(Request $request)
    {


        $hoy = now()->format('Y-m-d');

        // FILTRO POR NOMBRE DEL ESTUDIANTE
        $busqueda = $request->buscar;

        $estudiantes = Estudiante::with([
               'persona',
               'convenios' => function ($query) {
                   $query->where('estado', 'activo')
                         ->with(['cuotas' => function ($query) {
                             $query->where('estado', 'pendiente')
                             ->orWhere('estado', 'solicitud de baja');
                         }]);
               },
               'convenios.inscripcion.productosSeleccionados.detalle'
           ])
           ->when($busqueda, function ($query) use ($busqueda) {
               $query->whereHas('persona', function ($q) use ($busqueda) {
                   $q->where('nombres', 'LIKE', "%$busqueda%")
                     ->orWhere('apellidos', 'LIKE', "%$busqueda%");
               });
           })
           ->get();

        if ($estudiantes->isEmpty()) {
            return back()->with('error', 'No se encontraron estudiantes en la busqueda.');
        }

        $conveniosInsolventes = [];

        foreach ($estudiantes as $estudiante) {

            // ✔ Todas las matrículas del estudiante
            $matriculaciones = Matriculacion::where('estudiante_id', $estudiante->id)
                ->orderBy('ciclo_escolar')
                ->get();

            if ($matriculaciones->isEmpty()) {
                continue;
            }

            foreach ($matriculaciones as $matricula) {

                $convenio = Convenio::where('inscripcion_id', $matricula->id)
                    ->with(['cuotas' => function ($q) use ($hoy) {
                        $q->where('estado', 'pendiente')
                         ->orWhere('estado', 'solicitud de baja')
                          ->whereDate('fecha_vencimiento', '<', $hoy) // ✔ SOLO VENCIDAS
                          ->with('productoSeleccionado.detalle');
                    }])
                    ->first();

                if (!$convenio) {
                    continue;
                }

                // ✔ Filtrar nuevamente por seguridad
                $cuotasVencidas = $convenio->cuotas->filter(function ($cuota) use ($hoy) {
                    return $cuota->estado === 'pendiente'
                        && !empty($cuota->fecha_vencimiento)
                        && $cuota->fecha_vencimiento < $hoy;
                });

                if ($cuotasVencidas->isEmpty()) {
                    continue;
                }

                // ✔ Guardado en arreglo final
                $conveniosInsolventes[$estudiante->id]['estudiante'] = $estudiante;
                $conveniosInsolventes[$estudiante->id]['ciclos'][] = $matricula->ciclo_escolar;
                $conveniosInsolventes[$estudiante->id]['convenios'][] = [
                    'matricula' => $matricula,
                    'convenio'  => $convenio,
                    'cuotas'    => $cuotasVencidas
                ];
            }
        }

        if (empty($conveniosInsolventes)) {
            return back()->with('error', 'No hay estudiantes con cuotas vencidas.');
        }

        return view('pagos.insolventes', [
            'resultados' => $conveniosInsolventes
        ]);
    }


    public function exonerados(Request $request)
    {


        $hoy = now()->format('Y-m-d');

        // FILTRO POR NOMBRE DEL ESTUDIANTE
        $busqueda = $request->buscar;

        $estudiantes = Estudiante::with([
               'persona',
               'convenios' => function ($query) {
                   $query->where('estado', 'activo')
                         ->with(['cuotas' => function ($query) {
                             $query->where('baja', 'si');

                         }]);
               },
               'convenios.inscripcion.productosSeleccionados.detalle'
           ])
           ->when($busqueda, function ($query) use ($busqueda) {
               $query->whereHas('persona', function ($q) use ($busqueda) {
                   $q->where('nombres', 'LIKE', "%$busqueda%")
                     ->orWhere('apellidos', 'LIKE', "%$busqueda%");
               });
           })
           ->get();

        if ($estudiantes->isEmpty()) {
            return back()->with('error', 'No se encontraron estudiantes en la busqueda.');
        }

        $conveniosInsolventes = [];

        foreach ($estudiantes as $estudiante) {

            // ✔ Todas las matrículas del estudiante
            $matriculaciones = Matriculacion::where('estudiante_id', $estudiante->id)
                ->orderBy('ciclo_escolar')
                ->get();

            if ($matriculaciones->isEmpty()) {
                continue;
            }

            foreach ($matriculaciones as $matricula) {

                $convenio = Convenio::where('inscripcion_id', $matricula->id)
                    ->with(['cuotas' => function ($q) use ($hoy) {
                        $q->where('baja', 'si')
                          ->whereDate('fecha_vencimiento', '<', $hoy) // ✔ SOLO VENCIDAS
                          ->with('productoSeleccionado.detalle');
                    }])
                    ->first();

                if (!$convenio) {
                    continue;
                }

                // ✔ Filtrar nuevamente por seguridad
                $cuotasVencidas = $convenio->cuotas->filter(function ($cuota) use ($hoy) {
                    return $cuota->estado === 'pendiente' &&$cuota->baja === 'si';


                });

                if ($cuotasVencidas->isEmpty()) {
                    continue;
                }

                // ✔ Guardado en arreglo final
                $conveniosInsolventes[$estudiante->id]['estudiante'] = $estudiante;
                $conveniosInsolventes[$estudiante->id]['ciclos'][] = $matricula->ciclo_escolar;
                $conveniosInsolventes[$estudiante->id]['convenios'][] = [
                    'matricula' => $matricula,
                    'convenio'  => $convenio,
                    'cuotas'    => $cuotasVencidas
                ];
            }
        }

        if (empty($conveniosInsolventes)) {
            return back()->with('error', 'No hay estudiantes con cuotas vencidas.');
        }

        return view('pagos.exonerados', [
            'resultados' => $conveniosInsolventes
        ]);
    }


    public function buscar(Request $request)
    {

        //dd($request->all());
        $validated = $request->validate([
            'criterio' => 'required|string|in:estudiante,familia',

        ]);

        $ciclo = $request->ciclo_escolar;
        $criterio = $validated['criterio'];
        $valor1 = $request->valor;
        $valor2 = $request->valorf;


        if ($criterio === 'estudiante') {
            // Buscar solo por nombre completo (nombres + apellidos)
            $estudiante = Estudiante::whereHas('persona', function ($query) use ($valor1) {
                $query->where(DB::raw("LOWER(CONCAT(nombres, ' ', apellidos))"), 'LIKE', '%' .$valor1 . '%');
            })
                ->with([
                'persona',
                'convenios' => function ($query) {
                    $query->where('estado', 'activo')->with(['cuotas' => function ($query) {
                        $query->where('estado', 'pendiente');
                    }]);
                },
                'convenios.inscripcion.productosSeleccionados.detalle'
            ])
                ->first();

            if (!$estudiante) {
                return back()->with('error', 'Estudiante no encontrado o sin convenios activos.');
            }

            // Obtener la matrícula activa para el estudiante
            $matriculacion = Matriculacion::where('estudiante_id', $estudiante->id)
                ->where('ciclo_escolar', $ciclo)
                ->first();

            if (!$matriculacion) {
                return back()->with('error', 'Matrícula no encontrada para este estudiante.');
            }

            $convenioID = Convenio::where('inscripcion_id', $matriculacion->id)->first();

            if (!$convenioID) {
                return back()->with('error', 'Convenio no encontrado para esta matrícula.');
            }

            // Obtener el convenio con las cuotas pendientes
            $convenio = Convenio::with(['cuotas.productoSeleccionado.detalle'])->findOrFail($convenioID->id);
            $convenio->cuotas = $convenio->cuotas->where('estado', 'pendiente');

            return view('pagos.resultados_estudiante', compact('convenio'));
        }

        if ($criterio === 'familia') {
            // Buscar la familia por su código familiar

            $familias = Familia::where('codigo_familiar', $valor2)
            ->with([
                'muchosestudiantes' => function ($query) {
                    $query->with([
                        'persona',
                        'convenios' => function ($query) {
                            $query->where('estado', 'activo')
                                  ->with([
                                      'cuotas' => function ($query) {
                                          $query->where('estado', 'pendiente');
                                      },
                                      'inscripcion.productosSeleccionados.detalle'
                                  ]);
                        }
                    ]);
                }
            ])
            ->get();

            // Agrupar por codigo_familiar
            $resultado = $familias->groupBy('codigo_familiar')->map(function ($grupo) {
                // Unir todos los estudiantes en una sola colección
                $estudiantes = $grupo->flatMap->muchosestudiantes;
                //dd( $resultado);
                return [
                    'codigo_familiar' => $grupo->first()->codigo_familiar,
                    'estudiantes' => $estudiantes->map(function ($estudiante) {
                        return [
                            'estudiante' => $estudiante,
                            'persona' => $estudiante->persona ? [
                                'nombres' => $estudiante->persona->nombres,
                                'apellidos' => $estudiante->persona->apellidos
                            ] : null,
                            'convenios' => $estudiante->convenios->map(function ($convenio) {
                                return [
                                    'convenio' => $convenio,
                                    'cuotas_pendientes' => $convenio->cuotas ?? collect(),
                                    'productos' => optional($convenio->inscripcion)->productosSeleccionados->map(function ($producto) {
                                        return [
                                            'nombre' => optional($producto->detalle)->nombre ?? 'N/A',
                                            'descripcion' => optional($producto->detalle)->descripcion ?? 'N/A',
                                            'precio' => optional($producto->detalle)->precio ?? 0.0,
                                        ];
                                    }) ?? collect(),
                                ];
                            }),
                        ];
                    }),
                ];
            })->values(); // Resetear las claves para mantener una lista ordenada

            return view('pagos.resultados_familia', ['resultado' => $resultado]);
        }

        return back()->with('error', 'Criterio no válido.');
    }

    public function estadofinanciero(Request $request)
    {
        $validated = $request->validate([
            'valor' => 'required|string',
        ]);

        $ciclo = $request->ciclo_escolar;
        $valor = $validated['valor'];

        // Buscar por carnet o por nombre completo
        $estudiante = Estudiante::where('id', $valor)
            ->orWhereHas('persona', function ($query) use ($valor) {
                $query->where(DB::raw("CONCAT(nombres, ' ', apellidos)"), 'LIKE', "%{$valor}%");
            })
            ->with([
                'persona',
                'convenios' => function ($query) {
                    $query->where('estado', 'activo')->with(['cuotas' => function ($query) {
                        $query->whereIn('estado', ['pendiente', 'pagada', 'vencida']);
                    }]);
                },
                'convenios.inscripcion.productosSeleccionados.detalle'
            ])
            ->first();

        if (!$estudiante) {
            return back()->with('error', 'Estudiante no encontrado o sin convenios activos.');
        }

        // Obtener la matrícula activa para el estudiante
        $matriculacion = Matriculacion::where('estudiante_id', $estudiante->id)
            ->where('ciclo_escolar', $ciclo)
            ->first();

        if (!$matriculacion) {
            return back()->with('error', 'Matrícula no encontrada para este estudiante.');
        }

        $convenioID = Convenio::where('inscripcion_id', $matriculacion->id)->first();

        if (!$convenioID) {
            return back()->with('error', 'Convenio no encontrado para esta matrícula.');
        }

        // ✅ Obtener el convenio con cuotas y pagos
        $convenio = Convenio::with([
            'cuotas.productoSeleccionado.detalle',
            'pagos' => function ($query) {
                $query->with(['pagoMetodos', 'facturaEmitida', 'reciboEmitido', 'reciboInternoEmitido']);
            }
        ])->findOrFail($convenioID->id);

        // Filtrar las cuotas con estado válido
        $convenio->cuotas = $convenio->cuotas->filter(function ($cuota) {
            return in_array($cuota->estado, ['pendiente', 'pagada', 'vencida']);
        });

        return view('pagos.resultado_estado_financiero', compact('convenio'));
    }



    /**
     * Registrar un pago para las cuotas seleccionadas.
     */



    /**
 * Mostrar el formulario de registro de pago.
 *
 * @param int $convenioId
 * @return \Illuminate\View\View
 */
    public function create($convenioId)
    {
        // Obtener el convenio con sus cuotas pendientes
        $convenio = Convenio::with(['cuotas' => function ($query) {
            $query->where('estado', 'pendiente');
        }, 'inscripcion.estudiante.persona', 'inscripcion.paquete'])
        ->findOrFail($convenioId);

        // Verificar si hay cuotas pendientes
        if ($convenio->cuotas->isEmpty()) {
            return redirect()->route('pagos.index')->with('error', 'No hay cuotas pendientes para este convenio.');
        }

        // Métodos de pago disponibles
        $metodosPago = [
            'efectivo',
            'transferencia',
            'tarjeta',
            'cheque',
            'deposito',
            'intervalo1' => 'Efectivo y Transferencia',
            'intervalo2' => 'Efectivo y Tarjeta',
            'intervalo3' => 'Efectivo y Cheque',
            'intervalo4' => 'Transferencia y Tarjeta',
            'intervalo5' => 'Transferencia y Cheque',
            'intervalo6' => 'Transferencia y Deposito',
        ];

        // Retornar la vista con los datos necesarios
        return view('pagos.create', compact('convenio', 'metodosPago'));
    }



    public function store(Request $request)
    {


        // dd($request->cuotas->id);


        // Obtener el convenio relacionado
        $convenio = Convenio::findOrFail($request->convenio_id);


    }

    /**
        * Registrar un pago.
        */
    public function registrarPago(Request $request)
    {
        $request->validate([
            'cuotas' => 'required|array',
            'cuotas.*' => 'exists:cuotas,id',
            'monto_total' => 'required|numeric',
            'tipo_pago' => 'required|string',
            'metodo_pago' => 'required|string',
            'monto_efectivo' => 'nullable|numeric',
            'monto_transferencia' => 'nullable|numeric',
            'referencia_transferencia' => 'nullable|string',
            'monto_tarjeta' => 'nullable|numeric',
            'numero_baucher' => 'nullable|string',
            'monto_cheque' => 'nullable|numeric',
            'numero_cheque' => 'nullable|string',
            'monto_deposito' => 'nullable|numeric',
            'no_deposito' => 'nullable|string',
            'banco_deposito' => 'nullable|string',
        ]);

        $convenio = Convenio::findOrFail($request->convenio_id);

        // Montos por método
        $montos = [
            'efectivo' => $request->monto_efectivo ?? 0,
            'transferencia' => $request->monto_transferencia ?? 0,
            'tarjeta' => $request->monto_tarjeta ?? 0,
            'cheque' => $request->monto_cheque ?? 0,
            'deposito' => $request->monto_deposito ?? 0,
        ];

        $montoDisponibleTotal = array_sum($montos);

        // Verificar total de cuotas seleccionadas
        $montoCuotasSeleccionadas = Cuota::whereIn('id', $request->cuotas)->sum('monto_cuota');

        if ($montoDisponibleTotal > $montoCuotasSeleccionadas) {
            return redirect()
                ->route('pagos.resultados_estudiante', ['convenio' => $convenio])
                ->withErrors([
                    'monto_total' => 'La cantidad ingresada supera el total a pagar de las cuotas seleccionadas (Q' . number_format($montoCuotasSeleccionadas, 2) . ').'
                ])->withInput();
        }

        if ($request->tipo_pago === "completo" && ($montoDisponibleTotal != $montoCuotasSeleccionadas)) {
            return redirect()
                ->route('pagos.resultados_estudiante', ['convenio' => $convenio])
                ->withErrors([
                    'completo' => 'Ha seleccionado Pago Completo pero la cantidad ingresada es distinta al monto total de las cuotas.'
                ])->withInput();
        }

        // Detalles extra por método
        $detalles = [
            'transferencia' => [
                'referencia' => $request->referencia_transferencia,
                'banco' => $request->banco_deposito,
            ],
            'tarjeta' => [
                'baucher' => $request->numero_baucher,
            ],
            'cheque' => [
                'numero' => $request->numero_cheque,
            ],
            'deposito' => [
                'numero_boleta' => $request->no_deposito,
                'banco' => $request->banco_deposito,
            ]
        ];

        $fechaPago = Carbon::now()->format('d/m/Y H:i');

        // Procesar cuotas
        foreach ($request->cuotas as $index => $cuotaId) {

            $cuota = Cuota::findOrFail($cuotaId);

            if ($montoDisponibleTotal <= 0) {
                break;
            }

            $exonerar = $request->exonerar[$index] ?? 'No';

            // Monto pendiente de esta cuota
            $montoPendiente = min($cuota->monto_cuota, $montoDisponibleTotal);

            // Crear registro de pago
            $pago = Pago::create([
                'convenio_id' => $cuota->convenio_id,
                'tipo_pago' => $request->tipo_pago,
                'monto' => $montoPendiente,
                'exonerar' => $exonerar,
                'fecha_pago' => now(),
            ]);

            // Distribuir por métodos
            foreach ($montos as $metodo => &$disponible) {
                if ($disponible <= 0 || $montoPendiente <= 0) {
                    continue;
                }

                $montoMetodo = min($disponible, $montoPendiente);

                $pago->pagoMetodos()->create([
                    'metodo_pago' => $metodo,
                    'monto' => $montoMetodo,
                    'detalles' => $detalles[$metodo] ?? null,
                ]);

                $disponible -= $montoMetodo;
                $montoPendiente -= $montoMetodo;
                $montoDisponibleTotal -= $montoMetodo;
            }

            // Relación cuota-pago
            $pago->cuotas()->attach($cuota->id);

            // Actualizar cuota
            $cuota->monto_cuota -= $pago->monto;
            $cuota->estado = $cuota->monto_cuota <= 0 ? 'pagada' : 'pendiente';
            $cuota->monto_cuota = max(0, $cuota->monto_cuota);
            $cuota->save();

            $estudiante = optional(optional($cuota->convenio)->estudiante2)->persona;
            $nombreEstudiante = $estudiante
            ? $estudiante->nombres . ' ' . $estudiante->apellidos
            : 'Estudiante desconocido';

            // === NUEVA NOTIFICACIÓN INDIVIDUAL POR CUOTA ===

            $mesPago = $cuota->fecha_vencimiento->translatedFormat('F');
            $añoPago = $cuota->fecha_vencimiento->format('Y');
            $nombreProducto = optional(optional($cuota->productoSeleccionado)->detalle)->nombre ?? 'Producto sin detalle';

            $mensaje = "Pago realizado al estudiante: $nombreEstudiante"."-"." $nombreProducto $mesPago $añoPago"
               . " | Monto pagado: Q" . number_format($pago->monto, 2)
               . " | Fecha: $fechaPago";

            // Enviar a cada responsable de la familia
            $familia = optional($convenio->estudiante2)->familia;

            $usuariosIds = collect([
                optional($familia->padre)->cms_users_id,
                optional($familia->madre)->cms_users_id,
                optional($familia->encargado)->cms_users_id
            ])->filter()->unique();

            foreach ($usuariosIds as $userId) {
                CmsNotification::create([
                    'content' => $mensaje,
                    'id_cms_users' => $userId,
                    'url' => null,
                    'is_read' => 0,
                ]);
            }
        }

        return redirect()
            ->route('ruta.facturar', ['id' => $convenio->id])
            ->with('success', 'Pagos generados correctamente');
    }







    public function registrarPagoFamiliar(Request $request)
    {
        $request->validate([
            'cuotas' => 'required|array',
            'cuotas.*' => 'exists:cuotas,id',
            'monto_total' => 'required|numeric',
            'tipo_pago' => 'required|string',
            'metodo_pago' => 'required|string',
            'monto_efectivo' => 'nullable|numeric',
            'monto_transferencia' => 'nullable|numeric',
            'referencia_transferencia' => 'nullable|string',
            'monto_tarjeta' => 'nullable|numeric',
            'numero_baucher' => 'nullable|string',
            'monto_cheque' => 'nullable|numeric',
            'numero_cheque' => 'nullable|string',
            'monto_deposito' => 'nullable|numeric',
            'no_deposito' => 'nullable|string',
            'banco_deposito' => 'nullable|string',
        ]);

        $conveniosPagados = [];

        // Montos disponibles por método
        $montosDisponibles = [
            'efectivo' => $request->monto_efectivo ?? 0,
            'transferencia' => $request->monto_transferencia ?? 0,
            'tarjeta' => $request->monto_tarjeta ?? 0,
            'cheque' => $request->monto_cheque ?? 0,
            'deposito' => $request->monto_deposito ?? 0,
        ];

        $montoDisponibleTotal = array_sum($montosDisponibles);

        // Total de cuotas seleccionadas
        $totalCuotasSeleccionadas = Cuota::whereIn('id', $request->cuotas)->sum('monto_cuota');

        // Validaciones
        if ($montoDisponibleTotal > $totalCuotasSeleccionadas) {
            return redirect()->route('pagos.resultados_familia', ['codigo_familiar' => $request->codigo_familiar])
                ->withErrors([
                    'monto_total' => 'La cantidad ingresada supera el total a pagar de las cuotas seleccionadas (Q' . number_format($totalCuotasSeleccionadas, 2) . ').'
                ])->withInput();
        }

        if ($request->tipo_pago === "completo" && ($montoDisponibleTotal < $totalCuotasSeleccionadas)) {
            return redirect()->route('pagos.resultados_familia', ['codigo_familiar' => $request->codigo_familiar])
                ->withErrors([
                    'completo' => 'Ha seleccionado Pago Completo pero la cantidad ingresada es inferior al total a pagar.'
                ])->withInput();
        }

        $fechaPago = Carbon::now()->format('d/m/Y H:i');

        // === PROCESAR CADA CUOTA ===
        foreach ($request->cuotas as $index => $cuotaId) {

            $cuota = Cuota::findOrFail($cuotaId);

            if ($montoDisponibleTotal <= 0) {
                break;
            }

            $convenioId = $cuota->convenio_id;

            if (!in_array($convenioId, $conveniosPagados)) {
                $conveniosPagados[] = $convenioId;
            }

            $exonerar = $request->exonerar[$cuotaId] ?? 'No';

            // Cuánto se pagará de esta cuota
            $montoPendiente = min($cuota->monto_cuota, $montoDisponibleTotal);

            // Crear pago
            $pago = Pago::create([
                'convenio_id' => $convenioId,
                'tipo_pago' => $request->tipo_pago,
                'monto' => $montoPendiente,
                'exonerar' => $exonerar,
                'fecha_pago' => now(),
            ]);

            // Distribución entre métodos
            foreach ($montosDisponibles as $metodo => &$disponible) {
                if ($disponible <= 0 || $montoPendiente <= 0) {
                    continue;
                }

                $montoMetodo = min($disponible, $montoPendiente);

                $pago->pagoMetodos()->create([
                    'metodo_pago' => $metodo,
                    'monto' => $montoMetodo,
                    'detalles' => [
                        'referencia' => $request->referencia_transferencia,
                        'banco' => $request->banco_deposito,
                        'baucher' => $request->numero_baucher,
                        'numero_cheque' => $request->numero_cheque,
                        'numero_boleta' => $request->no_deposito,
                    ],
                ]);

                $disponible -= $montoMetodo;
                $montoPendiente -= $montoMetodo;
                $montoDisponibleTotal -= $montoMetodo;
            }

            // Asociar cuota al pago
            $pago->cuotas()->attach($cuota->id);

            // Actualizar cuota
            $cuota->monto_cuota -= $pago->monto;
            $cuota->estado = $cuota->monto_cuota <= 0 ? 'pagada' : 'pendiente';
            $cuota->monto_cuota = max(0, $cuota->monto_cuota);
            $cuota->save();
            $mesPago = $cuota->fecha_vencimiento->translatedFormat('F');
            $añoPago = $cuota->fecha_vencimiento->format('Y');

            $estudiante = optional(optional($cuota->convenio)->estudiante2)->persona;
            $nombreEstudiante = $estudiante
            ? $estudiante->nombres . ' ' . $estudiante->apellidos
            : 'Estudiante desconocido';


            // === NOTIFICACIÓN INDIVIDUAL ===

            $mesPago = $cuota->fecha_vencimiento->translatedFormat('F');
            $añoPago = $cuota->fecha_vencimiento->format('Y');
            $nombreProducto = optional(optional($cuota->productoSeleccionado)->detalle)->nombre ?? 'Producto sin detalle';

            $mensaje = "Pago realizado al estudiante: $nombreEstudiante"."-"." $nombreProducto $mesPago $añoPago"
                . " | Monto pagado: Q" . number_format($pago->monto, 2)
                . " | Fecha: $fechaPago";

            // Identificar familia
            $convenio = Convenio::find($convenioId);
            $familia = optional($convenio->estudiante2)->familia;

            $usuariosIds = collect([
                optional($familia->padre)->cms_users_id,
                optional($familia->madre)->cms_users_id,
                optional($familia->encargado)->cms_users_id,
            ])->filter()->unique();

            foreach ($usuariosIds as $userId) {
                CmsNotification::create([
                    'content' => $mensaje,
                    'id_cms_users' => $userId,
                    'url' => null,
                    'is_read' => 0,
                ]);
            }
        }

        $convenioIds = Cuota::whereIn('id', $request->cuotas)
            ->pluck('convenio_id')
            ->unique()
            ->toArray();

        return redirect()->route('ruta.facturarfamilia', ['ids' => implode(',', $convenioIds)])
            ->with('success', 'Pagos generados correctamente');
    }


    private function asociarMetodoPago($pago, $request, &$montosDisponibles, $montoPendiente)
    {
        $metodos = ['efectivo', 'transferencia', 'tarjeta', 'cheque', 'deposito'];

        foreach ($metodos as $metodo) {
            if ($montoPendiente <= 0) {
                break;
            }

            $montoDisponible = $montosDisponibles[$metodo] ?? 0;

            if ($montoDisponible > 0) {
                $montoUsado = min($montoDisponible, $montoPendiente);

                $detalles = [];

                switch ($metodo) {
                    case 'transferencia':
                        $detalles = [
                            'referencia' => $request->referencia_transferencia,
                            'banco' => $request->banco_deposito,
                        ];
                        break;
                    case 'tarjeta':
                        $detalles = [
                            'baucher' => $request->numero_baucher,
                        ];
                        break;
                    case 'cheque':
                        $detalles = [
                            'numero' => $request->numero_cheque,
                        ];
                        break;
                    case 'deposito':
                        $detalles = [
                            'numero_boleta' => $request->no_deposito,
                            'banco' => $request->banco_deposito,
                        ];
                        break;
                }

                $pago->pagoMetodos()->create([
                    'metodo_pago' => $metodo,
                    'monto' => $montoUsado,
                    'detalles' => $detalles,
                ]);

                // Actualizar montos disponibles y lo pendiente por pagar
                $montosDisponibles[$metodo] -= $montoUsado;
                $montoPendiente -= $montoUsado;
            }
        }
    }


    public function verPagos()
    {
        // Recuperar todos los pagos con las cuotas asociadas, productos seleccionados, y métodos de pago
        // $pagos = Pago::with(['cuotas.productoSeleccionado.detalle', 'pagoMetodos'])->get();

        $pagos = Pago::with(['cuotas.productoSeleccionado.detalle', 'pagoMetodos'])
        ->whereDoesntHave('facturaEmitida')   // Filtra solo los pagos sin factura emitida
        ->whereDoesntHave('reciboEmitido')    // Filtra solo los pagos sin recibo emitido
        ->get();

        // Formatear los datos para la vista
        $datosPagos = $pagos->map(function ($pago) {
            return [
                'id' => $pago->id,
                'convenio_id' => $pago->convenio_id,
                'monto' => $pago->monto,
                'fecha_pago' => $pago->fecha_pago,
                'inscripcion' => $pago->cuotas->first()->productoSeleccionado->inscripcion,
                'tipo_pago' => $pago->tipo_pago,
                'cuotas' => $pago->cuotas->map(function ($cuota) {
                    return [
                        'id' => $cuota->id,
                        'monto_cuota' => $cuota->monto_cuota,
                        'estado' => $cuota->estado,
                        'producto_seleccionado' => $cuota->productoSeleccionado ? [
                            'id' => $cuota->productoSeleccionado->id,
                            'nombre' => $cuota->productoSeleccionado->detalle->nombre ?? 'N/A',
                            'precio' => $cuota->productoSeleccionado->precio,
                        ] : null,
                    ];
                }),
                'metodos_de_pago' => $pago->pagoMetodos->map(function ($metodo) {
                    return [
                        'metodo' => $metodo->metodo_pago,
                        'monto' => $metodo->monto,
                        'detalles' => $metodo->detalles,
                    ];
                }),
            ];
        });

        // Retornar los datos a la vista
        return view('pagos.ver', compact('datosPagos'));
    }



    public function facturarPagos($convenioId)
    {
        // Recuperar todos los pagos con las cuotas asociadas, productos seleccionados, y métodos de pago
        // $pagos = Pago::with(['cuotas.productoSeleccionado.detalle', 'pagoMetodos'])->get();

        $pagos = Pago::with(['cuotas.productoSeleccionado.detalle', 'pagoMetodos'])
        ->where('convenio_id', $convenioId)
        ->whereDoesntHave('facturaEmitida')   // Filtra solo los pagos sin factura emitida
        ->whereDoesntHave('reciboEmitido')
        ->whereDoesntHave('recibointernoEmitido')   // Filtra solo los pagos sin recibo emitido
        ->get();

        // Formatear los datos para la vista
        $datosPagos = $pagos->map(function ($pago) {

            setlocale(LC_TIME, 'es_ES.UTF-8');
            return [
                'id' => $pago->id,
                'convenio_id' => $pago->convenio_id,
                'monto' => $pago->monto,
                'fecha_pago' => $pago->fecha_pago,
                'inscripcion' => $pago->cuotas->first()->productoSeleccionado->inscripcion,
                'tipo_pago' => $pago->tipo_pago,
                'exonerar' => $pago->exonerar,

                'cuotas' => $pago->cuotas->map(function ($cuota) {
                    return [
                        'id' => $cuota->id,
                        'mes_vencimiento' => \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F'),
                        'anio_vencimiento' => \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('Y'),
                        'monto_cuota' => $cuota->monto_cuota,
                        'estado' => $cuota->estado,
                        'producto_seleccionado' => $cuota->productoSeleccionado ? [
                            'id' => $cuota->productoSeleccionado->id,
                            'nombre' => $cuota->productoSeleccionado->detalle->nombre ?? 'N/A',
                            'comprobante' => $cuota->productoSeleccionado->detalle->tipo_comprobante ?? 'N/A',
                            'BienoServicio' => $cuota->productoSeleccionado->detalle->tipo_producto ?? 'N/A',
                            'precio' => $cuota->productoSeleccionado->precio,

                        ] : null,
                    ];
                }),
                'metodos_de_pago' => $pago->pagoMetodos->map(function ($metodo) {
                    return [
                        'metodo' => $metodo->metodo_pago,
                        'monto' => $metodo->monto,
                        'detalles' => $metodo->detalles,
                    ];
                }),
            ];
        });



        // Retornar los datos a la vista
        return view('pagos.comprobantePagos', compact('datosPagos'));
    }




    public function facturarPagosFamiliar($convenioId)
    {
        $convenioIdsArray = explode(',', $convenioId); // Convertir en array

        $pagos = Pago::with(['cuotas.productoSeleccionado.detalle', 'pagoMetodos', 'convenio.estudiante.persona'])
            ->whereIn('convenio_id', $convenioIdsArray) // Buscar todos los convenios
            ->whereDoesntHave('facturaEmitida')
            ->whereDoesntHave('reciboEmitido')
            ->whereDoesntHave('recibointernoEmitido')
            ->get();

        // Formatear los pagos antes de enviarlos a la vista

        // dd( $pagos);
        $pagosAgrupados = $this->formatearPagos($pagos);


        return view('pagos.comprobantePagosFamiliar', compact('pagosAgrupados'));
    }

    private function formatearPagos($pagos)
    {
        return $pagos->map(function ($pago) {
            return [
                'id' => $pago->id,
                'convenio_id' => $pago->convenio_id,
                'monto' => $pago->monto,
                'fecha_pago' => $pago->fecha_pago,
                'tipo_pago' => $pago->tipo_pago,
                'exonerar' => $pago->exonerar,

               'alumnos' => $pago->convenio->estudiante->map(function ($estudiante) {
                   return optional($estudiante->persona)->nombres." ".($estudiante->persona)->apellidos;
               })->filter()->values()->toArray(),
                'cuotas' => $this->formatearCuotas($pago->cuotas),
                'metodos_de_pago' => $this->formatearMetodosPago($pago->pagoMetodos),
            ];
        });
    }

    private function formatearCuotas($cuotas)
    {
        return optional($cuotas)->map(function ($cuota) {
            return [
                'id' => $cuota->id,
                 'mes_vencimiento' => \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F'),
                        'anio_vencimiento' => \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('Y'),
                'monto_cuota' => $cuota->monto_cuota,
                'estado' => $cuota->estado,
                'producto_seleccionado' => $cuota->productoSeleccionado ? [
                    'id' => $cuota->productoSeleccionado->id,
                    'nombre' => $cuota->productoSeleccionado->detalle->nombre ?? 'N/A',
                    'comprobante' => $cuota->productoSeleccionado->detalle->tipo_comprobante ?? 'N/A',
                    'BienoServicio' => $cuota->productoSeleccionado->detalle->tipo_producto ?? 'N/A',
                    'precio' => $cuota->productoSeleccionado->precio,
                ] : null,
            ];
        }) ?? collect();
    }

    private function formatearMetodosPago($metodosPago)
    {
        return $metodosPago->map(function ($metodo) {
            return [
                'metodo' => $metodo->metodo_pago,
                'monto' => $metodo->monto,
                'detalles' => $metodo->detalles,
            ];
        });
    }





    public function generarPago(Request $request)
    {

        //  dd($request->all());

        $pagosFactura = $request->input('pagosFactura', []);
        $pagosRecibo = $request->input('pagosRecibo', []);
        $pagosReciboInterno = $request->input('pagosReciboInterno', []);

        $pagosIds = array_merge($pagosFactura, $pagosRecibo, $pagosReciboInterno);

        if (empty($pagosIds)) {
            return back()->with('error', 'Debe seleccionar al menos un pago.');
        }



        $pagos = Pago::with([
            'cuotas.productoSeleccionado',
            'convenio.inscripcion.estudiante.persona',
            'convenio.inscripcion.estudiante.asignacion.gestiones',
            'convenio.inscripcion.estudiante.familia',
            'pagoMetodos'
        ])->whereIn('id', $pagosIds)->get();

        if ($pagos->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron pagos con los IDs proporcionados.');
        }


        $metodos = [];
        $montos = [];
        $documentos = [];
        $bancos = [];

        $pagos->each(function ($pago) use (&$metodos, &$montos, &$documentos, &$bancos) {
            $pago->pagoMetodos->each(function ($metodo) use (&$metodos, &$montos, &$documentos, &$bancos) {
                $metodos[] = Str::title($metodo->metodo_pago);
                $montos[] = number_format($metodo->monto, 2);

                // Asegurar que detalles sea un array válido
                $detalles = is_string($metodo->detalles) ? json_decode($metodo->detalles, true) : $metodo->detalles;
                $detalles = is_array($detalles) ? $detalles : [];

                $detalleString = '';
                $bancoString = 'N/A';

                if ($metodo->metodo_pago === 'transferencia') {
                    if (isset($detalles['referencia'])) {
                        $detalleString = "Ref: {$detalles['referencia']}";
                    }
                    if (isset($detalles['banco'])) {
                        $bancoString = $detalles['banco'];
                    }
                } elseif ($metodo->metodo_pago === 'tarjeta' && isset($detalles['baucher'])) {
                    $detalleString = "Baucher: {$detalles['baucher']}";
                } elseif ($metodo->metodo_pago === 'cheque' && isset($detalles['numero'])) {
                    $detalleString = "Cheque: {$detalles['numero']}";
                } elseif ($metodo->metodo_pago === 'deposito') {
                    if (isset($detalles['numero_boleta'])) {
                        $detalleString = "Boleta: {$detalles['numero_boleta']}";
                    }
                    if (isset($detalles['banco'])) {
                        $bancoString = $detalles['banco'];
                    }
                }

                $documentos[] = $detalleString ?: 'N/A';
                $bancos[] = $bancoString;
            });
        });

        // Convertir a strings separados por coma
        $metodosPagoString = implode(', ', $metodos);
        $montosString = implode(', ', $montos);
        $documentosString = implode(', ', $documentos);
        $bancosString = implode(', ', $bancos);

        /* dd([
             'Metodo de Pago' => $metodosPagoString,
             'Monto' => $montosString,
             'Documentos' => $documentosString,
             'Bancos' => $bancosString
         ]);*/

        $accion = $request->input('accion');

        switch ($accion) {
            case 'generarFactura':

                $total = 0;
                $montoGravable = 0;
                $montoImpuesto = 0;
                $porcentajeIVA = 0.12; // 12% de IVA

                $data = [

                   'resolucion' =>  $pagos->first()->convenio->inscripcion->estudiante->asignacion->gestiones->resolucion_DIACO,
                   'Metodo_Pago' => $metodosPagoString,
                   'Monto' => $montosString,
                   'Documentos' => $documentosString,
                   'Bancos' => $bancosString,
                    'estudiante' => $pagos->first()->convenio->inscripcion->estudiante->persona->nombres . ' ' .
                        $pagos->first()->convenio->inscripcion->estudiante->persona->apellidos,
                    'carnet' => $pagos->first()->convenio->inscripcion->estudiante->carnet,
                    'codigo_familiar' => $pagos->first()->convenio->inscripcion->estudiante->familia->codigo_familiar,
                    'nombre_familiar' => $pagos->first()->convenio->inscripcion->estudiante->familia->nombre_familiar,

                    'tipoespecial' => $request->tipo_identificacion,
                    'nit' => $request->nit,
                    'cliente' => $request->cliente,
                    'direccion' => $request->direccion,
                    'pago_id' => $pagosFactura,
                    'items' => []
                ];

                $productosAgrupados = [];

                foreach ($pagos as $pago) {
                    foreach ($pago->cuotas as $cuota) {
                        if ($cuota->productoSeleccionado) {

                            $productoNombre = $cuota->productoSeleccionado->detalle->nombre;


                            if ($productoNombre=== 'Mensualidad') {
                                $mes = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F');
                                $anio = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('Y');
                                $descripcion = $productoNombre . ' ' .$mes.' '.$anio;
                            } else {
                                $descripcion = $productoNombre;

                            }


                            $precioUnitario = $pago->pagoMetodos->first()->monto;

                            // Agrupar productos por su descripción
                            if (!isset($productosAgrupados[$descripcion])) {
                                $productosAgrupados[$descripcion] = [
                                    'descripcion' => $descripcion,
                                    'cantidad' => 1, // Siempre 1
                                    'precioTotal' => 0
                                ];
                            }

                            // Sumar el monto de las cuotas del mismo producto
                            $productosAgrupados[$descripcion]['precioTotal'] += $precioUnitario;
                        }
                    }
                }

                foreach ($productosAgrupados as $producto) {
                    $precio = $producto['precioTotal'];
                    $montoGravableItem = $precio / 1.12;
                    $montoImpuestoItem = $precio - $montoGravableItem;

                    $total += $precio;
                    $montoGravable += $montoGravableItem;
                    $montoImpuesto += $montoImpuestoItem;

                    $data['items'][] = [
                        'BienOServicio' => 'S',
                        'NumeroLinea' => count($data['items']) + 1,
                        'Cantidad' => 1,
                        'UnidadMedida' => 'UN',
                        'Descripcion' => $producto['descripcion'],
                        'PrecioUnitario' => number_format($precio, 3, '.', ''),
                        'Precio' => number_format($precio, 3, '.', ''),
                        'Descuento' => number_format(0, 3, '.', ''),
                        'MontoGravable' => number_format($montoGravableItem, 3, '.', ''),
                        'MontoImpuesto' => number_format($montoImpuestoItem, 3, '.', ''),
                        'Total' => number_format($precio, 3, '.', ''),
                    ];
                }

                $data['total'] = number_format($total, 3, '.', '');
                $data['montoGravable'] = number_format($montoGravable, 3, '.', '');
                $data['montoImpuesto'] = number_format($montoImpuesto, 3, '.', '');

                //dd($data);

                $facturaController = new FacturaController();
                return $facturaController->generarFactura(new Request($data));

            case 'generarRecibo':

                // Preparar los ítems para el recibo
                $items = [];


                $total = 0;
                $montoGravable = 0;
                $montoImpuesto = 0;
                $porcentajeIVA = 0.12; // 12% de IVA


                $data = [
                   'Metodo_Pago' => $metodosPagoString,
                   'Monto' => $montosString,
                   'Documentos' => $documentosString,
                   'Bancos' => $bancosString,
                   'resolucion' =>  $pagos->first()->convenio->inscripcion->estudiante->asignacion->gestiones->resolucion_DIACO,
                    'estudiante' => $pagos->first()->convenio->inscripcion->estudiante->persona->nombres . ' ' .
                        $pagos->first()->convenio->inscripcion->estudiante->persona->apellidos,
                    'carnet' => $pagos->first()->convenio->inscripcion->estudiante->carnet,
                    'codigo_familiar' => $pagos->first()->convenio->inscripcion->estudiante->familia->codigo_familiar,
                    'nombre_familiar' => $pagos->first()->convenio->inscripcion->estudiante->familia->nombre_familiar,
                    'nit' => $request->nit,
                    'tipoespecial' => $request->tipo_identificacion,
                    'cliente' => $request->cliente,
                    'direccion' => $request->direccion,
                    'pago_id' => $pagosRecibo,
                    'items' => []
                ];

                $productosAgrupados = [];

                foreach ($pagos as $pago) {
                    foreach ($pago->cuotas as $cuota) {
                        if ($cuota->productoSeleccionado) {
                            $productoNombre = $cuota->productoSeleccionado->detalle->nombre;

                            if ($productoNombre === 'Mensualidad') {
                                $mes = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F');
                                $anio = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('Y');
                                $descripcion = $productoNombre . ' ' .$mes.' '.$anio;
                            } else {
                                $descripcion = $productoNombre;
                            }
                            $precioUnitario = $pago->pagoMetodos->first()->monto;

                            // Agrupar productos por su descripción
                            if (!isset($productosAgrupados[$descripcion])) {
                                $productosAgrupados[$descripcion] = [
                                    'descripcion' => $descripcion,
                                    'cantidad' => 1, // Siempre 1
                                    'precioTotal' => 0
                                ];
                            }

                            // Sumar el monto de las cuotas del mismo producto
                            $productosAgrupados[$descripcion]['precioTotal'] += $precioUnitario;
                        }
                    }
                }

                foreach ($productosAgrupados as $producto) {
                    $precio = $producto['precioTotal'];
                    $montoGravableItem = $precio / 1.12;
                    $montoImpuestoItem = $precio - $montoGravableItem;

                    $total += $precio;
                    $montoGravable += $montoGravableItem;
                    $montoImpuesto += $montoImpuestoItem;

                    $data['items'][] = [
                        'BienOServicio' => 'S',
                        'NumeroLinea' => count($data['items']) + 1,
                        'Cantidad' => 1,
                        'UnidadMedida' => 'UN',
                        'Descripcion' => $producto['descripcion'],
                        'PrecioUnitario' => number_format($precio, 3, '.', ''),
                        'Precio' => number_format($precio, 3, '.', ''),
                        'Descuento' => number_format(0, 3, '.', ''),
                        'MontoGravable' => number_format($montoGravableItem, 3, '.', ''),
                        'MontoImpuesto' => number_format($montoImpuestoItem, 3, '.', ''),
                        'Total' => number_format($precio, 3, '.', ''),
                    ];
                }

                $data['total'] = number_format($total, 3, '.', '');
                $data['montoGravable'] = number_format($montoGravable, 3, '.', '');
                $data['montoImpuesto'] = number_format($montoImpuesto, 3, '.', '');

                //dd($data);

                $reciboController = new ReciboController();
                return $reciboController->generarRecibo(new Request($data));

            case 'generarReciboInterno':
                $items = [];


                $total = 0;
                $montoGravable = 0;
                $montoImpuesto = 0;
                $porcentajeIVA = 0.12; // 12% de IVA

                $data = [
                    'Metodo_Pago' => $metodosPagoString,
                    'Monto' => $montosString,
                    'Documentos' => $documentosString,
                    'Bancos' => $bancosString,
                    'resolucion' =>  $pagos->first()->convenio->inscripcion->estudiante->asignacion->gestiones->resolucion_DIACO,
                    'estudiante' => $pagos->first()->convenio->inscripcion->estudiante->persona->nombres . ' ' .
                        $pagos->first()->convenio->inscripcion->estudiante->persona->apellidos,
                    'carnet' => $pagos->first()->convenio->inscripcion->estudiante->carnet,
                    'codigo_familiar' => $pagos->first()->convenio->inscripcion->estudiante->familia->codigo_familiar,
                    'nombre_familiar' => $pagos->first()->convenio->inscripcion->estudiante->familia->nombre_familiar,
                    'nit' => $request->nit,
                    'cliente' => $request->cliente,
                    'direccion' => $request->direccion,
                    'pago_id' =>  $pagosReciboInterno,
                    'items' => []
                ];

                $productosAgrupados = [];

                foreach ($pagos as $pago) {
                    foreach ($pago->cuotas as $cuota) {
                        if ($cuota->productoSeleccionado) {
                            $productoNombre = $cuota->productoSeleccionado->detalle->nombre;

                            if ($productoNombre === 'Mensualidad') {
                                $mes = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F');
                                $anio = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('Y');
                                $descripcion = $productoNombre . ' ' .$mes.' '.$anio;
                            } else {
                                $descripcion = $productoNombre;
                            }
                            $precioUnitario = $pago->pagoMetodos->first()->monto;

                            // Agrupar productos por su descripción
                            if (!isset($productosAgrupados[$descripcion])) {
                                $productosAgrupados[$descripcion] = [
                                    'descripcion' => $descripcion,
                                    'cantidad' => 1, // Siempre 1
                                    'precioTotal' => 0
                                ];
                            }

                            // Sumar el monto de las cuotas del mismo producto
                            $productosAgrupados[$descripcion]['precioTotal'] += $precioUnitario;
                        }
                    }
                }

                foreach ($productosAgrupados as $producto) {
                    $precio = $producto['precioTotal'];
                    $montoGravableItem = $precio / 1.12;
                    $montoImpuestoItem = $precio - $montoGravableItem;

                    $total += $precio;
                    $montoGravable += $montoGravableItem;
                    $montoImpuesto += $montoImpuestoItem;

                    $data['items'][] = [
                        'BienOServicio' => 'S',
                        'NumeroLinea' => count($data['items']) + 1,
                        'Cantidad' => 1,
                        'UnidadMedida' => 'UN',
                        'Descripcion' => $producto['descripcion'],
                        'PrecioUnitario' => number_format($precio, 3, '.', ''),
                        'Precio' => number_format($precio, 3, '.', ''),
                        'Descuento' => number_format(0, 3, '.', ''),
                        'MontoGravable' => number_format($montoGravableItem, 3, '.', ''),
                        'MontoImpuesto' => number_format($montoImpuestoItem, 3, '.', ''),
                        'Total' => number_format($precio, 3, '.', ''),
                    ];
                }

                $data['total'] = number_format($total, 3, '.', '');
                $data['montoGravable'] = number_format($montoGravable, 3, '.', '');
                $data['montoImpuesto'] = number_format($montoImpuesto, 3, '.', '');

                //dd($data);

                $recibointernoController = new ReciboInternoController();
                return $recibointernoController->generarRecibo(new Request($data));

            default:
                return back()->with('error', 'Acción no válida.');
        }
    }



    public function generarFactura($pagos)
    {
        // Preparar los ítems para la factura combinada
        $data = [
           'estudiante' => $pago->convenio->inscripcion->estudiante->persona->nombres . ' ' . $pago->convenio->inscripcion->estudiante->persona->apellidos,
           'carnet' => $pago->convenio->inscripcion->estudiante->carnet,
           'codigo_familiar' => $pago->convenio->inscripcion->estudiante->familia->codigo_familiar,
           'nombre_familiar' => $pago->convenio->inscripcion->estudiante->familia->nombre_familiar,
           'nit' => $request->nit, // Pasar NIT recibido desde la solicitud
           'cliente' => $request->cliente, // Pasar Cliente
           'direccion' => $request->direccion, // Pasar Dirección

           'items' => []
        ];
        foreach ($request->items as $index => $item) {
            $data['items'][] = [

                'BienOServicio' => 'S',
                'NumeroLinea' => $index + 1,
                'Cantidad' => $item['cantidad'],
                'UnidadMedida' => $item['unidad_medida'] ?? 'UN', // Unidad de medida
                'Descripcion' => $item['descripcion'],
                'PrecioUnitario' => number_format($item['precio_unitario'], 3),
                'Precio' => number_format($item['precio'], 3),
                'Descuento' => number_format($item['descuento'], 3),
                'MontoGravable' => number_format($item['monto_gravable'] ?? 0, 3),
                'MontoImpuesto' => number_format($item['monto_impuesto'] ?? 0, 3),
                'Total' => number_format($item['precio'], 3),
            ];
        }

        $response = Http::post(route('factura.generar'), $data);

        // Retornar la respuesta obtenida de la generación de factura
        return $response->json();

        foreach ($pagos as $pago) {
            foreach ($pago->cuotas as $cuota) {
                if ($cuota->productoSeleccionado) {
                    $descripcion = $cuota->productoSeleccionado->detalle->nombre;

                    // Si ya existe un ítem con la misma descripción, combinarlo
                    if (isset($items[$descripcion])) {
                        $items[$descripcion]['cantidad'] += 1; // Sumar cantidad
                        $items[$descripcion]['precio_unitario'] += $pago->pagoMetodos->first()->monto; // Sumar precio unitario
                        $items[$descripcion]['precio'] += $cuota->monto_cuota; // Sumar el precio total
                        $items[$descripcion]['pagoId'][] = $pago->id; // Agregar el ID del pago
                    } else {
                        // Crear un nuevo ítem si no existe
                        $items[$descripcion] = [
                            'cantidad' => 1, // Inicializar en 1
                            'descripcion' => $descripcion,
                            'precio_unitario' => $pago->pagoMetodos->first()->monto, // Precio unitario
                            'precio' => $cuota->monto_cuota, // Precio total
                            'descuento' => 0, // No hay descuento
                            'pagoId' => [$pago->id], // IDs de los pagos relacionados
                        ];
                    }
                }
            }
        }

        // Convertir los ítems en un array indexado
        $items = array_values($items);

        // Pasar los datos a la vista para generar la factura
        /* return view('pagos.factura', [
             'items' => $items,
             'pagos' => $pagos, // Aquí pasamos todos los pagos correctamente
         ]);*/
    }


    public function generarRecibo($pagos)
    {
        // Preparar los ítems para el recibo
        $items = [];


        $total = 0;
        $montoGravable = 0;
        $montoImpuesto = 0;
        $porcentajeIVA = 0.12; // 12% de IVA

        $data = [
            'estudiante' => $pagos->first()->convenio->inscripcion->estudiante->persona->nombres . ' ' .
                $pagos->first()->convenio->inscripcion->estudiante->persona->apellidos,
            'carnet' => $pagos->first()->convenio->inscripcion->estudiante->carnet,
            'codigo_familiar' => $pagos->first()->convenio->inscripcion->estudiante->familia->codigo_familiar,
            'nombre_familiar' => $pagos->first()->convenio->inscripcion->estudiante->familia->nombre_familiar,
            'nit' => $request->nit,
            'cliente' => $request->cliente,
            'direccion' => $request->direccion,
            'pago_id' => $pagosFactura,
            'items' => []
        ];

        $productosAgrupados = [];

        foreach ($pagos as $pago) {
            foreach ($pago->cuotas as $cuota) {
                if ($cuota->productoSeleccionado) {
                    $descripcion = $cuota->productoSeleccionado->detalle->nombre;
                    $precioUnitario = $pago->pagoMetodos->first()->monto;

                    // Agrupar productos por su descripción
                    if (!isset($productosAgrupados[$descripcion])) {
                        $productosAgrupados[$descripcion] = [
                            'descripcion' => $descripcion,
                            'cantidad' => 1, // Siempre 1
                            'precioTotal' => 0
                        ];
                    }

                    // Sumar el monto de las cuotas del mismo producto
                    $productosAgrupados[$descripcion]['precioTotal'] += $precioUnitario;
                }
            }
        }

        foreach ($productosAgrupados as $producto) {
            $precio = $producto['precioTotal'];
            $montoGravableItem = $precio / 1.12;
            $montoImpuestoItem = $precio - $montoGravableItem;

            $total += $precio;
            $montoGravable += $montoGravableItem;
            $montoImpuesto += $montoImpuestoItem;

            $data['items'][] = [
                'BienOServicio' => 'S',
                'NumeroLinea' => count($data['items']) + 1,
                'Cantidad' => 1,
                'UnidadMedida' => 'UN',
                'Descripcion' => $producto['descripcion'],
                'PrecioUnitario' => number_format($precio, 3, '.', ''),
                'Precio' => number_format($precio, 3, '.', ''),
                'Descuento' => number_format(0, 3, '.', ''),
                'MontoGravable' => number_format($montoGravableItem, 3, '.', ''),
                'MontoImpuesto' => number_format($montoImpuestoItem, 3, '.', ''),
                'Total' => number_format($precio, 3, '.', ''),
            ];
        }

        $data['total'] = number_format($total, 3, '.', '');
        $data['montoGravable'] = number_format($montoGravable, 3, '.', '');
        $data['montoImpuesto'] = number_format($montoImpuesto, 3, '.', '');

        //dd($data);

        $reciboController = new ReciboController();
        return $reciboController->generarRecibo(new Request($data));
    }

    public function generarReciboInterno($pagos)
    {
        // Preparar los ítems para el recibo interno
        $items = [];

        foreach ($pagos as $pago) {
            foreach ($pago->cuotas as $cuota) {
                if ($cuota->productoSeleccionado) {
                    $descripcion = $cuota->productoSeleccionado->detalle->nombre;

                    // Si ya existe un ítem con la misma descripción, combinarlo
                    if (isset($items[$descripcion])) {
                        $items[$descripcion]['precio_unitario'] += $pago->pagoMetodos->first()->monto; // Sumar precio unitario
                        $items[$descripcion]['precio'] += $cuota->monto_cuota; // Sumar el precio total
                        $items[$descripcion]['pagoId'][] = $pago->id; // Agregar el ID del pago
                    } else {
                        // Crear un nuevo ítem si no existe
                        $items[$descripcion] = [
                            'cantidad' => 1, // Siempre 1 inicialmente
                            'descripcion' => $descripcion,
                            'precio_unitario' => $pago->pagoMetodos->first()->monto, // Precio unitario
                            'precio' => $cuota->monto_cuota, // Monto total del pago
                            'descuento' => 0, // No hay descuento
                            'pagoId' => [$pago->id], // IDs de los pagos relacionados
                        ];
                    }
                }
            }
        }

        // Convertir los ítems en un array indexado
        $items = array_values($items);



        // Pasar los datos a la vista para generar el recibo interno
        return view('pagos.recibointerno', [
            'items' => $items,
            'pagos' => $pagos,
        ]);
    }


    public function consolidar(Request $request)
    {
        $pagosSeleccionados = $request->input('pagos', []);

        if (empty($pagosSeleccionados)) {
            return redirect()->back()->with('error', 'Debes seleccionar al menos un abono para consolidar.');
        }

        // Obtener los pagos seleccionados
        $pagos = Pago::whereIn('id', $pagosSeleccionados)->get();

        // Consolidar los pagos (sumar montos, actualizar estado, etc.)
        $montoTotal = $pagos->sum('monto');
        // Generar la factura consolidada (implementa tu lógica aquí)
        Factura::create([
            'monto_total' => $montoTotal,
            'detalle' => 'Consolidación de pagos',
            'pagos_ids' => json_encode($pagosSeleccionados),
        ]);
        return redirect()->back()->with('success', 'Pagos consolidados y factura generada correctamente.');
    }

    public function generarPDF($id)
    {
        $convenio = Convenio::with([
            'inscripcion.estudiante.persona',
            'inscripcion.estudiante.cgshges.grados',
            'inscripcion.estudiante.cgshges.cursos',
            'inscripcion.estudiante.cgshges.jornadas.jornada',
            'inscripcion.paquete',
            'cuotas.productoSeleccionado.detalle'
        ])->findOrFail($id);

        \Carbon\Carbon::setLocale('es');



        $pdf =  \PDF::loadView('pagos.pdf_estado_financiero', compact('convenio'));
        return $pdf->download('estado_financiero.pdf');
    }


    public static function generarResultadoDesdeCodigoFamiliar($codigo_familiar)
    {
        // Buscar la familia por su código familiar
        $familias = Familia::where('codigo_familiar', $codigo_familiar)
        ->with([
            'muchosestudiantes' => function ($query) {
                $query->with([
                    'persona',
                    'convenios' => function ($query) {
                        $query->where('estado', 'activo')
                              ->with([
                                  'cuotas' => function ($query) {
                                      $query->where('estado', 'pendiente');
                                  },
                                  'inscripcion.productosSeleccionados.detalle'
                              ]);
                    }
                ]);
            }
        ])
        ->get();

        // Agrupar por codigo_familiar
        $resultado = $familias->groupBy('codigo_familiar')->map(function ($grupo) {
            // Unir todos los estudiantes en una sola colección
            $estudiantes = $grupo->flatMap->muchosestudiantes;

            return [
                'codigo_familiar' => $grupo->first()->codigo_familiar,
                'estudiantes' => $estudiantes->map(function ($estudiante) {
                    return [
                        'estudiante' => $estudiante,
                        'persona' => $estudiante->persona ? [
                            'nombres' => $estudiante->persona->nombres,
                            'apellidos' => $estudiante->persona->apellidos
                        ] : null,
                        'convenios' => $estudiante->convenios->map(function ($convenio) {
                            return [
                                'convenio' => $convenio,
                                'cuotas_pendientes' => $convenio->cuotas ?? collect(),
                                'productos' => optional($convenio->inscripcion)->productosSeleccionados->map(function ($producto) {
                                    return [
                                        'nombre' => optional($producto->detalle)->nombre ?? 'N/A',
                                        'descripcion' => optional($producto->detalle)->descripcion ?? 'N/A',
                                        'precio' => optional($producto->detalle)->precio ?? 0.0,
                                    ];
                                }) ?? collect(),
                            ];
                        }),
                    ];
                }),
            ];
        })->values(); // Resetear las claves para mantener una lista ordenada

        return $resultado;

    }


    public function pendientesdecomprobantes()
    {



        $convenioIdsArray = explode(',', $convenioId); // Convertir en array

        $pagos = Pago::with(['cuotas.productoSeleccionado.detalle', 'pagoMetodos', 'convenio.estudiante.persona'])

            ->whereDoesntHave('facturaEmitida')
            ->whereDoesntHave('reciboEmitido')
            ->whereDoesntHave('recibointernoEmitido')
            ->get();

        // Formatear los pagos antes de enviarlos a la vista

        // dd( $pagos);
        $pagosAgrupados = $this->formatearPagos($pagos);


        return view('pagos.comprobantespendientes', compact('pagosAgrupados'));

    }






}
