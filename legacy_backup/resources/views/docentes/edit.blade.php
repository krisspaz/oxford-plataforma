@extends('crudbooster::admin_template')

@section('content')



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
    <h2>Editar Datos de Docentes</h2>
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
    <form action="{{ route('docentes.update', $docente->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <h1>Datos del Docente</h1>



        <div class="row mt-3">
            <!-- Columna para la fotografía (izquierda) -->
            <div class="col-md-3">
                <div class="form-group">
                    <div class="form-group">
                        <label for="fotografia_docente">Fotografía:</label>
                        <input type="file" class="form-control-file" id="fotografia_docente" name="fotografia_docente" accept="image/*" onchange="previewImage(event)">
                        <img id="imagePreview"
                             src="{{ isset($persona) && $persona->fotografia ? asset('storage/fotografias/' . $persona->fotografia) : '#' }}"
                             alt="Vista Previa"
                             style="{{ isset($persona) && $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
                    </div>
                </div>
            </div>

            <!-- Columna para el resto de los campos (derecha) -->
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="privilegio_id_docente">Nombre del Privilegio</label>
                            <select name="privilegio_id_docente" id="privilegio_id_docente" class="form-control" >

                                @foreach ($privilegios as $privilegio)
                                <option value="{{ $privilegio->id }}"
                                    {{ $privilegio->name == 'DOCENTE' ? 'selected' : '' }}
                                     {{ $privilegio->name != 'DOCENTE' ? 'disabled' : '' }}>
                                        {{ $privilegio->name }}
                                </option>
                            @endforeach

                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nombres_docente">Nombres</label>
                            <input type="text" name="nombres_docente" class="form-control" value="{{ $docente->persona->nombres }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="apellidos_docente">Apellidos</label>
                            <input type="text" name="apellidos_docente" class="form-control"  value="{{ $docente->persona->apellidos }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="genero_docente">Género</label>
                            <select name="genero_docente" id="genero_docente" class="form-control">
                                <option value="Masculino" {{ $docente->persona->genero == "Masculino" ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ $docente->persona->genero == "Femenino" ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_nacimiento_docente">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento_docente" name="fecha_nacimiento_docente"
                            class="form-control"
                            value="{{ \Carbon\Carbon::parse($docente->persona->fecha_nacimiento)->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="anios_docente">Años Cumplidos</label>
                            <input type="text" id="anios_docente" name="anios_docente" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_tipoidentificacion_docente">Tipo de Identificación</label>
                            <select name="id_tipoidentificacion_docente" id="id_tipoidentificacion_docente" class="form-control">
                                @foreach ($tiposIdentificacion as $tipo)
                                    <option value="{{ $tipo->id }}" {{ $tipo->id == $docente->persona->identificacion_documentos_id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="identificacion_docente">No. de Identificación</label>
                            <input type="text" name="identificacion_docente" class="form-control" value="{{ $docente->persona->num_documento }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cedula_docente">Cedula/Carnet Docente</label>
                            <input type="text" name="cedula_docente" class="form-control" value="{{ $docente->cedula }}" >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="especialidad_docente">Especialidad Docente</label>
                            <input type="text" name="especialidad_docente" class="form-control" value="{{ $docente->especialidad }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="telefono_docente">Teléfono</label>
                            <input type="text" name="telefono_docente" class="form-control" value="{{ $docente->persona->telefono }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="correo_docente">Email</label>
                            <input type="email" name="correo_docente" class="form-control" value="{{ $docente->persona->email }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="direccion_docente">Dirección</label>
                            <textarea name="direccion_docente" id="direccion_docente" rows="3" class="form-control" placeholder="Escribe la dirección completa" required>{{ $docente->persona->direccion }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_defuncion_docente">QEPD</label>
                            <input type="date" id="fecha_defuncion_docente" name="fecha_defuncion_docente"
                            class="form-control"

                            value="{{ old('fecha_defuncion_docente', isset($docente->persona->fecha_defuncion) ? \Carbon\Carbon::parse($docente->persona->fecha_defuncion)->format('Y-m-d') : '') }}"
                            >


                        </div>
                    </div>


                </div>
            </div>
        </div>






        {{-- Botón para enviar --}}
        <button type="submit" class="btn btn-primary">Registrar Docente</button>
        <a href="{{ route('docentes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>


@push('bottom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha_nacimiento_docente');
    const edadInput = document.getElementById('anios_docente');

    function calcularEdad(fechaNacimientoStr) {
        if (!fechaNacimientoStr) return '';
        const fechaNacimiento = new Date(fechaNacimientoStr);
        const hoy = new Date();

        let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        const mes = hoy.getMonth() - fechaNacimiento.getMonth();
        const dia = hoy.getDate() - fechaNacimiento.getDate();

        if (mes < 0 || (mes === 0 && dia < 0)) edad--;
        return edad >= 0 ? edad : 0;
    }

    // Calcular al cargar la página (para vista "edit")
    if (fechaInput.value) {
        edadInput.value = calcularEdad(fechaInput.value);
    }

    // Calcular cuando se cambia la fecha
    fechaInput.addEventListener('change', function() {
        edadInput.value = calcularEdad(this.value);
    });
});
</script>
@endpush

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

