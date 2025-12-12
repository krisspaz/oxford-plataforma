@extends('crudbooster::admin_template')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Reporte de Calificaciones 2</h3>
    </div>

    <div class="box-body table-responsive no-padding">
        <form method="POST" action="{{ route('cuadro_notas.generateReport') }}" class="mb-3" id="filtro-form">
            @csrf
            <div class="input-group" style="max-width: 500px;">
                <input type="text" id="nombre_estudiante" name="nombre_estudiante" class="form-control" placeholder="Buscar por nombre de estudiante" value="{{ request('nombre_estudiante') }}" oninput="toggleLimpiarButton()">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                    <button type="button" id="limpiar-btn" class="btn btn-default" onclick="limpiarFiltro()" style="display: {{ request('nombre_estudiante') ? 'inline-block' : 'none' }};">
                        Limpiar
                    </button>
                </span>
            </div>

            {{-- Campos ocultos para mantener los filtros activos --}}
            <input type="hidden" name="materia_id" value="{{ request('materia_id') }}">
            <input type="hidden" name="bimestre_id" value="{{ request('bimestre_id') }}">
            <input type="hidden" name="ciclo_escolar_id" value="{{ request('ciclo_escolar_id') }}">


        </form>

        {{-- Formulario oculto para limpiar --}}
        <form id="clear-form" method="POST" action="{{ route('cuadro_notas.generateReport') }}" style="display: none;">
            @csrf
            <input type="hidden" name="materia_id" value="{{ request('materia_id') }}">
            <input type="hidden" name="bimestre_id" value="{{ request('bimestre_id') }}">
            <input type="hidden" name="ciclo_escolar_id" value="{{ request('ciclo_escolar_id') }}">
        </form>

        <div class="form-inline" style="margin: 10px 0;">
            <label for="itemsPerPage">Mostrar: </label>
            <select id="itemsPerPage" class="form-control" style="width: auto; margin-left: 5px;">
                <option value="5" selected>5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="100">100</option>
                <option value="9999">Todos</option>
            </select>
        </div>
        <div id="pagination-controls" class="text-center" style="margin-bottom: 20px;"></div>




        <br>

        @php
            $nombreFiltro = request('nombre_estudiante');

            $filtradas = $calificaciones->filter(function($item) use ($nombreFiltro) {
                if (!$nombreFiltro) return true;

                $nombreCompleto = strtolower($item->tareaEstudiante->estudiante->persona->apellidos . ' ' . $item->tareaEstudiante->estudiante->persona->nombres);
                return str_contains($nombreCompleto, strtolower($nombreFiltro));
            });

            $agrupadas = $filtradas->groupBy(function($item) {
                return $item->tareaEstudiante->estudiante->id . '-' .
                       $item->tareaEstudiante->estudiante->asignacion->grados->nombre . '-' .
                       $item->tareaEstudiante->tarea->bimestre->nombre;
            });
        @endphp

        @if(count($agrupadas))
            @foreach($agrupadas as $grupo => $items)
                @php
                    $estudiante = $items->first()->tareaEstudiante->estudiante;
                    $curso = $estudiante->asignacion->grados->nombre;
                    $bimestre = $items->first()->tareaEstudiante->tarea->bimestre->nombre;
                    $porMateria = $items->groupBy('tareaEstudiante.tarea.materia.gestionMateria.nombre');
                @endphp

            <div class="box box-info estudiante-box">
                    <div class="box-header">
                        <strong>Estudiante:</strong> {{ $estudiante->persona->apellidos }} {{ $estudiante->persona->nombres }} <br>
                        <strong>Curso:</strong> {{ $curso }} <br>
                        <strong>Bimestre:</strong> {{ $bimestre }}
                    </div>
                    <div class="box-body">
                        @foreach($porMateria as $materia => $calificacionesMateria)
                            <h4><strong>Materia:</strong> {{ $materia ?? 'Sin nombre' }}</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tarea</th>
                                        <th>Descripción</th>
                                        <th>Fecha de Entrega</th>
                                        <th>Estado</th>
                                        <th>Calificación</th>
                                        <th>Comentario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($calificacionesMateria as $calificacion)
                                        <tr>
                                            <td>{{ $calificacion->tareaEstudiante->tarea->titulo }}</td>
                                            <td>{{ $calificacion->tareaEstudiante->tarea->descripcion }}</td>
                                            <td>{{ \Carbon\Carbon::parse($calificacion->tareaEstudiante->tarea->fexpiracion)->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($calificacion->tareaEstudiante && $calificacion->tareaEstudiante->fecha_entrega)
                                                    Tarea Entregada
                                                @else
                                                    Sin Entregar
                                                @endif
                                            </td>
                                            <td>{{ $calificacion->calificacion }}</td>
                                            <td>{{ $calificacion->comentario }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info">
                No hay calificaciones disponibles para los filtros seleccionados.
            </div>
        @endif

    </div>
</div>

<script>
    function limpiarFiltro() {
        document.getElementById('nombre_estudiante').value = '';
        document.getElementById('limpiar-btn').style.display = 'none';
        document.getElementById('filtro-form').submit();
    }

    function toggleLimpiarButton() {
        const input = document.getElementById('nombre_estudiante').value;
        document.getElementById('limpiar-btn').style.display = input.trim() ? 'inline-block' : 'none';
    }
</script>

<!-- DataTables CSS y JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('.table').DataTable({
            language: {
                decimal: "",
                emptyTable: "No hay datos disponibles en la tabla",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrar _MENU_ registros",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No se encontraron resultados",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                },
                aria: {
                    sortAscending: ": activar para ordenar la columna ascendente",
                    sortDescending: ": activar para ordenar la columna descendente"
                }
            },
            paging: true,
            info: true,
            ordering: true,
            pageLength: 1, // Número inicial de registros por página
            lengthMenu: [1,5, 10, 25, 50, 100]
        });
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const itemsPerPageSelect = document.getElementById('itemsPerPage');
        const allItems = document.querySelectorAll('.estudiante-box');
        const paginationControls = document.getElementById('pagination-controls');

        let currentPage = 1;

        function renderPagination(itemsPerPage) {
            const totalItems = allItems.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);

            paginationControls.innerHTML = ''; // limpiar

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = 'btn btn-default';
                btn.innerText = i;
                if (i === currentPage) {
                    btn.classList.add('btn-primary');
                }
                btn.addEventListener('click', function () {
                    currentPage = i;
                    showPage(currentPage, itemsPerPage);
                    renderPagination(itemsPerPage);
                });
                paginationControls.appendChild(btn);
            }
        }

        function showPage(page, itemsPerPage) {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            allItems.forEach((item, index) => {
                item.style.display = (index >= start && index < end) ? 'block' : 'none';
            });
        }

        itemsPerPageSelect.addEventListener('change', function () {
            const perPage = parseInt(this.value);
            currentPage = 1;
            showPage(currentPage, perPage);
            renderPagination(perPage);
        });

        // Inicialización
        const initialPerPage = parseInt(itemsPerPageSelect.value);
        showPage(currentPage, initialPerPage);
        renderPagination(initialPerPage);
    });
</script>


@endsection
