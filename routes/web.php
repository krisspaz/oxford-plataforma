<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EstadoController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\GradoController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\JornadaController;
use App\Http\Controllers\DiaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\GradoCarreraController;
use App\Http\Controllers\JornadaDiaHorarioController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\PvTbgradosTbnivelesController;
use App\Http\Controllers\TelefonoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\SucursalTelefonoController;
use App\Http\Controllers\IdentificacionDocumentoController;
use App\Http\Controllers\NivelSucursalController;
use App\Http\Controllers\ParentescoController;
use App\Http\Controllers\PadreController;
use App\Http\Controllers\AnuladorController;

use App\Http\Controllers\AdministrativoController;

use App\Http\Controllers\MadreController;

use App\Http\Controllers\EncargadoController;
use App\Http\Controllers\PvPadresTutoresController;

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ReporteRecibosInternosController;
use App\Http\Controllers\AjustesInscripcionesController;
use App\Http\Controllers\ExoneracionController;

use App\Http\Controllers\CalendarioTareaController;



use App\Http\Controllers\InscripcionController;

use App\Http\Controllers\ContenidoMateriaController;

use App\Http\Controllers\PersonaController;

use App\Http\Controllers\UserProfileController;

use App\Http\Controllers\FamiliasController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\InvoiceController;

use App\Http\Controllers\DocenteController;
use App\Http\Controllers\ReingresoController;

use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReporteFacturasController;
use App\Http\Controllers\ReporteRecibosSatController;


use App\Http\Controllers\ConvenioController;
use App\Http\Controllers\MateriaCursoController;

use App\Http\Controllers\DocenteTareaController;
use App\Http\Controllers\EstudianteTareaController;

use App\Http\Controllers\PvEstudianteContratoController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\PaqueteDetalleController;

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\DetalleProductoController;
use App\Http\Controllers\MatriculacionController;
use App\Http\Controllers\ProductoSeleccionadoController;
use App\Http\Controllers\AsignadorDePaquetesController;
use App\Http\Controllers\ConvenioDetalleController;
use App\Http\Controllers\AjusteFamiliarController;
use App\Http\Controllers\AjustesAsignacionController;
use App\Http\Controllers\AjustesPersonalAdministrativoController;

use App\Http\Controllers\ReciboController;
use App\Http\Controllers\ReciboInternoController;
use App\Http\Controllers\CalificacionTareaController;
use App\Http\Controllers\ReportePagoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\NuevoUsuarioController;

use App\Http\Controllers\ReportePagosController;
use App\Http\Controllers\ReporteAcademicoController;
use App\Http\Controllers\CierreAcademicoController;

use App\Models\Convenio;
use App\Http\Controllers\CuadroNotaController;

use App\Http\Controllers\FacturaController;

use App\Http\Controllers\ReporteNotasController;
use App\Http\Controllers\DocumentoInscripcionController;
use App\Http\Controllers\AjustePersonaController;

use App\Http\Controllers\AjustesEstudiantesController;
use App\Http\Controllers\AjustesContratoController;

Route::get('/profile', [UserProfileController::class, 'profile'])->name('profile');
Route::post('admin/personas/validate', [PersonaController::class, 'validateField'])->name('personas.validate');

// web.php
Route::get('/getMunicipios/{departamentoID}', [PersonaController::class, 'getMunicipios'])->name('getMunicipios');



Route::get('admin/personas/inscripcion', [PersonaController::class, 'create2'])->name('persona.create2');
Route::post('admin/personas/inscripcion/store', [PersonaController::class, 'store'])->name('persona.store');

Route::resource('admin/personas', PersonaController::class);








































































// Agrupar rutas administrativas con el middleware de autenticación de CRUD Booster
Route::group(['middleware' => ['web', 'auth', 'checkRoleMenu']], function () {

    Route::resource('/admin/cierre_academico', CierreAcademicoController::class);
    Route::get('/admin/pagos/insolventes', [PagoController::class, 'insolventes'])->name('pagos.insolventes');
    Route::get('/admin/pagos/exonerados', [PagoController::class, 'exonerados'])->name('pagos.exonerados');

    Route::get('admin/pagos/pdf-estudiante/{id}', [PagoController::class, 'descargarInsolvente'])->name('pagos.pdf.estudiante');

    Route::get('admin/pagos/pdf-todos', [PagoController::class, 'descargarPdfTodos'])->name('pagos.pdf.todos');



    // Rutas para las operaciones CRUD de Sucursal
    Route::get('/admin/pagos/estudiante', [PagoController::class, 'mostrarestudiante'])->name('pagos.buscarpagos');


    Route::get('/admin/calendario-tareas', [CalendarioTareaController::class, 'index'])->name('calendario.tareas');
    Route::get('/admin/eventos-tareas', [CalendarioTareaController::class, 'eventos'])->name('eventos.tareas');
    Route::get('/admin/eventos-tareaspadres', [CalendarioTareaController::class, 'eventospadres'])->name('eventos.tareaspadres');
    Route::get('/admin/calendario-tareaspares', [CalendarioTareaController::class, 'calendariopadres'])->name('calendario.tareaspadres');
    // Rutas para estudiantes
    Route::post('/admin/estudiantes/tareas/subir', [CalendarioTareaController::class, 'subirArchivo'])->name('estudiantes.tareas.subircalendario');
    Route::get('/admin/estudiantes/tareas/descargar/{id}', [CalendarioTareaController::class, 'descargarArchivo'])->name('estudiantes.tareas.descargar');
    Route::delete('/admin/estudiantes/tareas/eliminar/{id}', [CalendarioTareaController::class, 'eliminarArchivo'])->name('estudiantes.tareas.eliminar');

    Route::get('/admin/calificaciones/reportes', [CuadroNotaController::class, 'showReportForm'])->name('cuadro_notas.reportes');
    Route::post('/admin/calificaciones/reportes', [CuadroNotaController::class, 'generateReport'])->name('cuadro_notas.generateReport');

    Route::get(
        '/admin/cuadro-nota/estudiante/{estudiante_id}/tarea/{tarea_id}',
        [CuadroNotaController::class, 'mostrarCalificacionEstudiante']
    )
    ->name('cuadro-nota.mostrarCalificacion');

    Route::get('/admin/calificaciones/reportesdocentes', [CuadroNotaController::class, 'showReportFormdocentes'])->name('cuadro_notas.reportesdocentes');
    Route::post('/admin/calificaciones/reportescalificadas', [CuadroNotaController::class, 'generateReportdocentes'])->name('cuadro_notas.generateReportdocentes');

    Route::post('/admin/cuadro-notas/cierre', [CuadroNotaController::class, 'cierre'])->name('cuadro_notas.cierre');
    Route::get('/admin/cuadro-notas/cierre', [CuadroNotaController::class, 'mostrarCierre'])->name('cuadro_notas.mostrar_cierre');
    Route::get('/admin/cuadro-notas/buscar', [CuadroNotaController::class, 'buscarNotas'])->name('cuadro-notas.buscar');
    Route::get('/admin/cuadro-notas/notaspadres', [CuadroNotaController::class, 'notaspadres'])->name('cuadro-notas.notaspadres');
    Route::get('/admin/cuadro-notas/notasestudiantes', [CuadroNotaController::class, 'mostrarestudiante'])->name('cuadro-notas.notaspadresestudiantes');
    Route::resource('/admin/ajustes_contrato', AjustesContratoController::class);

    Route::resource('/admin/ajustes_estudiantes', AjustesEstudiantesController::class);
    Route::get('/admin/ajustes_estudiantes/create', [AjustesEstudiantesController::class, 'create'])->name('ajustes_estudiantes.create');


    Route::get('/admin/facturas/{id}/download', [FacturaController::class, 'download'])->name('facturas.download');
    Route::get('/admin/recibos/{id}/download', [ReciboController::class, 'download'])->name('recibos.download');
    Route::get('/admin/recibos-internos/{id}/download', [ReciboInternoController::class, 'download'])->name('recibos_internos.download');



    Route::resource('/admin/reporte_pagos', ReportePagosController::class);
    Route::get('/admin/reporte_pagos/export/excel', [ReportePagosController::class, 'exportExcel'])->name('reporte_pagos.export_excel');
    Route::get('/admin/reporte_pagos/export/pdf', [ReportePagosController::class, 'exportPdf'])->name('reporte_pagos.export_pdf');



    Route::resource('/admin/ajustes_inscripciones', AjustesInscripcionesController::class);


    Route::get('/admin/reportes/recibosinternos/anuladas', [ReporteRecibosInternosController::class, 'recibosAnuladas'])->name('reportes.recibosinternos.anuladas');
    Route::put('/admin/reportes/recibosinternos/{factura}/anular', [ReporteRecibosInternosController::class, 'anular'])->name('reportes.recibosinternos.anular');
    Route::get('/admin/reportes/recibosinternos', [ReporteRecibosInternosController::class, 'index'])->name('reportes.recibosinternos.index');
    Route::get('/admin/reportes/recibosinternos/{serie}', [ReporteRecibosInternosController::class, 'show'])->name('reportes.recibosinternos.show');
    Route::post('/admin/reportes/recibosinternos/buscar', [ReporteRecibosInternosController::class, 'buscarPorNit'])->name('reportes.recibosinternos.buscar');
    Route::get('/admin/reportes/recibosinternos/{serie}/pdf', [ReporteRecibosInternosController::class, 'descargarPDF'])->name('reportes.recibosinternos.descargarPDF');

    Route::resource('/admin/ajuste-administrativos', AjustesPersonalAdministrativoController::class);

    Route::get('/admin/reportes/recibossat/anuladas', [ReporteRecibosSatController::class, 'recibosAnuladas'])->name('reportes.recibos.anuladas');
    Route::put('/admin/reportes/recibossat/{factura}/anular', [ReporteRecibosSatController::class, 'anular'])->name('reportes.recibos.anular');
    Route::get('/admin/reportes/recibossat', [ReporteRecibosSatController::class, 'index'])->name('reportes.recibos.index');
    Route::get('/admin/reportes/recibossat/{serie}', [ReporteRecibosSatController::class, 'show'])->name('reportes.recibos.show');
    Route::post('/admin/reportes/recibossat/buscar', [ReporteRecibosSatController::class, 'buscarPorNit'])->name('reportes.recibos.buscar');
    Route::get('/admin/reportes/recibossat/{serie}/pdf', [ReporteRecibosSatController::class, 'descargarPDF'])->name('reportes.recibos.descargarPDF');


    Route::post('/admin/recibointerno/anular/{serie}', [AnuladorController::class, 'procesaranulacion'])
        ->name('recibointerno.procesar_anulacion');
    Route::get('/admin/reportes/facturas/anuladas', [ReporteFacturasController::class, 'facturasAnuladas'])->name('reportes.facturas.anuladas');
    Route::put('/admin/reportes/facturas/{factura}/anular', [ReporteFacturasController::class, 'anular'])->name('reportes.facturas.anular');
    Route::get('/admin/reportes/facturas', [ReporteFacturasController::class, 'index'])->name('reportes.facturas.index');
    Route::get('/admin/reportes/facturas/{serie}', [ReporteFacturasController::class, 'show'])->name('reportes.facturas.show');
    Route::post('/admin/reportes/facturas/buscar', [ReporteFacturasController::class, 'buscarPorNit'])->name('reportes.facturas.buscar');
    Route::get('/admin/reportes/facturas/{serie}/pdf', [ReporteFacturasController::class, 'descargarPDF'])->name('reportes.facturas.descargarPDF');

    Route::get('/admin/anular/documento', [AnuladorController::class, 'showForm'])->name('anulador.form');
    Route::post('/admin/anular/documento', [AnuladorController::class, 'anularDocumento'])->name('anulador.enviar');

    Route::resource('/admin/ajustes_asignacion', AjustesAsignacionController::class);


    Route::resource('/admin/ajuste-familiar', AjusteFamiliarController::class);

    Route::resource('/admin/usuarios', NuevoUsuarioController::class);


    Route::prefix('/admin/ajuste-persona')->name('ajuste-persona.')->group(function () {
        Route::get('/', [AjustePersonaController::class, 'index'])->name('index');
        Route::get('/create', [AjustePersonaController::class, 'create'])->name('create');
        Route::post('/', [AjustePersonaController::class, 'store'])->name('store');
        Route::get('/{id}', [AjustePersonaController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AjustePersonaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AjustePersonaController::class, 'update'])->name('update');
        Route::delete('/{id}', [AjustePersonaController::class, 'destroy'])->name('destroy');
    });


    Route::get('/obtener-ciclos/{id}', [DocumentoInscripcionController::class, 'obtenerCiclos']);
    Route::get('/obtener-inscripciones/{id}/{ciclo}', [DocumentoInscripcionController::class, 'obtenerInscripciones']);


    Route::resource('/admin/documentos', DocumentoInscripcionController::class);


    Route::get('/admin/reporte/filtros', [ReporteNotasController::class, 'mostrarFormularioFiltros'])->name('reporte.boletas.filtros');
    Route::get('/admin/reporte/boletas', [ReporteNotasController::class, 'reporteBoletasPorFiltros'])->name('reporte.boletas.filtro');
    Route::get('/admin/boleta/{estudiante_id}/pdf', [ReporteNotasController::class, 'exportarPDF'])->name('boleta.pdf');



    Route::get('/admin/boleta/{estudiante_id}', [ReporteNotasController::class, 'reporteBoleta'])->name('boleta.estudiante');



    Route::prefix('/admin/docentes')->group(function () {
        Route::resource('cuadro-notas', CuadroNotaController::class)->names([
            'index'   => 'cuadro-notas.index',
            'create'  => 'cuadro-notas.create',
            'store'   => 'cuadro-notas.store',
            'show'    => 'cuadro-notas.show',
            'edit'    => 'cuadro-notas.edit',
            'update'  => 'cuadro-notas.update',
            'destroy' => 'cuadro-notas.destroy',
        ]);


    });







    Route::get('/admin/reporte/filtrar', [ReporteAcademicoController::class, 'filtrarEstudiantes'])->name('reporte.filtrar');
    Route::get('/admin/reporte/pdf', [ReporteAcademicoController::class, 'generarPDF'])->name('reporte.pdf');

    Route::resource('/admin/materias', MateriaController::class);

    Route::get('/get-materias/{gestionId}/{nivelId}/{cursoId}/{gradoId}/{seccionId}/{jornadaId}', [MateriaCursoController::class, 'getMaterias'])
    ->name('materias.get');

    Route::get('/admin/estudiante/{materiaId}/{Inscripcion2}/contenido', [EstudianteTareaController::class, 'obtenerContenidoPorEstudiante'])->name('estudiante.contenido');
    Route::resource('/admin/contenido_materias', ContenidoMateriaController::class);

    Route::get('/admin/recibosinternos/exito', [ReciboInternoController::class, 'mostrarExito'])->name('recibo.exito');
    Route::get('/admin/recibosinternos/descargar', [ReciboInternoController::class, 'descargarRecibo'])->name('recibo.descargar');

    Route::get('/admin/recibos/formulario', [ReciboInternoController::class, 'mostrarFormulario'])->name('recibo.formulario');
    Route::post('/admin/recibos/generar', [ReciboInternoController::class, 'generarRecibo'])->name('recibo.generar');

    Route::get('/admin/calificaciones', [CalificacionTareaController::class, 'index'])->name('calificaciones.index');
    Route::get('/admin/sumar-calificaciones/{materia_id}/{bimestre_id}/{ciclo_escolar}', [CalificacionTareaController::class, 'sumarCalificaciones']);

    Route::get('/admin/calificaciones/create/{tareaEstudianteId}', [CalificacionTareaController::class, 'create'])->name('calificaciones.create');

    Route::post('/calificaciones', [CalificacionTareaController::class, 'store'])->name('calificaciones.store');

    Route::get('calificaciones/{tareaId}/calificadas', [CalificacionTareaController::class, 'calificadas'])->name('calificaciones.calificadas');
    Route::get('calificaciones/{id}/edit', [CalificacionTareaController::class, 'edit'])->name('calificaciones.edit');
    Route::put('calificaciones/{id}', [CalificacionTareaController::class, 'update'])->name('calificaciones.update');
    Route::delete('calificaciones/{id}', [CalificacionTareaController::class, 'destroy'])->name('calificaciones.destroy');

    Route::get('/admin/tareas/familiaalumnos', [CalificacionTareaController::class, 'mostrarFormulario'])->name('tareas.calificadas');
    Route::get('/admin/tareas/calificadas/buscar', [CalificacionTareaController::class, 'buscarTareas'])->name('tareas.buscar');
    //Route::get('/admin/tareas/pdf/{estudiante_id}', [CalificacionTareaController::class, 'descargarPDF'])->name('tareas.pdf');
    //Route::get('/admin/tareas/pdf/{estudiante}/tareas/{tareasCalificadas}/pdf', [CalificacionTareaController::class, 'descargarPDF'])->name('tareas.pdf');
    Route::get('/admin/tareas/pdf/{estudiante}/pdf', [CalificacionTareaController::class, 'descargarPDF'])->name('tareas.pdf');

    Route::get('/admin/tareas/familiaalumnosguia', [CalificacionTareaController::class, 'mostrarFormularioGuia'])->name('tareas.mostrarguia');
    Route::get('/admin/tareas/calificadas/buscarguia', [CalificacionTareaController::class, 'BuscarGuia'])->name('tareas.buscarguia');

    Route::get('/calificaciones/{tarea}/calificar', [CalificacionTareaController::class, 'calificar'])->name('calificaciones.calificar');
    Route::post('/calificaciones/guardar-todo', [CalificacionTareaController::class, 'storeMultiple'])->name('calificaciones.storeMultiple');

    Route::post('/generar-recibointerno', [ReciboInternoController::class, 'generarRecibo'])->name('recibos.generar');



    // Rutas para ConvenioDetalle
    Route::prefix('admin/convenios_detalles')->group(function () {
        Route::get('/', [ConvenioDetalleController::class, 'index'])->name('convenios_detalles.index'); // Listar convenios con detalles
        // Route::get('/create/{id}', [ConvenioDetalleController::class, 'create'])->name('convenios_detalles.create'); // Formulario de creación
        Route::post('/store', [ConvenioDetalleController::class, 'store'])->name('convenios_detalles.store'); // Almacenar nuevo detalle
        Route::get('/{convenioDetalle}', [ConvenioDetalleController::class, 'show'])->name('convenios_detalles.show'); // Ver un detalle específico
        Route::get('/{convenioDetalle}/edit', [ConvenioDetalleController::class, 'edit'])->name('convenios_detalles.edit'); // Formulario de edición

        Route::delete('/{convenioDetalle}', [ConvenioDetalleController::class, 'destroy'])->name('convenios_detalles.destroy'); // Eliminar un detalle
    });

    // Ruta para mostrar el formulario de registro de pago
    Route::get('/admin/pagos/create/{convenioId}', [PagoController::class, 'create'])->name('pagos.create');

    Route::post('/admin/pagos/estadofinanciero', [PagoController::class, 'estadofinanciero'])->name('pagos.financiero');

    Route::post('/admin/pagos/consolidar', [PagoController::class, 'consolidar'])->name('pagos.consolidar');
    Route::post('/admin/pagos/registrar', [PagoController::class, 'registrarPago'])->name('pagos.registrar');
    Route::post('/admin/pagos/registrarporfamilia', [PagoController::class, 'registrarPagoFamiliar'])->name('pagos.registrofamiliar');

    Route::post('/admin/pagos/generar', [PagoController::class, 'generarPago'])->name('pagos.generarPago');
    Route::get('/admin/estado-financiero/pdf/{id}', [PagoController::class, 'generarPDF'])->name('estado.financiero.pdf');



    Route::put('admin/convenios/{id}/update', [ConvenioDetalleController::class, 'update'])->name('convenios_detalles.update');
    Route::get('/admin/convenios/{id}/mostrar-cuotas', [ConvenioDetalleController::class, 'MostrarCuotas'])->name('convenios.mostrar_cuotas');

    Route::get('/admin/convenios/con-detalles', [ConvenioDetalleController::class, 'ConDetalles'])->name('convenios.con_detalles');
    Route::get('/admin/convenios_detalles/create/{id}', [ConvenioDetalleController::class, 'create'])->name('convenios_detalles.create');


    Route::post('/admin/asignador_de_paquetes/store', [AsignadorDePaquetesController::class, 'store'])->name('asignador_de_paquetes.store');

    Route::get('/admin/asignador_de_paquetes', [AsignadorDePaquetesController::class, 'index'])->name('asignador_de_paquetes.index');
    Route::get('/admin/asignador_de_paquetes/create/{id}', [AsignadorDePaquetesController::class, 'create'])->name('asignador_de_paquetes.create');
    Route::resource('/admin/productos_seleccionados', ProductoSeleccionadoController::class);
    Route::post('/productos_seleccionados/{inscripcionId}', [ProductoSeleccionadoController::class, 'store'])->name('productos_seleccionados.store');


    Route::prefix('/admin/matriculaciones')->name('matriculaciones.')->group(function () {
        Route::get('/', [MatriculacionController::class, 'index'])->name('index'); // Listar matriculaciones
        Route::get('/create', [MatriculacionController::class, 'create'])->name('create'); // Formulario de creación
        Route::post('/', [MatriculacionController::class, 'store'])->name('store'); // Guardar nueva matriculación
        Route::get('/{matriculacion}', [MatriculacionController::class, 'show'])->name('show'); // Ver detalles
        Route::get('/{matriculacion}/edit', [MatriculacionController::class, 'edit'])->name('edit'); // Formulario de edición
        Route::put('/{matriculacion}', [MatriculacionController::class, 'update'])->name('update'); // Actualizar matriculación
        Route::delete('/{matriculacion}', [MatriculacionController::class, 'destroy'])->name('destroy'); // Eliminar matriculación


    });





    Route::resource('/admin/detalle_productos', DetalleProductoController::class);

    Route::resource('/admin/productos', ProductoController::class);







    Route::resource('/admin/paquete_detalles', PaqueteDetalleController::class);
    Route::resource('/admin/paquetes', PaqueteController::class);
    Route::post('/admin/paquetes/{id}/recalcular-precio', [PaqueteController::class, 'recalcularPrecio'])->name('paquetes.recalcularPrecio');







    Route::resource('/admin/estudiante_contratos', PvEstudianteContratoController::class);

    Route::post('/contratos/subir', [PvEstudianteContratoController::class, 'uploadSignedContract'])->name('estudiante_contratos.uploadSignedContract');
    // Rutas para

    Route::prefix('/admin/docentes-tareas')->name('docentes.')->group(function () {
        Route::get('tareas', [DocenteTareaController::class, 'index'])->name('tareas.index');
        Route::get('tareas/crear', [DocenteTareaController::class, 'create'])->name('tareas.create');
        Route::post('tareas', [DocenteTareaController::class, 'store'])->name('tareas.store');
        Route::get('tareas/{id}', [DocenteTareaController::class, 'show'])->name('tareas.show');
        Route::get('tareas/{id}/editar', [DocenteTareaController::class, 'edit'])->name('tareas.edit');
        Route::put('tareas/{id}', [DocenteTareaController::class, 'update'])->name('tareas.update');
        Route::delete('tareas/{id}', [DocenteTareaController::class, 'destroy'])->name('tareas.destroy');
        Route::get('tareas/{id}/asignar', [DocenteTareaController::class, 'asignarForm'])->name('tareas.asignar.form');
        Route::post('tareas/{id}/asignar', [DocenteTareaController::class, 'asignar'])->name('tareas.asignar');

    });



    Route::get('/consultar-nit/{nit}', [FacturaController::class, 'consultarNit']);
    Route::get('/admin/test-archivo', [ContenidoMateriaController::class, 'testArchivo']);


    Route::get('/admin/docentes-tareas/listado', [DocenteTareaController::class, 'listado'])->name('tareas.listado');

    // Rutas para Estudiantes
    Route::get('/admin/estudiantes/tareas', [EstudianteTareaController::class, 'index'])->name('estudiantes.tareas.index');
    Route::get('/admin/estudiantes/panel-materias', [EstudianteTareaController::class, 'materias'])->name('estudiantes.tareas.panelMaterias');

    Route::get('/admin/estudiantes/tareas/materia/{materiaId}', [EstudianteTareaController::class, 'tareasPorMateria'])
        ->name('estudiantes.tareas.porMateria');
    Route::get('/admin/estudiantes/tareas/{id}/descargar', [EstudianteTareaController::class, 'descargarArchivo'])->name('estudiantes.tareas.descargar');
    Route::get('/admin/estudiantes/tareas/{id}', [EstudianteTareaController::class, 'show'])->name('estudiantes.tareas.show');

    Route::get('/admin/estudiantes/tareas/{id}/subir', [EstudianteTareaController::class, 'subirArchivo'])->name('estudiantes.tareas.subir');
    Route::post('/admin/estudiantes/tareas/{id}/subir', [EstudianteTareaController::class, 'guardarArchivo'])->name('estudiantes.tareas.guardarArchivo');


    Route::prefix('estudiantes')->name('estudiantes.')->middleware('auth')->group(function () {
        Route::get('tareas', [EstudianteTareaController::class, 'index'])->name('tareas.index');
        Route::post('tareas/{tareaEstudianteId}/subir', [EstudianteTareaController::class, 'uploadFile'])->name('tareas.upload');
        Route::get('tareas/{tareaEstudianteId}/descargar', [EstudianteTareaController::class, 'downloadFile'])->name('tareas.descargar');
    });

    Route::delete('/admin/tarea-estudiante/{id}/eliminar-archivo', [EstudianteTareaController::class, 'eliminarArchivo'])->name('estudiantes.tareas.eliminarArchivo');




    Route::get('/admin/estudiantes/tareas/{id}/descargar', [EstudianteTareaController::class, 'descargarArchivo'])->name('estudiantes.tareas.descargar');



    Route::put('materiascursos/{materiascurso}', [MateriaCursoController::class, 'update'])->name('materiascursos.update');

    Route::resource('/admin/materiascursos', MateriaCursoController::class);


    Route::prefix('/admin/pagos')->group(function () {
        // Mostrar formulario de búsqueda
        Route::get('/buscar', [PagoController::class, 'index'])->name('pagos.buscar.formulario');


        // Procesar búsqueda
        Route::post('/buscar', [PagoController::class, 'buscar'])->name('pagos.buscar');

        Route::get('/facturar/{id}', [PagoController::class, 'facturarPagos'])->name('ruta.facturar');
        Route::get('/facturarfamilia/{ids}', [PagoController::class, 'facturarPagosFamiliar'])->name('ruta.facturarfamilia');
        Route::get('/pendientescomprobantes', [PagoController::class, 'pendientesdecomprobantes'])->name('ruta.facturarfamilia2');
        // Registrar pagos
        Route::post('/registrar', [PagoController::class, 'registrarPago'])->name('pagos.registrar');
        Route::get('/verpagos', [PagoController::class, 'verPagos'])->name('pagos.ver');
        Route::post('/factura/generar/{pago_id}', [PagoController::class, 'generarFactura'])->name('pagos.generar');
        Route::post('/recibo/generar/{pago_id}', [PagoController::class, 'generarRecibo'])->name('pagos.recibo');
        Route::post('/recibointerno/generar/{pago_id}', [PagoController::class, 'generarReciboInterno'])->name('pagos.recibointerno');
    });


    Route::get('/pagos/resultados/{convenio}', function ($convenio) {
        $convenio = Convenio::findOrFail($convenio);
        return view('pagos.resultados_estudiante', compact('convenio'));
    })->name('pagos.resultados_estudiante');


    Route::get('/pagos/resultados-familia/{codigo_familiar}', function ($codigo_familiar) {
        $resultado = PagoController::generarResultadoDesdeCodigoFamiliar($codigo_familiar);
        return view('pagos.resultados_familia', compact('resultado'));
    })->name('pagos.resultados_familia');




    Route::get('/admin/reporte-pagos', [ReportePagoController::class, 'generarPDF'])->name('pagos.reporte');

    Route::get('admin/filtrar-pagos', [ReportePagoController::class, 'mostrarFormularioFiltros'])
    ->name('reportes.filtrar_pagos');



    Route::get('/admin/factura/descargar/{id}', [FacturaController::class, 'descargarFactura'])->name('descargar.factura');

    Route::post('/admin/generar-recibo', [ReciboController::class, 'generarRecibo'])->name('factura.recibo');





    Route::post('/pagos/store', [PagoController::class, 'store'])->name('pagos.store');





    Route::get('/admin/estudiantes/{estudiantes}/pago', [ConvenioController::class, 'pagoaestuiantes'])
    ->name('convenios.estudianteconvenio');

    Route::get('/admin/convenios/mostrar', [ConvenioController::class, 'mostrar_convenios'])->name('convenios.mostrar');

    Route::post('/admin/convenios', [ConvenioController::class, 'store'])->name('convenios.store'); // Para crear
    Route::put('/admin/convenios/{id}', [ConvenioController::class, 'update'])->name('convenios.update'); // Para actualizar
    Route::get('/admin/convenios/create/{id}', [ConvenioController::class, 'create'])->name('convenios.create');

    Route::get('/admin/convenios', [ConvenioController::class, 'index'])->name('convenios.index'); // Lista todos los convenios
    Route::get('/admin/convenios/{id}/edit', [ConvenioController::class, 'edit'])->name('convenios.edit'); // Muestra el formulario de edición
    Route::get('/admin/convenios/{id}', [ConvenioController::class, 'show'])->name('convenios.show'); // Muestra un convenio en detalle
    Route::delete('/admin/convenios/{id}', [ConvenioController::class, 'destroy'])->name('convenios.destroy');



    Route::prefix('/admin/inscripcion/reingreso')->group(function () {
        Route::get('/', [ReingresoController::class, 'index'])->name('reingreso.index'); // Listar estudiantes
        Route::get('/{id}/edit', [ReingresoController::class, 'edit'])->name('reingreso.edit'); // Formulario de reingreso
        Route::put('/{id}', [ReingresoController::class, 'update'])->name('reingreso.update'); // Actualizar datos de reingreso

    });
    Route::get('/get-paquetes', [ReingresoController::class, 'getPaquetes']);



    Route::get('admin/factura/generar/exito', function () {
        return view('factura.exito');
    })->name('admin/factura/generar/exito');

    Route::get('admin/recibos/generar/exitorecibointerno', function () {
        return view('recibos.exitorecibointerno');
    })->name('admin/factura/generar/exito');

    Route::get('admin/factura/generar/exitorecibo', function () {
        return view('factura.exitorecibo');
    })->name('admin/factura/generar/exitorecibo');

    Route::get('admin/recibo/generar', function () {
        return view('recibos.detalle');
    })->name('admin/recibo/generar');





    Route::resource('/admin/docentes', DocenteController::class);
    Route::resource('/admin/administrativos', AdministrativoController::class);
    //Route::put('/docentes/{id}', [DocenteController::class, 'update'])->name('docentes.update');


    Route::get('factura/exito', function () {
        return view('factura.exito');
    });




    Route::get('admin/factura', function () {
        return view('factura.factura');
    })->name('factura.form');


    Route::get('/admin/factura/recibo', function () {
        return view('factura.recibo');
    })->name('factura.reci');

    Route::get('/generate-xml', [FacturaController::class, 'generateXml']);


    Route::post('/admin/generar-factura', [FacturaController::class, 'generarFactura'])->name('factura.generar');
    Route::get('/admin/descargar-xml', [FacturaController::class, 'downloadXml'])->name('factura.descargar');

    Route::get('admin/factura/xml', [FacturaController::class, 'downloadXml'])->name('factura.xml');

    Route::post('/admin/factura/generar', [FacturaController::class, 'generarFactura'])->name('factura.generar');

    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'downloadXML'])->name('download-xml');


    Route::get('/admin/invoices/create', [InvoiceController::class,'create'])->name('invoices.create');
    Route::post('/admin/invoices', [InvoiceController::class,'store'])->name('invoices.store');
    Route::get('/admin/invoices/{invoice}', [InvoiceController::class,'show'])->name('invoices.show');

    Route::get('/contrato/{id}', [ContratoController::class, 'generarContrato'])->name('contrato.generar');

    Route::post('/contratos/{id}/subir', [ContratoController::class, 'uploadSignedContract'])->name('contratos.uploadSignedContract');
    Route::get('/contratoregenerar/{inscripcion}/{numero}', [ContratoController::class, 'recrearcontrato'])->name('contrato.regenerar');


    // Mostrar cuotas
    Route::get(
        '/admin/exonerar/{estudianteId}',
        [ExoneracionController::class, 'mostrarCuotas']
    )->name('cuotas.exonerar');


    // Mostrar cuotas exoneradas
    Route::get(
        '/admin/exoneradas/{estudianteId}',
        [ExoneracionController::class, 'mostrarCuotasExoneradas']
    )->name('cuotas.exoneradas');

    // Procesar exoneración
    Route::post(
        '/admin/exonerar/cuotas',
        [ExoneracionController::class, 'solicitudexonerarCuotas']
    )->name('cuotas.exonerar.solicitar');




    Route::get(
        '/admin/exoneradasprocesar/{estudianteId}',
        [ExoneracionController::class, 'mostrarListaCuotasExoneradas']
    )->name('cuotas.exonerar.procesar');

    // Rechazar exoneración
    Route::post(
        '/admin/rechazar/cuotas',
        [ExoneracionController::class, 'rechazarexonerarCuotas']
    )->name('cuotas.exonerar.rechazar');


    //procesar exoneracion
    Route::post(
        '/admin/procesarexoneracion/cuotas',
        [ExoneracionController::class, 'procesarexoneracion']
    )->name('cuotas.exonerar.proceder');

    Route::get('/admin/notifications/clear-view', function () {
        return view('notifications.clear');
    });


    Route::get('admin/familias/create2/{id}', [FamiliasController::class, 'create2'])->name('familias.create2');
    Route::get('admin/familias/{familiar}', [FamiliasController::class,'show'])->name('familias.show');

    Route::resource('admin/estados', EstadoController::class);
    Route::resource('admin/niveles', NivelController::class);
    Route::resource('admin/grados', GradoController::class);
    Route::resource('admin/carreras', CarreraController::class);
    Route::resource('admin/jornadas', JornadaController::class);
    Route::resource('admin/statuses', EstadoController::class);
    Route::resource('admin/dias', DiaController::class);
    Route::resource('admin/horarios', HorarioController::class);
    Route::resource('admin/grado-carreras', GradoCarreraController::class);
    Route::resource('admin/jornada-dia-horarios', JornadaDiaHorarioController::class);
    Route::resource('admin/departamentos', DepartamentoController::class);
    Route::resource('admin/municipios', MunicipioController::class);
    Route::resource('admin/telefonos', TelefonoController::class);
    Route::resource('admin/sucursal_telefonos', SucursalTelefonoController::class);
    Route::resource('admin/sucursals', SucursalController::class);
    Route::resource('admin/pv_tbgrados_tbniveles', PvTbgradosTbnivelesController::class);
    Route::get('/sucursals/municipios/{departamento}', [SucursalController::class, 'getMunicipios'])->name('sucursals.getMunicipios');
    Route::resource('admin/niveles_sucursals', NivelSucursalController::class);
    Route::resource('admin/parentescos', ParentescoController::class);
    Route::resource('admin/identificacion_documentos', IdentificacionDocumentoController::class);
    Route::resource('admin/padres', PadreController::class);
    Route::resource('admin/madres', MadreController::class);
    Route::resource('admin/encargados', EncargadoController::class);
    Route::resource('admin/pv_padres_tutores', PvPadresTutoresController::class);
    Route::resource('admin/alumnos', AlumnoController::class);

    Route::get('admin/inscripcion/search', [InscripcionController::class, 'search'])->name('inscripcion.search');
    Route::get('admin/inscripcion/search2', [InscripcionController::class, 'search'])->name('inscripcion.search');
    Route::resource('admin/inscripcion', InscripcionController::class);

    Route::resource('admin/familias', FamiliasController::class);

    Route::get('personas/get-municipios/{departamento_id}', [PersonaController::class, 'getMunicipios'])->name('personas.getMunicipios');
    Route::get('/get-codigo-postal/{municipio}', [App\Http\Controllers\MunicipioController::class, 'getCodigoPostal']);

    Route::get('inscripcion/get-municipios/{departamento_id}', [InscripcionController::class, 'getMunicipios'])->name('inscripcion.getMunicipios');
    Route::get('encargados/get-municipios/{departamento_id}', [EncargadoController::class, 'getMunicipios'])->name('encargados.getMunicipios');
    Route::get('madres/get-municipios/{departamento_id}', [MadreController::class, 'getMunicipios'])->name('madres.getMunicipios');
    Route::get('padres/get-municipios/{departamentoId}', [PadreController::class, 'getMunicipios'])->name('padres.getMunicipios');
    Route::put('/admin/familias/{id}', [FamiliasController::class, 'update'])->name('familias.update');
    Route::put('/admin/familias/', [FamiliasController::class, 'editarEstudiante'])->name('familias.editarestudiante');
    Route::put('/admin/familiasmedico/', [FamiliasController::class, 'editarHistorialMedico'])->name('familias.editarmedico');
    Route::get('/familias/forms/forms_persona', [FamiliasController::class, 'getFormPersona'])->name('familias.forms_persona');
    Route::put('/familias/editar-academico', [FamiliasController::class, 'editarAcademico'])->name('familias.editar-academico');

    Route::get('/familias/{id}/edit', [FamiliasController::class, 'edit'])->name('familias.edit');




    Route::get('/get-niveles', [InscripcionController::class, 'getNiveles'])->name('get.niveles');
    Route::get('/get-cursos', [InscripcionController::class, 'getCursos'])->name('get.cursos');
    Route::get('/get-grados', [InscripcionController::class, 'getGrados'])->name('get.grados');
    Route::get('/get-secciones', [InscripcionController::class, 'getSecciones'])->name('get.secciones');
    Route::get('/get-jornadas', [InscripcionController::class, 'getJornadas'])->name('get.jornadas');



    Route::get('/municipios/{departamento}', function ($departamentoId) {
        $municipios = \App\Models\Municipio::where('departamento_id', $departamentoId)->get();
        return response()->json($municipios);
    });
    Route::get('get-municipios-by-departamento', [SucursalController::class, 'getMunicipiosByDepartamento'])->name('sucursales.getMunicipiosByDepartamento');




});


Route::get('/', function () {
    //return view('principal');
    return redirect('/admin/login');
});
