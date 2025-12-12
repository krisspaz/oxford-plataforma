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
    <div class="box-header with-border">
        <h3 class="box-title">Agregar Pago</h3>
    </div>
    <form method="POST" action="{{ route('reporte_pagos.store') }}">
        @csrf
        <div class="box-body">
            <div class="form-group">
                <label>Convenio</label>
                <select name="convenio_id" class="form-control" required>
                    <option value="">Seleccione convenio</option>
                    @foreach($convenios as $convenio)
                        <option value="{{ $convenio->id }}">
                            {{ $convenio->nombre ?? 'Convenio #' . $convenio->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Cuota (opcional)</label>
                <select name="cuota_id" class="form-control">
                    <option value="">-- Sin cuota --</option>
                    @foreach($cuotas as $cuota)
                        <option value="{{ $cuota->id }}">
                            {{ $cuota->descripcion ?? 'Cuota #' . $cuota->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Tipo de Pago</label>
                <input type="text" name="tipo_pago" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Monto</label>
                <input type="number" name="monto" class="form-control" required step="0.01" min="0">
            </div>

            <div class="form-group">
                <label>Exonerar</label>
                <select name="exonerar" class="form-control">
                    <option value="0">No</option>
                    <option value="1">Sí</option>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha de Pago</label>
                <input type="date" name="fecha_pago" class="form-control" required>
            </div>
        </div>
        <div class="box-footer">
            <button class="btn btn-primary" type="submit">Guardar</button>
            <a href="{{ route('reporte_pagos.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>

@endsection
