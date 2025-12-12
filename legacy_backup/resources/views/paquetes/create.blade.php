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
    <h1>Crear Paquete</h1>
    
    <!-- Mostrar errores si los hay -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario para crear el paquete -->
    <form action="{{ route('paquetes.store') }}" method="POST">
        @csrf
        
        <!-- Nombre -->
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
        </div>
        
        <!-- Descripción -->
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion">{{ old('descripcion') }}</textarea>
        </div>

        <!-- Precio -->
        <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" class="form-control" id="precio" name="precio" value="{{ old('precio') }}" required>
        </div>

        <!-- Selección de Cursos -->
        <div class="form-group">
            <label for="cursos">Selecciona los Cursos</label>
            <div>
                <!-- Checkbox para cada curso -->
                @foreach ($cursos as $curso)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="cursos[]" value="{{ $curso->curso_id }}" id="curso_{{ $curso->curso_id }}">
                        <label class="form-check-label" for="curso_{{ $curso->curso_id }}">
                            {{ $curso->curso }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Selección de Estado -->
        <div class="form-group">
            <label for="estado_id">Estado</label>
            <select class="form-control" id="estado_id" name="estado_id" required>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                        {{ $estado->estado }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Botón de Enviar -->
        <button type="submit" class="btn btn-primary">Crear Paquete</button>
    </form>
</div>
@endsection
