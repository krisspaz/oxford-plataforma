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
        <h3 class="box-title">Detalle de Facturación</h3>
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

                        @foreach ($datosPagos as $pago)
                        @if (!empty($pago['cuotas']) && $pago['cuotas'][0]['producto_seleccionado']['comprobante'] === 'Factura' && $pago['exonerar'] != 'Si')

                                <label>
                                    Pago #{{ $pago['id'] }} - {{ $pago['inscripcion']['estudiante']['persona']->nombres }}
                                    {{ $pago['inscripcion']['estudiante']->persona->apellidos }}
                                    @foreach ($pago['cuotas'] as $cuota)
                                        @foreach ($pago['metodos_de_pago'] as $metodo)
                                            - {{ $cuota['producto_seleccionado']['nombre'] }} {{ $cuota['mes_vencimiento'] }} {{ $cuota['anio_vencimiento'] }}
                                            - <strong>Monto:</strong> Q. {{ number_format($metodo['monto'], 2) }}
                                            - <strong>Metodo:</strong> {{ $metodo['metodo']}}
                                            - <strong>Tipo de Pago:</strong> {{ $pago['tipo_pago']}}
                                            @if(!empty($pago['exonerar']))
                                            Exonerar: {{ $pago['exonerar'] }}
                                        @endif
                                        @endforeach
                                    @endforeach
                                    <!-- Incluimos el pago mediante un input oculto -->
                                    <input type="hidden" name="pagosFactura[]" value="{{ $pago['id'] }}">
                                </label>
                                <br>
                            @endif
                        @endforeach
                    </div>
                    <input type="hidden" name="accion" value="generarFactura">
                    <button type="submit" class="btn btn-primary" id="btnFactura">Generar Factura</button>
                </form>

                <hr>

                <!-- Sección Recibo -->
                <h4>Pagos con Recibo</h4>
                <form method="POST" action="{{ route('pagos.generarPago') }}">
                    @csrf
                    <div class="checkbox">
                        <input type="hidden" name="nit" id="hiddenNit">
                        <input type="hidden" name="cliente" id="hiddenCliente">
                        <input type="hidden" name="direccion" id="hiddenDireccion">
                          <input type="hidden" name="tipo_identificacion" class="hiddenTipoIdentificacion">
                        @foreach ($datosPagos as $pago)
                            @if (!empty($pago['cuotas']) && $pago['cuotas'][0]['producto_seleccionado']['comprobante'] === 'Recibo' && $pago['exonerar'] != 'Si')
                                <label>
                                    Pago #{{ $pago['id'] }} - {{ $pago['inscripcion']['estudiante']['persona']->nombres }}
                                    {{ $pago['inscripcion']['estudiante']->persona->apellidos }}
                                    @foreach ($pago['cuotas'] as $cuota)
                                        @foreach ($pago['metodos_de_pago'] as $metodo)
                                            - {{ $cuota['producto_seleccionado']['nombre'] }} {{ $cuota['mes_vencimiento'] }} {{ $cuota['anio_vencimiento'] }}
                                            - <strong>Monto:</strong> Q. {{ number_format($metodo['monto'], 2) }}
                                            - <strong>Metodo:</strong> {{ $metodo['metodo']}}
                                            - <strong>Tipo de Pago:</strong> {{ $pago['tipo_pago']}}
                                            @if(!empty($pago['exonerar']))
                                                Exonerar: {{ $pago['exonerar'] }}
                                            @endif
                                        @endforeach
                                    @endforeach
                                    <input type="hidden" name="pagosRecibo[]" value="{{ $pago['id'] }}">
                                </label>
                                <br>
                            @endif
                        @endforeach
                    </div>
                    <input type="hidden" name="accion" value="generarRecibo">
                    <button type="submit" class="btn btn-success" id="btnRecibo">Generar Recibo</button>
                </form>

                <hr>

                <!-- Sección Recibo Interno -->
                <h4>Pagos con Recibo Interno</h4>
                <form method="POST" action="{{ route('pagos.generarPago') }}">
                    @csrf
                    <div class="checkbox">
                        <input type="hidden" name="nit" id="hiddenNit">
                        <input type="hidden" name="cliente" id="hiddenCliente">
                        <input type="hidden" name="direccion" id="hiddenDireccion">
                        @foreach ($datosPagos as $pago)
                            @if (!empty($pago['cuotas']) && ($pago['cuotas'][0]['producto_seleccionado']['comprobante'] === 'Recibo Interno' || $pago['exonerar'] === 'Si'))
                                <label>
                                    Pago #{{ $pago['id'] }} - {{ $pago['inscripcion']['estudiante']['persona']->nombres }}
                                    {{ $pago['inscripcion']['estudiante']->persona->apellidos }}
                                    @foreach ($pago['cuotas'] as $cuota)
                                        @foreach ($pago['metodos_de_pago'] as $metodo)
                                            - {{ $cuota['producto_seleccionado']['nombre'] }} {{ $cuota['mes_vencimiento'] }} {{ $cuota['anio_vencimiento'] }}
                                            - <strong>Monto:</strong> Q. {{ number_format($metodo['monto'], 2) }}
                                            - <strong>Metodo:</strong> {{ $metodo['metodo']}}
                                            - <strong>Tipo de Pago:</strong> {{ $pago['tipo_pago']}}
                                            @if(!empty($pago['exonerar']))
                                            <strong> Exonerar: </strong>{{ $pago['exonerar'] }}
                                                @endif
                                        @endforeach
                                    @endforeach
                                    <input type="hidden" name="pagosReciboInterno[]" value="{{ $pago['id'] }}">
                                </label>
                                <br>
                            @endif
                        @endforeach
                    </div>
                    <input type="hidden" name="accion" value="generarReciboInterno">
                    <button type="submit" class="btn btn-warning" id="btnReciboInterno">Generar Recibo Interno</button>
                </form>
            </div>
        </div>

    </div>
</div>


  <!-- Modal Padre-->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">New message</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form action="{{ route('pagos.generarPago') }}" method="POST">
            @csrf

            @include('pagos.forms.forms_emitirfactura', ['prefijo' => 'factura'])



            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Send message</button>
        </div>
      </div>
    </div>
  </div>


<script>
    // Ya no es necesaria la función updateButtonStatus puesto que todos los pagos vienen preseleccionados
    window.onload = function() {
        // Puedes remover estas líneas si no se utilizan
    };
</script>


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
