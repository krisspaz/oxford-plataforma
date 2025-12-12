@extends('crudbooster::admin_template')
@section('content')


<div class="box box-default">

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
        <h3 class="box-title">Editar Pago</h3>
    </div>
    <form method="POST" action="{{ route('reporte_pagos.update', $reporte_pago->id) }}">
        @csrf
        @method('PUT')
        <div class="box-body">
            <div class="form-group">
                <label>Convenio</label>
                <select name="convenio_id" class="form-control" required style="width: 30%">
                    <option value="">Seleccione convenio</option>
                    @foreach($convenios as $convenio)
                        <option value="{{ $convenio->id }}" {{ $reporte_pago->convenio_id == $convenio->id ? 'selected' : '' }}>
                            {{ $convenio->nombre ?? 'Convenio #' . $convenio->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Cuota (opcional)</label>
                <select name="cuota_id" class="form-control select2" style="width: 30%">
                    <option value="">-- Sin cuota --</option>
                    @foreach($cuotas as $cuota)
                        <option value="{{ $cuota->id }}" {{ $reporte_pago->cuotas->contains('id', $cuota->id) ? 'selected' : '' }}>
                            {{ $cuota->descripcion ?? 'Cuota #' . $cuota->id }}  {{ $cuota->descripcion  . $cuota->productoSeleccionado->detalle->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <label>Exonerar</label>
                <select name="tipo_pago" class="form-control" style="width: 30%">
                    <option value="completo" {{ $reporte_pago->tipo_pago == "completo" ? 'selected' : '' }}>Completo</option>
                    <option value="abono" {{ $reporte_pago->tipo_pago == "abono" ? 'selected' : '' }}>Abono</option>
                </select>
            </div>

            <div class="form-group">
                <label>Monto</label>
                <input type="number" name="monto" class="form-control" step="0.01" min="0" value="{{ $reporte_pago->monto }}" required style="width: 30%">
            </div>

       
            <div class="form-group">
                <label>Exonerar</label>
                <select name="exonerar" class="form-control" style="width: 30%">
                    <option value="No" {{ $reporte_pago->exonerar == "No" ? 'selected' : '' }}>No</option>
                    <option value="Si" {{ $reporte_pago->exonerar == "Si" ? 'selected' : '' }}>Sí</option>
                </select>
            </div>

            @php
            use Carbon\Carbon;
        @endphp
        
            <div class="form-group">
                <label>Fecha de Pago</label>
                <input type="date" name="fecha_pago" class="form-control"
                value="{{ $reporte_pago->fecha_pago ? Carbon::parse($reporte_pago->fecha_pago)->format('Y-m-d') : '' }}"
                required style="width: 30%">

            </div>
        </div>
        

        <div class="box-footer">
            <button class="btn btn-primary" type="submit">Actualizar</button>
            <a href="{{ route('reporte_pagos.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>

@push('bottom')
<!-- Select2 CSS & JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        theme: 'bootstrap4', // Opcional, puedes quitar si no usas Bootstrap 4
        placeholder: 'Seleccione',
        allowClear: true,
        theme: 'bootstrap4'
    });
});
</script>
@endpush

@endsection
