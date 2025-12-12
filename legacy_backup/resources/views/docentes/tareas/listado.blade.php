@extends('crudbooster::admin_template')
@section('content')
@if (session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ session('error') }}
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Listado de Estudiantes Asignados al Curso</h3>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filtroGrado">Filtrar por Grado:</label>
                <select id="filtroGrado" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($estudiantes->pluck('asignacion.grados.nombre')->unique() as $grado)
                        <option value="{{ $grado }}">{{ $grado }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtroCurso">Filtrar por Curso:</label>
                <select id="filtroCurso" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($estudiantes->pluck('asignacion.cursos.curso')->unique() as $curso)
                        <option value="{{ $curso }}">{{ $curso }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtroSeccion">Filtrar por Sección:</label>
                <select id="filtroSeccion" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($estudiantes->pluck('asignacion.secciones.seccion')->unique() as $seccion)
                        <option value="{{ $seccion }}">{{ $seccion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtroJornada">Filtrar por Jornada:</label>
                <select id="filtroJornada" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($estudiantes->pluck('asignacion.jornadas.jornada.nombre')->unique() as $jornada)
                        <option value="{{ $jornada }}">{{ $jornada }}</option>
                    @endforeach
                </select>
            </div>
        </div>


    </div>
    <div class="panel-body">
        @if ($estudiantes->isEmpty())
            <div class="alert alert-warning">
                No hay estudiantes asignados a los cursos de este docente.
            </div>
        @else
            <table id="EstudianteListadoTable" class="table table-bordered table-striped">
                <thead>
                    <tr>

                        <th>Carné</th>
                        <th>Nombre del Estudiante</th>
                        <th>Curso</th>
                        <th>Grado</th>
                        <th>Sección</th>
                        <th>Jornada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($estudiantes as $index => $estudiante) <!-- Aquí asignamos un índice -->
                        <tr>

                            <td>{{ $estudiante->carnet }}</td>
                            <td>{{ $estudiante->persona->apellidos ?? 'N/A' }} {{ $estudiante->persona->nombres ?? 'N/A' }}</td>
                            <td>{{ $estudiante->asignacion->cursos->curso ?? 'N/A' }}</td>
                            <td>{{ $estudiante->asignacion->grados->nombre ?? 'N/A' }}</td>
                            <td>{{ $estudiante->asignacion->secciones->seccion ?? 'N/A' }}</td>
                            <td>{{ $estudiante->asignacion->jornadas->jornada->nombre ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@push('bottom')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.5/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.5/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>



@php
    use crocodicstudio\crudbooster\helpers\CRUDBooster;

    $ruta_relativa_bd = CRUDBooster::getSetting('logo_reporte');
    $ruta_logo_completa = storage_path('app/' . $ruta_relativa_bd);

    $imagen_base64 = null;
    $tipo_imagen = 'png';

    if (!empty($ruta_relativa_bd) && file_exists($ruta_logo_completa) && !is_dir($ruta_logo_completa)) {
        try {
            $tipo_imagen = pathinfo($ruta_logo_completa, PATHINFO_EXTENSION);
            $datos_imagen = file_get_contents($ruta_logo_completa);
            $imagen_base64 = base64_encode($datos_imagen);
        } catch (\Exception $e) {
            $imagen_base64 = null;
        }
    }
@endphp

<script>
    const logoBase64 = @json($imagen_base64 ? 'data:image/{{ $tipo_imagen }};base64,' . $imagen_base64 : null);
    const nombreEstablecimiento = @json(CRUDBooster::getSetting('nombre_del_establecimiento'));
    const direccionEstablecimiento = @json(CRUDBooster::getSetting('direccion_del_establecimiento'));
    const telefonoEstablecimiento = @json(CRUDBooster::getSetting('numero_de_telefono'));
</script>

<script>
$(document).ready(function() {
    var tabla = $('#EstudianteListadoTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                text: 'Copiar',
                titleAttr: 'Copiar al portapapeles',
                className: 'btn btn-secondary',
                action: function (e, dt, button, config) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                    Swal.fire({
                        icon: 'success',
                        title: 'Elementos copiados',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            {
                extend: 'excelHtml5',
                text: 'Exportar a Excel',
                title: 'Listado de Alumnos',
                messageTop: function() {
                    return `${nombreEstablecimiento}\n${direccionEstablecimiento}\nTeléfono: ${telefonoEstablecimiento}`;
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'Exportar a PDF',
                title: 'Listado de Alumnos',
                orientation: 'portrait',
                pageSize: 'letter',
                customize: function (doc) {
                    if (logoBase64) {
                        doc.content.splice(0, 0, {
                            margin: [0, 0, 0, 12],
                            alignment: 'center',
                            image: logoBase64,
                            width: 80
                        });
                    }
                    doc.content.splice(1, 0, {
                        text: `${nombreEstablecimiento}\n${direccionEstablecimiento}\nTeléfono: ${telefonoEstablecimiento}`,
                        alignment: 'center',
                        margin: [0, 0, 0, 12],
                        fontSize: 12,
                        bold: true
                    });
                }
            },
            {
                extend: 'print',
                text: 'Imprimir',
                title: '',
                customize: function (win) {
                    $(win.document.body)
                        .prepend(`
                            <div style="text-align:center; margin-bottom:20px;">
                                ${logoBase64 ? `<img src="${logoBase64}" style="height:80px;">` : '[LOGO NO DISPONIBLE]'}
                                <h2>${nombreEstablecimiento}</h2>
                                <h4>${direccionEstablecimiento}</h4>
                                <h4>Teléfono: ${telefonoEstablecimiento}</h4>
                                <h2>Listado de Alumnos</h2>
                            </div>
                        `);
                }
            }
        ],
        order: [[1, 'asc']],
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            oPaginate: {
                sFirst: "Primero",
                sPrevious: "Anterior",
                sNext: "Siguiente",
                sLast: "Último"
            }
        }
    });

    // Filtros personalizados
    $('#filtroGrado').on('change', function () {
        tabla.column(3).search($(this).val()).draw();
    });

    $('#filtroCurso').on('change', function () {
        tabla.column(2).search($(this).val()).draw();
    });

    $('#filtroSeccion').on('change', function () {
        tabla.column(4).search($(this).val()).draw();
    });

    $('#filtroJornada').on('change', function () {
        tabla.column(5).search($(this).val()).draw();
    });
});
</script>


@endpush


@endsection
