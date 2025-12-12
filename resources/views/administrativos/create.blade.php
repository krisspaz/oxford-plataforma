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
    <h2>Registrar Nuevo Personal Administrativo</h2>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
   @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('administrativos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <h1 style="text-align: center;">Datos del Personal Administrativo</h1>

        <div class="row mt-3">
            <!-- Columna para la fotografía (izquierda) -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fotografia_administrativo">Fotografía:</label>
                    <input type="file" class="form-control-file" id="fotografia_administrativo" name="fotografia_administrativo" accept="image/*" onchange="previewImage(event)">
                    <img id="imagePreview"
                         src="{{ isset($administrativo) && $administrativo->fotografia_administrativo ? asset('storage/fotografias/' . $administrativo->fotografia_administrativo) : '#' }}"
                         alt="Vista Previa"
                         style="{{ isset($administrativo) && $administrativo->fotografia_administrativo ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
                </div>
            </div>

            @php
                $noPermitidos = ['DOCENTE', 'ESTUDIANTE', 'ENCARGADO',  'PADRES'];
            @endphp
            <!-- Columna para el resto de los campos (derecha) -->
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="privilegio_id_administrativo">Nombre del Privilegio</label>
                            <select name="privilegio_id_administrativo" id="privilegio_id_administrativo" class="form-control">
                                @foreach ($privilegios as $privilegio)
                                    @if (!in_array($privilegio->name, $noPermitidos))
                                        <option value="{{ $privilegio->id }}">
                                            {{ $privilegio->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nombres_administrativo">Nombres</label>
                            <input type="text" name="nombres_administrativo" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="apellidos_administrativo">Apellidos</label>
                            <input type="text" name="apellidos_administrativo" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="genero_administrativo">Género</label>
                            <select name="genero_administrativo" id="genero_administrativo" class="form-control">
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
                            <label for="fecha_nacimiento_administrativo">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento_administrativo" name="fecha_nacimiento_administrativo" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_tipoidentificacion_administrativo">Tipo de Identificación</label>
                            <select name="id_tipoidentificacion_administrativo" id="id_tipoidentificacion_administrativo" class="form-control">
                                @foreach ($tiposIdentificacion as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="identificacion_administrativo">No. de Identificación</label>
                            <input type="text" name="identificacion_administrativo" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cargo_id">Cargo</label>
                            <select name="cargo_id" id="cargo_id" class="form-control">
                                @foreach ($cargos as $cargo)
                                    <option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="telefono_administrativo">Teléfono</label>
                            <input type="text" name="telefono_administrativo" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="correo_administrativo">Email</label>
                            <input type="email" name="correo_administrativo" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="direccion_administrativo">Dirección</label>
                            <textarea name="direccion_administrativo" id="direccion_administrativo" rows="3" class="form-control" placeholder="Escribe la dirección completa" required></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_defuncion_administrativo">QEPD</label>
                            <input type="date" id="fecha_defuncion_administrativo" name="fecha_defuncion_administrativo" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botón para enviar --}}
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('administrativos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 1500
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: '¡Oops!',
            text: "{{ implode(', ', $errors->all()) }}",
            showConfirmButton: true
        });
    @endif

    @if (Session::get('message') != '')
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: "{{ Session::get('message') }}",
            showConfirmButton: true
        });
    @endif
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





@section('footer')
    <!-- Asegúrate de que jQuery se carga antes de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
@endsection



