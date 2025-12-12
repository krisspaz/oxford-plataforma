<!-- resources/views/convenios/edit.blade.php -->
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
<div class="container">
    <h1>Editar Convenio</h1>

    <form action="{{ route('convenios.update', $convenio->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="estudiante_id">Estudiante</label>
            <select name="estudiante_id" id="estudiante_id" class="form-control" required>
                <option value="">Seleccione un Estudiante</option>
                @foreach ($estudiantes as $estudiante)
                    <option value="{{ $estudiante->id }}" {{ $convenio->estudiante_id == $estudiante->id ? 'selected' : '' }}>{{ $estudiante->persona->nombres }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="monto_total">Monto Total</label>
            <input type="number" name="monto_total" id="monto_total" class="form-control" value="{{ $convenio->monto_total }}" required>
        </div>

        <div class="form-group">
            <label for="cantidad_cuotas">Cantidad de Cuotas</label>
            <input type="number" name="cantidad_cuotas" id="cantidad_cuotas" class="form-control" value="{{ $convenio->cantidad_cuotas }}" required min="1">
        </div>

        <div class="form-group">
            <label for="fecha_inicio">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" 
            value="{{ \Carbon\Carbon::parse($convenio->fecha_inicio)->format('Y-m-d') }}" required>
        </div>

        <div class="form-group">
            <label for="fecha_fin">Fecha de Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" 
            value="{{ \Carbon\Carbon::parse($convenio->fecha_fin)->format('Y-m-d') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection
