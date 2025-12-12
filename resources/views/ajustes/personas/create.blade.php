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
    <div class="box-header with-border">
        <h3 class="box-title">Crear Nueva Persona</h3>
    </div>

    <div class="box-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>¡Error!</strong> Por favor corrige los siguientes errores:<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ajuste-persona.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nombres">Nombres</label>
                        <input type="text" name="nombres" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select name="genero" class="form-control" required>
                            <option value="">-- Seleccione --</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="estado_civil">Estado Civil</label>
                        <select name="estado_civil" id="estado_civil" class="form-control">
                            <option value="Soltero" {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                            <option value="Casado" {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>Casado</option>
                            <option value="Unido" {{ old('estado_civil') == 'Unido' ? 'selected' : '' }}>Unido (Unión de Hecho)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="apellido_casada">Apellido de Casada</label>
                        <input type="text" name="apellido_casada" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="profesion">Profesión</label>
                        <input type="text" name="profesion" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="identificacion_documentos_id">Tipo de Documento</label>
                        <select name="identificacion_documentos_id" class="form-control" required>
                            <option value="">-- Seleccione --</option>
                            @foreach($documentos as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="num_documento">Número de Documento</label>
                        <input type="text" id="num_documento" name="num_documento" class="form-control" value="{{ old('num_documento') }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <textarea name="direccion" class="form-control" rows="2">{{ old('direccion') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha_defuncion">Fecha de Defunción</label>
                        <input type="date" name="fecha_defuncion" class="form-control" value="{{ old('fecha_defuncion') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="parentesco_id">Parentesco</label>
                        <select name="parentesco_id" class="form-control">
                            <option value="">-- Seleccione --</option>
                            @foreach($parentescos as $p)
                                <option value="{{ $p->id }}">{{ $p->parentesco }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="crear_usuario" value="1" {{ old('crear_usuario') ? 'checked' : '' }} id="crear_usuario_checkbox">
                        Crear usuario para esta persona
                    </label>
                </div>
            </div>

            @php
            $noPermitidos = ['DOCENTE', 'SECRETARIA', 'ADMINISTRATIVO', 'Super Administrator', 'COORDINACION ACADEMICA'];
        @endphp

            <!-- El select de privilegios estará oculto por defecto -->
            <div class="form-group" id="privilegio_group" style="display: none;">
                <label for="privilegio_id">Nombre del Privilegio</label>
                <select name="privilegio_id" id="privilegio_id" class="form-control">
                    <option value="">-- Seleccione --</option>
                    @foreach ($privilegios as $privilegio)
                    @if (!in_array($privilegio->name, $noPermitidos))
                        <option value="{{ $privilegio->id }}">
                            {{ $privilegio->name }}
                        </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('ajuste-persona.index') }}" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<!-- Agregar el script para mostrar/ocultar el select de privilegios -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Obtener el checkbox y el grupo del select
        const checkbox = document.getElementById('crear_usuario_checkbox');
        const privilegioGroup = document.getElementById('privilegio_group');

        // Función para mostrar u ocultar el select de privilegios
        function togglePrivilegioSelect() {
            if (checkbox.checked) {
                privilegioGroup.style.display = 'block';
            } else {
                privilegioGroup.style.display = 'none';
            }
        }

        // Ejecutar la función al cargar la página para revisar si el checkbox está marcado
        togglePrivilegioSelect();

        // Agregar evento al checkbox para que se ejecute cada vez que cambie
        checkbox.addEventListener('change', togglePrivilegioSelect);
    });
</script>
@endsection
