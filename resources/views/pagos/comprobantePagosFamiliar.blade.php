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
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>
    <div class="box-body">

         <div class="panel panel-default p-4" style="border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
    <!-- Título principal -->
    <div class="panel-heading text-center mb-4">
        <h1 style="font-size: 2.2rem; font-weight: 800; color: #2c3e50; text-transform: uppercase; letter-spacing: 1px;">
            Pagos Sin Emitir Comprobantes
        </h1>
    </div>

    <div class="panel-body">
        <!-- Sección de Identificación -->
        <fieldset style="border: 1px solid #ccc; padding: 20px; border-radius: 10px; margin-bottom: 25px;">
            <legend style="font-size: 1.1rem; font-weight: 600; color: #34495e;">Tipo de Identificación</legend>

            <div class="form-group mb-3">
                <label class="form-label d-block mb-2"><strong>Seleccione tipo de identificación:</strong></label>

                <div style="display:flex; gap:1rem; align-items:center;">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_identificacion" id="radioNIT" value="NIT">
                        <label class="form-check-label" for="radioNIT">NIT</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_identificacion" id="radioCUI" value="CUI">
                        <label class="form-check-label" for="radioCUI">CUI</label>
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="nit" class="form-label d-block">Número de Identificación</label>
                <div style="display:flex; gap:8px; align-items:center; max-width: 50%;">
                    <input type="text" class="form-control" id="nit" name="nit"
                           placeholder="Seleccione un tipo de identificación"
                           disabled required style="flex:1; min-width:50px;">
                    <button type="button" class="btn btn-info" id="buscar-btn" disabled>Buscar</button>
                </div>
            </div>

            <!-- Hidden para enviar tipo de identificación -->
            <input type="hidden" id="tipo_identificacion_hidden" name="tipo_identificacion_hidden" value="">
        </fieldset>

        <!-- Datos del cliente -->
        <fieldset style="border: 1px solid #ccc; padding: 20px; border-radius: 10px;">
            <legend style="font-size: 1.1rem; font-weight: 600; color: #34495e;">Datos del Cliente</legend>

            <div class="form-group mb-3">
                <label for="cliente" class="form-label">Cliente</label>
                <input type="text" class="form-control" id="cliente" name="cliente"
                       placeholder="Ingrese el nombre del cliente" required style="width: 50%;">
            </div>

            <div class="form-group mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion"
                       placeholder="Ingrese la dirección del cliente" required style="width: 60%;">
            </div>
        </fieldset>
    </div>
</div>

            <div class="panel-body">

                <!-- Sección Factura -->
                <h4>Pagos con Factura</h4>
                <form method="POST" action="{{ route('pagos.generarPago') }}">
                    @csrf
                    <div class="checkbox">
                        <input type="hidden" name="nit" id="hiddenNit">
                        <input type="hidden" name="cliente" id="hiddenCliente">
                        <input type="hidden" name="direccion" id="hiddenDireccion">
                         <input type="hidden" name="tipo_identificacion" class="hiddenTipoIdentificacion">

            @foreach ($pagosAgrupados->groupBy('convenio_id') as $convenioId => $pagos)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @php
                            $alumnos = implode(', ', $pagos->first()['alumnos'] ?? ['Alumno no disponible']);
                        @endphp
                        <strong>{{ $alumnos }} (Convenio #{{ $convenioId }})</strong>
                    </div>
                    <div class="panel-body">
                        @foreach (['Factura', 'Recibo', 'Recibo Interno'] as $tipoComprobante)
                            <h4>Pagos con {{ $tipoComprobante }}</h4>
                            <form method="POST" action="{{ route('pagos.generarPago') }}">
                                @csrf
                                <input type="hidden" name="nit" class="hiddenNit">
                                <input type="hidden" name="cliente" class="hiddenCliente">
                                <input type="hidden" name="direccion" class="hiddenDireccion">
                                <input type="hidden" name="tipo_identificacion" class="hiddenTipoIdentificacion">
                                <input type="hidden" name="accion" value="generar{{ str_replace(' ', '', $tipoComprobante) }}">

                                @php $hayPagos = false; @endphp

                                <div class="checkbox">
                                    @foreach ($pagos as $pago)
                                        @foreach ($pago['cuotas'] as $cuota)
                                            @php
                                                $debeIrEnEsteBloque = false;

                                                if ($pago['exonerar'] === 'Si' && $tipoComprobante === 'Recibo Interno') {
                                                    $debeIrEnEsteBloque = true;
                                                } elseif ($pago['exonerar'] !== 'Si' && $cuota['producto_seleccionado']['comprobante'] === $tipoComprobante) {
                                                    $debeIrEnEsteBloque = true;
                                                }
                                            @endphp

                                            @if ($debeIrEnEsteBloque)
                                                @php $hayPagos = true; @endphp
                                                <label>
                                                    Pago #{{ $pago['id'] }} - {{ $pago['tipo_pago'] }}
                                                    - {{ $cuota['producto_seleccionado']['nombre'] }} -
                                                    {{ $cuota['mes_vencimiento'] }} - {{ $cuota['anio_vencimiento'] }}
                                                    - <strong>Monto Pagado:</strong> Q. {{ number_format($pago['monto'], 2) }}
                                                    - <strong>Exonerar:</strong> {{ $pago['exonerar'] }}
                                                </label>
                                                <br>
                                                <input type="hidden" name="pagos{{ str_replace(' ', '', $tipoComprobante) }}[]" value="{{ $pago['id'] }}">
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>

                                @if ($hayPagos)
                                    <button
                                        type="submit"
                                        id="btn{{ str_replace(' ', '', $tipoComprobante) }}"
                                        class="btn btn-{{ $tipoComprobante == 'Factura' ? 'primary' : ($tipoComprobante == 'Recibo' ? 'success' : 'warning') }}">
                                        Generar {{ $tipoComprobante }}
                                    </button>
                                @else
                                    <p class="text-muted">No hay pagos disponibles para este comprobante.</p>
                                @endif
                            </form>
                            <hr>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Scripts -->
<!-- 🔹 Script: Activar input según tipo -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const nitInput = document.getElementById("nit");
    const buscarBtn = document.getElementById("buscar-btn");
    const radioNIT = document.getElementById("radioNIT");
    const radioCUI = document.getElementById("radioCUI");

    nitInput.disabled = true;
    buscarBtn.disabled = true;

    [radioNIT, radioCUI].forEach(radio => {
        radio.addEventListener("change", function () {
            if (radioNIT.checked || radioCUI.checked) {
                nitInput.disabled = false;
                buscarBtn.disabled = false;
                nitInput.placeholder = radioNIT.checked ? "Ingrese el NIT" : "Ingrese el CUI";
            } else {
                nitInput.disabled = true;
                buscarBtn.disabled = true;
                nitInput.value = "";
            }
        });
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nitInput = document.getElementById('nit');
        const clienteInput = document.getElementById('cliente');
        const buscarBtn = document.getElementById('buscar-btn');

        // Función para buscar el nombre del cliente usando el NIT
        const buscarCliente = async () => {
            const nit = nitInput.value.trim();

            if (nit.length > 0) {
                try {
                    const response = await fetch(`/consultar-nit/${nit}`);
                    const data = await response.json();

                    if (data.success) {
                        clienteInput.value = data.nombre;
                    } else {
                        clienteInput.value = '';
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

        buscarBtn.addEventListener('click', buscarCliente);
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const nitInput = document.getElementById("nit");
        const clienteInput = document.getElementById("cliente");
        const direccionInput = document.getElementById("direccion");

        const hiddenNit = document.querySelectorAll("input[name='nit']");
        const hiddenCliente = document.querySelectorAll("input[name='cliente']");
        const hiddenDireccion = document.querySelectorAll("input[name='direccion']");

        function actualizarCamposOcultos() {
            hiddenNit.forEach(input => input.value = nitInput.value);
            hiddenCliente.forEach(input => input.value = clienteInput.value);
            hiddenDireccion.forEach(input => input.value = direccionInput.value);
        }

        nitInput.addEventListener("input", actualizarCamposOcultos);
        clienteInput.addEventListener("input", actualizarCamposOcultos);
        direccionInput.addEventListener("input", actualizarCamposOcultos);
    });
</script>

<!-- 🔹 Script: Sincronizar inputs ocultos -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const nitInput = document.getElementById("nit");
    const clienteInput = document.getElementById("cliente");
    const direccionInput = document.getElementById("direccion");
    const radioNIT = document.getElementById("radioNIT");
    const radioCUI = document.getElementById("radioCUI");

    const hiddenTipo = document.querySelectorAll(".hiddenTipoIdentificacion");
    const hiddenNit = document.querySelectorAll(".hiddenNit");
    const hiddenCliente = document.querySelectorAll(".hiddenCliente");
    const hiddenDireccion = document.querySelectorAll(".hiddenDireccion");

    function syncHiddenInputs() {
        const tipoSeleccionado = radioNIT.checked ? "NIT" : (radioCUI.checked ? "CUI" : "");
        hiddenTipo.forEach(input => input.value = tipoSeleccionado);
        hiddenNit.forEach(input => input.value = nitInput.value);
        hiddenCliente.forEach(input => input.value = clienteInput.value);
        hiddenDireccion.forEach(input => input.value = direccionInput.value);
    }

    [nitInput, clienteInput, direccionInput, radioNIT, radioCUI].forEach(input => {
        input.addEventListener("input", syncHiddenInputs);
        input.addEventListener("change", syncHiddenInputs);
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("form").forEach(form => {
            form.addEventListener("submit", function() {
                setTimeout(() => {
                    location.reload(); // Recargar la página después de enviar el formulario
                }, 30000); // Se espera un poco para que el servidor procese la solicitud
            });
        });
    });
</script>


@endsection
