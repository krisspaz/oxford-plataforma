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


@if (session('success'))
<div>
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if ( Session::get('message') != '' )
<div class='alert alert-warning'>
    {{ Session::get('message') }}
</div>
@endif

<div class="box">
    <form action="{{ route('docentes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <h1 style="text-align: center;">Datos del Docente</h1>

        <div class="row mt-3">
            <!-- Columna para la fotografía (izquierda) -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fotografia_docente">Fotografía:</label>
                    <input type="file" class="form-control-file" id="fotografia_docente" name="fotografia_docente" accept="image/*" onchange="previewImage(event)">
                    <img id="imagePreview"
                         src="{{ isset($persona) && $persona->fotografia ? asset('storage/fotografias/' . $persona->fotografia) : '#' }}"
                         alt="Vista Previa"
                         style="{{ isset($persona) && $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
                </div>
            </div>

            <!-- Columna para el resto de los campos (derecha) -->
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="privilegio_id_docente">Nombre del Privilegio</label>
                            <select name="privilegio_id_docente" id="privilegio_id_docente" class="form-control">
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
                            <input type="text" name="nombres_docente" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="apellidos_docente">Apellidos</label>
                            <input type="text" name="apellidos_docente" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="genero_docente">Género</label>
                            <select name="genero_docente" id="genero_docente" class="form-control">
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_nacimiento_docente">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento_docente" name="fecha_nacimiento_docente" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_tipoidentificacion_docente">Tipo de Identificación</label>
                            <select name="id_tipoidentificacion_docente" id="id_tipoidentificacion_docente" class="form-control">
                                @foreach ($tiposIdentificacion as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="identificacion_docente">No. de Identificación</label>
                            <input type="text" name="identificacion_docente" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cedula_docente">Cédula/Carnet Docente</label>
                            <input type="text" name="cedula_docente" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="especialidad_docente">Especialidad Docente</label>
                            <input type="text" name="especialidad_docente" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="telefono_docente">Teléfono</label>
                            <input type="text" name="telefono_docente" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="correo_docente">Email</label>
                            <input type="email" name="correo_docente" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="direccion_docente">Dirección</label>
                            <textarea name="direccion_docente" id="direccion_docente" rows="3" class="form-control" placeholder="Escribe la dirección completa" required></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_defuncion_docente">QEPD</label>
                            <input type="date" id="fecha_defuncion_docente" name="fecha_defuncion_docente" class="form-control">
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

@endsection


@section('footer')
    <!-- Asegúrate de que jQuery se carga antes de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection




