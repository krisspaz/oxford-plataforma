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
    <h3 class="box-title">Listado de Pagos</h3>
</div>

<div class="box-body">

    <!-- Filtros de Identificación -->

    <!-- Total filtrado -->
    @if(request('fecha') || request('tipo_pago'))
        <div class="alert alert-info">
            <strong>Total de ingresos filtrados:</strong> Q. {{ number_format($totalFiltrado, 2) }}
        </div>
    @endif

    <!-- Botones de exportación -->
    <a href="{{ route('reporte_pagos.export_excel', request()->all()) }}" class="btn btn-success">
        <i class="fa fa-file-excel-o"></i> Exportar a Excel
    </a>
    <a href="{{ route('reporte_pagos.export_pdf', request()->all()) }}" class="btn btn-danger">
        <i class="fa fa-file-pdf-o"></i> Exportar a PDF
    </a>

    <!-- Tabla -->
    <table id="tablaPagos" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Inscripción Ref.</th>
                <th>Convenio Ref.</th>
                <th>Tipo de Pago</th>
                <th>Monto</th>
                <th>Exonerado</th>
                <th>Fecha de Pago</th>
                <th>Comprobante</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
            <tr>
                <td>{{ $pago->id }}</td>
                <td>{{ $pago->convenio->inscripcion->estudiante->carnet }}
                    {{ $pago->convenio->inscripcion->estudiante->persona->nombres }}
                    {{ $pago->convenio->inscripcion->estudiante->persona->apellidos }}</td>
                <td>{{ $pago->convenio->inscripcion->id }}</td>
                <td>{{ $pago->convenio->id }}</td>
                <td>{{ $pago->tipo_pago }}</td>
                <td>Q. {{ number_format($pago->monto, 2) }}</td>
                <td>{{ $pago->exonerar }}</td>
                <td>{{ $pago->fecha_pago }}</td>
                <td>
                    @if ($pago->facturaEmitida)
                        <a href="{{ route('reportes.facturas.descargarPDF', $pago->facturaEmitida->serie) }}" class="btn btn-xs btn-success">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </a>
                    @elseif ($pago->reciboEmitido)
                        <a href="{{ route('reportes.recibos.descargarPDF', $pago->reciboEmitido->serie) }}" class="btn btn-xs btn-success">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </a>
                    @elseif ($pago->recibointernoEmitido)
                        <a href="{{ route('reportes.recibosinternos.descargarPDF', $pago->recibointernoEmitido->serie) }}" class="btn btn-xs btn-success">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </a>
                    @else
                        <span class="label label-default">Sin comprobante</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('reporte_pagos.show', $pago->id) }}" class="btn btn-xs btn-info">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="{{ route('reporte_pagos.edit', $pago->id) }}" class="btn btn-xs btn-warning">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <form action="{{ route('reporte_pagos.destroy', $pago->id) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Seguro que desea eliminar este pago?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-xs btn-danger">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

<!-- Scripts -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#tablaPagos').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }, lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
    pageLength: 100,
        order: [[0, 'desc']]
    });

    const radios = document.querySelectorAll('input[name="tipo_identificacion"]');
    const inputNumero = document.getElementById('numero_identificacion');
    const btnBuscar = document.getElementById('buscarIdentificacion');
    const hiddenTipo = document.getElementById('tipoIdentificacionHidden');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            inputNumero.disabled = false;
            btnBuscar.disabled = false;
            hiddenTipo.value = this.value;
        });
    });

    btnBuscar.addEventListener('click', function() {
        const tipo = hiddenTipo.value;
        const numero = inputNumero.value.trim();

        if (!numero) {
            Swal.fire('Atención', 'Debe ingresar un número de ' + tipo, 'warning');
            return;
        }

        Swal.fire('Buscando...', tipo + ': ' + numero, 'info');
        // Aquí puedes agregar tu lógica para buscar el NIT o CUI en backend
    });
});
</script>

@endsection
