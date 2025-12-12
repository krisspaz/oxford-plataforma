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
    

    <h1>Crear Detalle de Paquete</h1>
    <form action="{{ route('paquete_detalles.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="paquete_id" class="form-label">Paquete</label>
            <select name="paquete_id" id="paquete_id" class="form-control" required>
                <option value="">Seleccione un paquete</option>
                @foreach ($paquetes as $paquete)
                    <option value="{{ $paquete->id }}">{{ $paquete->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripcion</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" >
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" name="precio" id="precio" class="form-control" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="tipo_comprobante" class="form-label">Tipo de Comprobante a Emitir</label>
            <select name="tipo_comprobante" id="tipo_comprobante" class="form-control" required>
                <option value="">Seleccione el Tipo de Comprobante</option>
              
                    <option value="Factura">Factura Electronica SAT</option>
                    <option value="Recibo">Recibo Electronico SAT</option>
                    <option value="Comprobante">Recibo Interno</option>
                 
            </select>
        </div>

        <div class="mb-3">
            <label for="tipo_producto" class="form-label">Tipo de Comprobante a Emitir</label>
            <select name="tipo_producto" id="tipo_producto" class="form-control" required>
                <option value="">Seleccione el Tipo de Producto</option>
              
                    <option value="S">Servicio</option>
                    <option value="B">Bien</option>
                  
                 
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('paquete_detalles.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>




@endsection
