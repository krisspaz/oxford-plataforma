@extends('crudbooster::admin_template')

@section('content')
<div class="box">

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

    <div class="box-header">
        <h1>Asignar Estudiantes a la Tarea: {{ $tarea->titulo }}</h1>
    <p><strong>Descripción:</strong> {{ $tarea->descripcion }}</p>

     <!-- Mostrar mensajes de error o éxito -->
     @if(session('success'))
     <div class="alert alert-success">
         {{ session('success') }}
     </div>
 @endif

 @if($errors->any())
     <div class="alert alert-danger">
         <ul>
             @foreach($errors->all() as $error)
                 <li>{{ $error }}</li>
             @endforeach
         </ul>
     </div>
 @endif

    </div>
    <div class="box-body">



    <form action="{{ route('docentes.tareas.asignar', $tarea->id) }}" method="POST">
        @csrf

        <table id="docentesAsignarTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>
                        <!-- Checkbox para seleccionar todos -->
                        <input type="checkbox" id="select-all">
                        <label for="select-all"> Seleccionar</label>
                    </th>
                    <th>#</th>
                    <th>Carnet</th>
                    <th>Nombre Completo</th>
                    <th>Nivel</th>
                    <th>Grado</th>
                    <th>Curso</th>
                    <th>Sección</th>
                    <th>Jornada</th>

                </tr>
            </thead>
            <tbody>
                @foreach($estudiantes as $estudiante)
                    <tr>
                        <td>
                            <!-- Checkbox individual -->
                            <input
                            type="checkbox"
                            name="estudiantes[]"
                            value="{{ $estudiante->id }}"
                            class="student-checkbox"
                            {{ in_array($estudiante->id, $estudiantesAsignadosIds) ? 'checked' : '' }}>
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $estudiante->carnet }}</td>
                        <td>{{ $estudiante->persona->apellidos }}{{" "}}{{ $estudiante->persona->nombres }}</td>
                        <td>{{ $estudiante->cgshges->niveles->nivel }}</td>
                        <td>{{ $estudiante->cgshges->grados->nombre }}</td>
                        <td>{{ $estudiante->cgshges->cursos->curso }}</td>
                        <td>{{ $estudiante->cgshges->secciones->seccion }}</td>
                        <td>{{ $estudiante->cgshges->jornadas->jornada->nombre }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Asignar Estudiantes</button>
        <a href="{{ route('docentes.tareas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</div>

<!-- Script para manejo de checkboxes -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');

        // Seleccionar o deseleccionar todos los checkboxes
        selectAllCheckbox.addEventListener('change', function () {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        // Si todos los checkboxes están seleccionados, marcar el checkbox principal
        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                selectAllCheckbox.checked = Array.from(studentCheckboxes).every(checkbox => checkbox.checked);
            });
        });
    });
</script>

@push('bottom')


<script>
    $(document).ready(function() {
        $('#docentesAsignarTable').DataTable({
            "searching": true,
            "ordering": true,
            "pageLength": 10,
            "order": [[3, "asc"]],
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sPrevious": "Anterior",
                    "sNext":     "Siguiente",
                    "sLast":     "Último"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });
    });
</script>

@endpush
@endsection

