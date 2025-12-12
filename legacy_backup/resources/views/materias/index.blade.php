@extends('crudbooster::admin_template')

@section('content')
<div class="box box-default">

    {{-- 🔹 Mensajes de éxito o error --}}
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

    {{-- 🔹 SweetAlert --}}
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

    <div class="box-header with-border d-flex justify-content-between align-items-center">
        <h3 class="box-title">Listado de Materias</h3>
        <div>
            <a href="{{ route('materias.create') }}" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> Agregar Nueva Materia
            </a>
        </div>
    </div>

    {{-- 🔹 Filtros: Checkbox y selector de registros --}}
    <div class="p-3 border-bottom mb-2" style="background-color: #f9f9f9;">
        <form method="GET" action="{{ route('materias.index') }}" class="form-inline">
            <div class="form-group mr-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="incluir_inactivas" value="1"
                        onchange="this.form.submit()"
                        {{ $incluirInactivas ? 'checked' : '' }}>
                    <span class="ml-2">Incluir Materias Inactivas</span>
                </label>
            </div>
            &nbsp; &nbsp; &nbsp; &nbsp;

            <div class="form-group">
                <label for="registros" class="mr-2">Mostrar:</label>
                <select name="registros" id="registros" class="form-control input-sm"
                    onchange="this.form.submit()">
                    @foreach([10,25,50,100,'Todos'] as $opcion)
                        <option value="{{ $opcion == 'Todos' ? -1 : $opcion }}"
                            {{ request('registros', 10) == ($opcion == 'Todos' ? -1 : $opcion) ? 'selected' : '' }}>
                            {{ $opcion }}
                        </option>
                    @endforeach
                </select>
                <span class="ml-2">registros</span>
            </div>
        </form>
    </div>

    {{-- 🔹 Tabla --}}
    <div class="box-body">
        <table id="materiasTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ciclo Escolar</th>
                    <th>Materia</th>
                    <th>Curso</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Jornada</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($materias as $materia)
                    <tr @if(isset($materia->estado->estado) && strtolower($materia->estado->estado) != 'activo') style="background-color: #f8d7da;" @endif>
                        <td>{{ $materia->id }}</td>
                        <td>{{ $materia->cgshe->gestiones->ciclo_escolar ?? 'N/A' }}</td>
                        <td>{{ $materia->gestionMateria->nombre ?? 'N/A' }}</td>
                        <td>{{ $materia->cgshe->cursos->curso ?? 'N/A' }}</td>
                        <td>{{ $materia->cgshe->grados->nombre ?? 'N/A' }}</td>
                        <td>{{ $materia->cgshe->secciones->seccion ?? 'N/A' }}</td>
                        <td>{{ $materia->cgshe->jornadas->jornada->nombre ?? 'N/A' }}</td>
                        <td>{{ $materia->estado->estado ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('materias.show', $materia->id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i> Ver
                            </a>
                            <a href="{{ route('materias.edit', $materia->id) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('materias.destroy', $materia->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta materia?')">
                                    <i class="fa fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('head')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.3/css/buttons.bootstrap4.min.css">
@endpush

@push('bottom')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.3/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.3/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.3/js/buttons.colVis.min.js"></script>

<script>
    $(document).ready(function() {
        $('#materiasTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            searching: true,
            ordering: true,
            pageLength: {{ request('registros', 10) == -1 ? 999999 : request('registros', 10) }},
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            buttons: [
                { extend: 'excelHtml5', text: '<i class="fa fa-file-excel-o"></i> Excel', className: 'btn btn-success btn-sm' },
                { extend: 'pdfHtml5', text: '<i class="fa fa-file-pdf-o"></i> PDF', className: 'btn btn-danger btn-sm' },
                { extend: 'print', text: '<i class="fa fa-print"></i> Imprimir', className: 'btn btn-info btn-sm' },
                { extend: 'colvis', text: '<i class="fa fa-columns"></i> Columnas', className: 'btn btn-secondary btn-sm' }
            ],
            language: {
                sLengthMenu: "Mostrar _MENU_ registros",
                sZeroRecords: "No se encontraron resultados",
                sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                sSearch: "Buscar:",
                oPaginate: {
                    sNext: "Siguiente",
                    sPrevious: "Anterior"
                }
            }
        });
    });
</script>
@endpush
