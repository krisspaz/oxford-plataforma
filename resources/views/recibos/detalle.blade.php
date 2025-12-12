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
        <h3 class="box-title" style="text-align: center" >Generar Recibo Interno</h3>
    </div>
    <div class="box-body">
        <form action="{{ route('recibo.generar') }}" method="POST">
            @csrf

            <!-- Fila 1: NIT, Nombre -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nit">NIT:</label>
                        <input type="text" name="nit" id="nit" class="form-control" placeholder="Ingrese el NIT">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ingrese el nombre completo">
                    </div>
                </div>
            </div>

            <!-- Fila 2: Dirección -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Ingrese la dirección">
                    </div>
                </div>
            </div>

            <!-- Ítems -->
            
            <div id="items-container" class="form-group">
                <div class="item row align-items-end">
                    <div class="col-md-2">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" name="items[0][Cantidad]" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="descripcion">Descripción:</label>
                        <input type="text" name="items[0][Descripcion]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="preciounitario">Precio Unitario:</label>
                        <input type="number" name="items[0][PrecioUnitario]" class="form-control" step="0.01" required>
                    </div>
                    <div class="col-md-1 text-right">
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarItem(this)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm mt-3" onclick="agregarItem()">
                <i class="fa fa-plus-circle"></i> Agregar Ítem
            </button>

            <!-- Botón de enviar -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa fa-file-pdf-o"></i> Generar Recibo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('bottom')
<script>
    let itemIndex = 1;

    function agregarItem() {
        const container = document.getElementById('items-container');
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('item', 'row', 'align-items-end', 'mt-3');
        itemDiv.innerHTML = `
            <div class="col-md-2">
                <label for="cantidad">Cantidad:</label>
                <input type="number" name="items[${itemIndex}][Cantidad]" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="descripcion">Descripción:</label>
                <input type="text" name="items[${itemIndex}][Descripcion]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="PrecioUnitario">Precio Unitario:</label>
                <input type="number" name="items[${itemIndex}][PrecioUnitario]" class="form-control" step="0.01" required>
            </div>
            <div class="col-md-1 text-right">
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarItem(this)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(itemDiv);
        itemIndex++;
    }

    function eliminarItem(button) {
        const item = button.closest('.item');
        item.remove();
    }
</script>
@endpush
