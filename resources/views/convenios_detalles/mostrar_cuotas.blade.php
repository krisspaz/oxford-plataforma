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
<div class="panel panel-default">
    <div class="panel-heading">
        <h3>Mostrar Cuotas del Convenio</h3>
    </div>

    <div class="panel-body">
        <h4><strong>Estudiante:</strong> {{ $convenio->inscripcion->estudiante->persona->nombres }} {{ $convenio->inscripcion->estudiante->persona->apellidos }}</h4>
        <h4><strong>Paquete Seleccionado:</strong> {{ $convenio->inscripcion->paquete->nombre }}</h4>

        <hr>

        <h4>Detalle de las Cuotas</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Producto Seleccionados</th>
                    <th>Monto Total</th>
                    <th>Fecha de Vencimiento</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($convenio->cuotas as $cuota)
                <tr>
                    <td>
                        @if (strtolower($cuota->productoSeleccionado->detalle->nombre ?? '') == 'mensualidad')
                            @php
                                // Obtener el mes en español según la fecha de vencimiento
                                $mes = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->translatedFormat('F'); 
                            @endphp
                            Mensualidad {{ ucfirst($mes) }}
                        @else
                            {{ $cuota->productoSeleccionado->detalle->nombre ?? 'N/A' }}
                        @endif
                    </td>

                    <td>{{ number_format($cuota->monto_cuota, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}</td>
                </tr>
            @endforeach
               
            </tbody>
        </table>

        <!-- Botón de editar debajo de la tabla -->
        <div class="text-center">
            <a href="{{ route('convenios_detalles.edit', $convenio->id) }}" class="btn btn-primary">Editar Convenio</a>
        </div>
    </div>
</div>
@endsection
