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
        <h1>Editar Padre</h1>
        <form action="{{ route('padres.update', $padre->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $padre->nombre) }}">
                @error('nombre')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido', $padre->apellido) }}">
                @error('apellido')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="identificacion_documentos_id">Documento de Identificación:</label>
                <select name="identificacion_documentos_id" id="identificacion_documentos_id" class="form-control">
                    @foreach($identificacionDocumentos as $documento)
                        <option value="{{ $documento->id }}" {{ old('identificacion_documentos_id', $padre->identificacion_documentos_id) == $documento->id ? 'selected' : '' }}>{{ $documento->nombre }}</option>
                    @endforeach
                </select>
                @error('identificacion_documentos_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="num_documento">Número de Documento:</label>
                <input type="text" name="num_documento" id="num_documento" class="form-control" value="{{ old('num_documento', $padre->num_documento) }}">
                @error('num_documento')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $padre->fecha_nacimiento) }}">
                @error('fecha_nacimiento')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="profesion">Profesión:</label>
                <input type="text" name="profesion" id="profesion" class="form-control" value="{{ old('profesion', $padre->profesion) }}">
                @error('profesion')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $padre->telefono) }}">
                @error('telefono')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="departamento_id">Departamento:</label>
                <select name="departamento_id" id="departamento_id" class="form-control">
                    <option value="">Seleccione un departamento</option>
                    @foreach($departamentos as $departamento)
                        <option value="{{ $departamento->id }}" {{ old('departamento_id', $padre->municipio->departamento_id) == $departamento->id ? 'selected' : '' }}>{{ $departamento->departamento }}</option>
                    @endforeach
                </select>
                @error('departamento_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="municipio_id">Municipio:</label>
                <select name="municipio_id" id="municipio_id" class="form-control">
                    <option value="">Seleccione un municipio</option>
                </select>
                @error('municipio_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <textarea name="direccion" id="direccion" class="form-control">{{ old('direccion', $padre->direccion) }}</textarea>
                @error('direccion')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('padres.index') }}" class="btn btn-secondary">Volver al listado</a>
        </form>
    </div>

    <script>
        document.getElementById('departamento_id').addEventListener('change', function() {
            var departamentoId = this.value;
            var municipioSelect = document.getElementById('municipio_id');
            municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';

            if (departamentoId) {
                fetch(`/padres/get-municipios/${departamentoId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(municipio => {
                            var option = document.createElement('option');
                            option.value = municipio.id;
                            option.text = municipio.municipio;
                            municipioSelect.add(option);
                        });
                    });
            }
        });

        // Para cargar los municipios cuando se edita
        window.addEventListener('load', function() {
            var departamentoId = document.getElementById('departamento_id').value;
            var municipioSelect = document.getElementById('municipio_id');
            if (departamentoId) {
                fetch(`/padres/get-municipios/${departamentoId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(municipio => {
                            var option = document.createElement('option');
                            option.value = municipio.id;
                            option.text = municipio.nombre;
                            if (municipio.id == '{{ $padre->municipio_id }}') {
                                option.selected = true;
                            }
                            municipioSelect.add(option);
                        });
                    });
            }
        });
    </script>
@endsection
