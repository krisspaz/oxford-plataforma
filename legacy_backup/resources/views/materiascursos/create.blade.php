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
    <h2 class="text-center">Nueva Asignación</h2>
    <form action="{{ route('materiascursos.store') }}" method="POST">
        @csrf
        <!-- Selección de Docente -->
        <div class="form-group">
            <label for="docente_id">Docente:</label>
            <select name="docente_id" id="docente_id" class="form-control">
                @foreach($docentes as $docente)
                    <option value="{{ $docente->id }}">{{ $docente->persona->nombres }} {{ $docente->persona->apellidos }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">


            <div class="form-group">
                <label for="gestion_select">Gestión Escolar</label>
                <select id="gestion_select" name="gestion_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione una Gestión</option>
                    @foreach ($ngestiones as $ngestion)
                        <option value="{{ $ngestion->id }}">{{"Ciclo Escolar: " }}{{ $ngestion->ciclo_escolar }}{{ " Gestion: " }}{{ $ngestion->gestion }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nivel_select">Nivel</label>
                <select id="nivel_select" name="nivel_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione un Nivel</option>
                </select>
            </div>

            <div class="form-group">
                <label for="curso_select">Curso</label>
                <select id="curso_select" name="curso_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione el Curso</option>
                </select>
            </div>

            <div class="form-group">
                <label for="grado_select">Grado</label>
                <select id="grado_select" name="grado_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione un Grado</option>
                </select>
            </div>
        </div>

        <!-- Tercera columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="seccion_select">Sección</label>
                <select id="seccion_select" name="seccion_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione una Sección</option>
                </select>
            </div>

            <div class="form-group">
                <label for="jornada_select">Jornada:</label>
                <select id="jornada_select" name="jornada_id" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione Jornada</option>
                </select>
            </div>

        <div class="form-group">
            <label for="estado_id">Estado</label>
            <select name="estado_id" id="estado_id" class="form-control select2" required>
                <option value="">Selecciona un estado</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
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

                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

        <!-- Botones de acción -->
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('materiascursos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>


<script>
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

    document.getElementById('gestion_select').addEventListener('change', function () {
        fetchOptions('/get-niveles', { gestion_id: this.value }, 'nivel_select', 'nivel');
    });

    document.getElementById('nivel_select').addEventListener('change', function () {
        fetchOptions('/get-cursos', { nivel_id: this.value }, 'curso_select', 'curso');
    });

    document.getElementById('curso_select').addEventListener('change', function () {
        fetchOptions('/get-grados', { curso_id: this.value }, 'grado_select', 'nombre');
    });

    document.getElementById('curso_select').addEventListener('change', function () {
        const cursoId = this.value;
        const gradoId = document.getElementById('grado_select').value; // Suponiendo que tienes un elemento con id 'grado_select'

        if (cursoId && gradoId) {
            fetchOptions('/get-secciones', { curso_id: cursoId, grado_id: gradoId }, 'seccion_select', 'seccion');

        }
        fetchOptions('/get-paquetes', { curso_id: cursoId }, 'paquete_select', 'nombre');
    });

    document.getElementById('grado_select').addEventListener('change', function () {
        const cursoId = document.getElementById('curso_select').value; // Suponiendo que tienes un elemento con id 'curso_select'
        const gradoId = this.value;

        if (cursoId && gradoId) {
            fetchOptions('/get-secciones', { curso_id: cursoId, grado_id: gradoId }, 'seccion_select', 'seccion');
        }
    });


    document.getElementById('seccion_select').addEventListener('change', function () {
        fetchOptions('/get-jornadas', { seccion_id: this.value }, 'jornada_select', 'nombre');
    });


    </script>

    <script>
     document.getElementById('jornada_select').addEventListener('change', function () {
    const gestionId = document.getElementById('gestion_select').value;
    const nivelId = document.getElementById('nivel_select').value;
    const cursoId = document.getElementById('curso_select').value;
    const gradoId = document.getElementById('grado_select').value;
    const seccionId = document.getElementById('seccion_select').value;
    const jornadaId = this.value;

    if (gestionId && nivelId && cursoId && gradoId && seccionId && jornadaId) {
        fetch(`/get-materias/${gestionId}/${nivelId}/${cursoId}/${gradoId}/${seccionId}/${jornadaId}`)
            .then(response => {
                if (!response.ok) throw new Error('No se encontraron materias.');
                return response.json();
            })
            .then(data => {
                console.log('Materias cargadas:', data); // DEBUG

                const tableBody = document.querySelector('table.table tbody');
                tableBody.innerHTML = ''; // Limpia la tabla

                data.forEach(materia => {
                    const nombreGestionMateria = materia.gestion_materia ? materia.gestion_materia.nombre : 'Sin nombre';

                    tableBody.innerHTML += `
                        <tr>
                            <td>
                                <input type="checkbox" name="materias[]" value="${materia.id}">
                            </td>
                            <td>${nombreGestionMateria}</td>
                        </tr>`;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('No se encontraron materias para la selección actual.');
            });
    }
});


    </script>
@endsection
