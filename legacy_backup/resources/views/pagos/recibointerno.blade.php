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
        <h3 class="box-title" style="text-align: center">Generar Recibo</h3>
    </div>
    <div class="box-body">
        <form action="{{ route('recibo.generar') }}" method="POST">
            @csrf

            <!-- Fila 1: NIT, Nombre -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nit">NIT:</label>
                        <input type="text" name="nit" id="nit" class="form-control" placeholder="Ingrese el NIT" required>
                        <button type="button" class="btn btn-info" id="buscar-btn">Buscar NIT</button>
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
                        <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Ingrese la dirección" required>
                    </div>
                </div>
            </div>

         
          
           
          
            <!-- Ítems -->
            <div id="items-container" class="form-group">
                @foreach ($items as $index => $item)

                @foreach ($item['pagoId'] as $pagoId)


                <input type="hidden" class="pago-id" name="pago_id[{{ $index }}][]" value="{{ $pagoId }}">
            @endforeach

         
                <div class="item row align-items-end mt-3">
                    <div class="col-md-2">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" name="items[{{ $index }}][cantidad]" class="form-control" value="{{ $item['cantidad'] }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="descripcion">Descripción:</label>
                        <input type="text" name="items[{{ $index }}][descripcion]" class="form-control" value="{{ $item['descripcion'] }}" required readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="precio_unitario">Precio Unitario:</label>
                        <input type="number" name="items[{{ $index }}][precio_unitario]" class="form-control" step="0.01" value="{{ $item['precio_unitario'] }}" required readonly>
                    </div>
                    <div class="col-md-1 text-right">
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarItem(this)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
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
    let itemIndex = {{ count($items) }};

    function agregarItem() {
        const container = document.getElementById('items-container');
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('item', 'row', 'align-items-end', 'mt-3');
        itemDiv.innerHTML = `
            <div class="col-md-2">
                <label for="cantidad">Cantidad:</label>
                <input type="number" name="items[${itemIndex}][cantidad]" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="descripcion">Descripción:</label>
                <input type="text" name="items[${itemIndex}][descripcion]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="precio_unitario">Precio Unitario:</label>
                <input type="number" name="items[${itemIndex}][precio_unitario]" class="form-control" step="0.01" required>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nitInput = document.getElementById('nit');
        const clienteInput = document.getElementById('nombre');
        const buscarBtn = document.getElementById('buscar-btn');
 
        // Función para buscar el nombre del cliente usando el NIT
        const buscarCliente = async () => {
            const nit = nitInput.value.trim(); // Eliminar espacios en blanco
 
            if (nit.length > 0) {
                try {
                    const response = await fetch(`/consultar-nit/${nit}`);
                    const data = await response.json();
 
                    if (data.success) {
                        clienteInput.value = data.nombre; // Asignar nombre al campo cliente
                    } else {
                        clienteInput.value = ''; // Limpiar si el NIT no se encuentra
                        alert("NIT no encontrado");
                    }
                } catch (error) {
                    console.error("Error al consultar el NIT:", error);
                    alert("Hubo un error al consultar el NIT. Intente nuevamente.");
                    clienteInput.value = '';
                }
            } else {
                alert("Por favor ingrese un NIT válido.");
            }
        };
 
        // Evento del botón "Buscar NIT"
        buscarBtn.addEventListener('click', buscarCliente);
    });
 </script>
@endpush
