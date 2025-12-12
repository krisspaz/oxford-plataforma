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
        <h1 class="box-title">Asignar Convenio a la Inscripción</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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

    <form action="{{ route('convenios.store') }}" method="POST">
        @csrf

        <div class="box-body">
            <input type="hidden" name="inscripcion_id" value="{{ $inscripcion->id }}">

            <div class="form-group">
                <label for="estudiante">Estudiante</label>
                <input type="text" class="form-control" id="estudiante" value="{{ $inscripcion->estudiante->persona->nombres }} {{ $inscripcion->estudiante->persona->apellidos }}" readonly>
            </div>

            <div class="form-group">
                <label for="paquete">Paquete Seleccionado</label>
                <input type="text" class="form-control" id="paquete" value="{{ $inscripcion->paquete->nombre }}" readonly>
            </div>

            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio del Convenio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Fecha de Fin del Convenio</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
            </div>

            <div class="form-group">
                <label for="estado">Estado del Convenio</label>
                <select class="form-control" id="estado" name="estado" required>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                    <option value="pendiente">Pendiente</option>
                </select>
            </div>

            <!-- Mostrar los productos seleccionados -->
            <div class="form-group">
                <label for="productos">Productos Seleccionados</label>
                <ul>
                    @foreach ($inscripcion->productosSeleccionados as $producto)
                        <li>
                            {{ $producto->detalle->nombre ?? 'Producto sin detalle' }}
                            <input type="hidden" name="productos[]" value="{{ $producto->id }}">
                        </li>
                    @endforeach
                </ul>
            </div>

            <button type="submit" class="btn btn-success">Guardar Convenio</button>
            <a href="{{ route('convenios.index') }}" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>
@endsection
