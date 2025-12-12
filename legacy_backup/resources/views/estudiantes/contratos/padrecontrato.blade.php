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
        <h1>Contrato</h1>
    </div>
    <div class="box-body">
   
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Carnet</th>
                <th>Estudiante</th>
                <th>Ciclo Escolar</th>
                <th>Contrato</th>
                <th>Contrato Firmado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contratos as $contrato)
            <tr>
                <td>{{ $contrato->id }}</td>
                <td>{{ $contrato->estudiante->carnet ?? 'N/A' }}</td>
                <td>{{ $contrato->estudiante->persona->nombres ?? 'N/A' }}{{""}}{{ $contrato->estudiante->persona->apellidos ?? 'N/A' }}</td>
                <td>{{ $contrato->contrato->ciclo_escolar }}</td>
                <td>
                    @if ($contrato->contrato)
                        <a href="{{ asset('storage/' . $contrato->contrato->archivo) }}" class="btn btn-success" target="_blank">Ver Contrato</a>
                    @else
                        <span>No disponible</span>
                    @endif
                </td>
            
                <td>
                    <!-- Descargar contrato si ya está generado -->
                    @if ($contrato->contrato && $contrato->contrato_firmado)
                        <a href="{{ asset('storage/' . $contrato->contrato_firmado) }}" class="btn btn-warning" target="_blank">Ver Contrato Firmado</a>
                    @else
                    <span>No disponible</span>
                    @endif
                    <!-- Enlace para ver el contrato -->
                  
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
</div>
@endsection
