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
    <div class="box-header">
        <h3 class="box-title">Registrar Pago</h3>
    </div>
    <div class="box-body">
        <form method="POST" action="{{ CRUDBooster::mainpath('registrar') }}">
            @csrf
            <div class="form-group">
                <label for="estudiante_id">ID del Estudiante</label>
                <input type="number" class="form-control" id="estudiante_id" name="estudiante_id" placeholder="Ingrese el ID del estudiante" required>
            </div>

            <div class="form-group">
                <label for="monto">Monto</label>
                <input type="number" step="0.01" class="form-control" id="monto" name="monto" placeholder="Ingrese el monto del pago" required>
            </div>

            <button type="submit" class="btn btn-success">Registrar Pago</button>
        </form>
    </div>
</div>

@if(session('success'))
    <script>
        Swal.fire('¡Éxito!', '{{ session('success') }}', 'success');
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire('Error', '{{ session('error') }}', 'error');
    </script>
@endif

@endsection
