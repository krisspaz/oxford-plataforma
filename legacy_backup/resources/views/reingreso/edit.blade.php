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
<div class="container">
    <h1>Matriculación del Curso: {{ $estudiante->persona->nombre_completo }}</h1>

    <form action="{{ route('reingreso.update', $estudiante->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Primera columna -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fotografia_estudiante">Fotografía del Estudiante:</label>
                    <img id="imagePreview"
                         src="{{ asset('storage/' . $estudiante->fotografia_estudiante) }}"
                         alt="Fotografía"
                         style="max-width: 150px; {{ isset($estudiante->fotografia_estudiante) ? '' : 'display: none;' }}" />
                </div>
                <p><strong>Nombre:</strong> {{ $estudiante->persona->nombres }}</p>
                <p><strong>Apellido:</strong> {{ $estudiante->persona->apellidos }}</p>
                <p><strong>Código Personal:</strong> {{ $estudiante->carnet }}</p>
                <p><strong>Código de Familia:</strong> {{ $estudiante->familia->codigo_familiar }}</p>
                <p><strong>Nombre de Familia:</strong> {{ $estudiante->familia->nombre_familiar }}</p>
            </div>

            <!-- Segunda columna -->


            <div class="col-md-4">
                <div class="form-group">
                    <label for="Ciclo_escolar">Ciclo_Escolar:</label>
                    <select name="ciclo_escolar" id="ciclo_escolar" class="form-control" style="width: 200px">
                        <option value="">Seleccione un año</option>
                        @foreach ($ngestiones as $ngestion)
                            <option value="{{ $ngestion->ciclo_escolar }}">{{ $ngestion->ciclo_escolar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="gestion_select">Gestión o Ciclo Escolar</label>
                    <select id="gestion_select" name="gestion_id" class="form-control" style="width: 200px" required>
                        <option value="">Seleccione una Gestión</option>
                        @foreach ($ngestiones as $ngestion)
                            <option value="{{ $ngestion->id }}">{{ $ngestion->gestion }}</option>
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
                    <label for="paquete_select">Paquete Escolar:</label>
                    <select id="paquete_select" name="paquete_id" class="form-control" style="width: 200px" required>
                        <option value="">Seleccione un Paquete</option>
                    </select>
                </div>



                <div class="form-group">
                    <label for="fecha_inscripcion" class="form-label">Fecha de Inscripción</label>
                    <input type="date" name="fecha_inscripcion" id="fecha_inscripcion" class="form-control" style="width: 200px"
                           value="{{ $matriculacion->fecha_inscripcion ?? old('fecha_inscripcion') }}" required>
                </div>

                <div class="form-group">
                    <label for="estado_id">Estado</label>
                  <select id="estado_id" name="estado_id" class="form-control" style="width: 200px" required>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" {{ $estudiante->estado_id == $estado->id ? 'selected' : '' }}>
                                {{ $estado->estado }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Guardar Matriculación</button>
            <a href="{{ route('reingreso.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
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
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
