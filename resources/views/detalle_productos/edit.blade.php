<!-- resources/views/detalle_productos/edit.blade.php -->
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
    <h1>Editar Detalle de Producto</h1>

    <form action="{{ route('detalle_productos.update', $detalleProducto->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="paquete_detalle_id" class="form-label">Paquete Detalle</label>
            <select name="paquete_detalle_id" id="paquete_detalle_id" class="form-control">
                @foreach ($paqueteDetalles as $paquete)
                    <option value="{{ $paquete->id }}" {{ $detalleProducto->paquete_detalle_id == $paquete->id ? 'selected' : '' }}>
                        {{ $paquete->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="producto_id" class="form-label">Producto</label>
            <select name="producto_id" id="producto_id" class="form-control">
                @foreach ($productos as $producto)
                    <option value="{{ $producto->id }}" {{ $detalleProducto->producto_id == $producto->id ? 'selected' : '' }}>
                        {{ $producto->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" value="{{ $detalleProducto->cantidad }}">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('detalle_productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
