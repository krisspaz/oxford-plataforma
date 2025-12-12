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
    <h2 class="text-center">Editar Asignación de Materias al Docente</h2>
    <form action="{{ route('materiascursos.update', $docente->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Selección de Docente -->
        <div class="form-group">
            <label for="docente_id">Docente:</label>
            <select name="docente_id" id="docente_id" class="form-control" disabled>
                <option value="{{ $docente->id }}">{{ $docente->persona->nombres }} {{ $docente->persona->apellidos }}</option>
            </select>
        </div>

        <div class="col-md-4">
            <!-- Selección de Gestión -->
            <div class="form-group">
                <label for="gestion_select">Gestión o Ciclo Escolar</label>
                <select id="gestion_select" name="gestion_id" class="form-control" style="width: 200px" required>

                    @foreach ($ngestiones as $ngestion)
                        <option value="{{ $ngestion->id }}" {{ $materiascurso->cgshges->gestion_id == $ngestion->id ? 'selected' : '' }}>
                           {{ $ngestion->ciclo_escolar }} {{ "-" }}{{ $ngestion->gestion }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Selección de Nivel -->
            <div class="form-group">
                <label for="nivel_select">Nivel</label>
                <select id="nivel_select" name="nivel_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione un Nivel</option>
                    @foreach ($niveles as $nivel)
                        <option value="{{ $nivel->id }}" {{ $materiascurso->cgshges->nivel_id == $nivel->id ? 'selected' : '' }}>
                            {{ $nivel->nivel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Selección de Curso -->
            <div class="form-group">
                <label for="curso_select">Curso:</label>
                <select name="curso_id" id="curso_select" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione un Curso</option>
                    @foreach ($cursos as $curso)
                        <option value="{{ $curso->id }}" {{ $materiascurso->cgshges->curso_id == $curso->id ? 'selected' : '' }}>
                            {{ $curso->curso }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Selección de Grado -->
            <div class="form-group">
                <label for="grado_select">Grado</label>
                <select id="grado_select" name="grado_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione un Grado</option>
                    @foreach ($grados as $grado)
                        <option value="{{ $grado->id }}" {{ $materiascurso->cgshges->grado_id == $grado->id ? 'selected' : '' }}>
                            {{ $grado->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Selección de Sección -->
            <div class="form-group">
                <label for="seccion_select">Sección</label>
                <select id="seccion_select" name="seccion_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione una Sección</option>
                    @foreach ($secciones as $seccion)
                        <option value="{{ $seccion->id }}" {{ $materiascurso->cgshges->seccion_id == $seccion->id ? 'selected' : '' }}>
                            {{ $seccion->seccion }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Selección de Jornada -->
            <div class="form-group">
                <label for="jornada_select">Jornada:</label>
                <select id="jornada_select" name="jornada_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione Jornada</option>
                    @foreach ($jornadas as $jornada)
                        <option value="{{ $jornada->id }}" {{ $materiascurso->cgshges->jornada_id == $jornada->id ? 'selected' : '' }}>
                            {{ $jornada->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Selección de Estado -->
            <div class="form-group">
                <label for="estado_id">Estado:</label>
                <select name="estado_id" id="estado_id" class="form-control">
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}"
                            @if($estado->id == $materiascurso->estado_id) selected @endif>
                            {{ $estado->estado }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tabla de Materias -->
        <div class="form-group">
            <label>Seleccione las Materias:</label>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Seleccionar</th>
                        <th>Nombre de la Materia</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materias as $materia)
                        <tr>
                            <td>
                                <input type="checkbox" name="materias[]" value="{{ $materia->id }}"
                                    @if(in_array($materia->id, $materiasAsignadas)) checked @endif>
                            </td>
                            <td>{{ $materia->nombre }}</td>
                            <td>{{ $materia->descripcion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Botones de acción -->
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('materiascursos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    const materiasAsignadas = @json($materiasAsignadas); // Inyectamos las materias asignadas

    function fetchOptions(url, params, targetSelectId, textField) {
        const query = new URLSearchParams(params).toString();

        fetch(`${url}?${query}`)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById(targetSelectId);
                select.innerHTML = '<option value="">Seleccione una opción</option>';
                data.forEach(item => {
                    select.innerHTML += `<option value="${item.id}">${item[textField]}</option>`;
                });
            })
            .catch(error => console.error('Error:', error));
    }

    function cargarMaterias() {
        const gestionId = document.getElementById('gestion_select').value;
        const nivelId = document.getElementById('nivel_select').value;
        const cursoId = document.getElementById('curso_select').value;
        const gradoId = document.getElementById('grado_select').value;
        const seccionId = document.getElementById('seccion_select').value;
        const jornadaId = document.getElementById('jornada_select').value;

        if (gestionId && nivelId && cursoId && gradoId && seccionId && jornadaId) {
            fetch(`/get-materias/${gestionId}/${nivelId}/${cursoId}/${gradoId}/${seccionId}/${jornadaId}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.querySelector('table.table tbody');
                    tableBody.innerHTML = '';

                    data.forEach(materia => {
                        let isChecked = materiasAsignadas.includes(materia.id) ? "checked" : "";
                        const row = `
                            <tr>
                                <td><input type="checkbox" name="materias[]" value="${materia.id}" ${isChecked}></td>
                                <td>${materia.gestion_materia.nombre}</td>
                                <td>${materia.gestion_materia && materia.gestion_materia.descripcion ? materia.gestion_materia.descripcion : 'Sin Descripción'}</td>
                            </tr>`;
                        tableBody.innerHTML += row;
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    }

    // Evento change para cada uno de los campos
    document.getElementById('gestion_select').addEventListener('change', cargarMaterias);
    document.getElementById('nivel_select').addEventListener('change', cargarMaterias);
    document.getElementById('curso_select').addEventListener('change', cargarMaterias);
    document.getElementById('grado_select').addEventListener('change', cargarMaterias);
    document.getElementById('seccion_select').addEventListener('change', cargarMaterias);
    document.getElementById('jornada_select').addEventListener('change', cargarMaterias);

    // Cargar las materias al cargar la página si ya hay selección previa
    document.addEventListener('DOMContentLoaded', cargarMaterias);
</script>
@endsection
