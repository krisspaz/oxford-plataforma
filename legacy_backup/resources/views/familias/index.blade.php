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
        <h3 class="box-title">Listado de Familias</h3>
    </div>
    <div class="box-body">
        <!-- Campo de búsqueda -->
        <div class="form-group">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar familia...">
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Código de Familia</th>
                        <th>Nombre de Familia</th>
                        <th>Padre</th>
                        <th>Madre</th>
                        <th>Estudiantes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaFamilias">
                    @foreach ($familias as $codigoFamiliar => $familiaCollection)
                        @php
                            $familia = $familiaCollection->first();
                            $estudiantesMostrados = [];
                        @endphp
                        <tr>
                            <td><strong>{{ $codigoFamiliar }}</strong></td>
                            <td><strong>{{ $familia->nombre_familiar }}</strong></td>
                            <td>{{ $familia->padre->nombres ?? 'No Posee' }} {{ $familia->padre->apellidos ?? '' }}</td>
                            <td>{{ $familia->madre->nombres ?? 'No Posee' }} {{ $familia->madre->apellidos ?? '' }}</td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($familiaCollection as $familia)
                                        @foreach ($familia->estudiantes2 as $estudiante)
                                            @php
                                                $nombre = $estudiante->persona->nombres ?? 'Nombre no disponible';
                                                $apellidos = $estudiante->persona->apellidos ?? 'Apellido no disponible';
                                            @endphp
                                            @if (!in_array($nombre, $estudiantesMostrados))
                                                <li>📚 {{ $nombre }} {{ $apellidos }}</li>
                                                @php
                                                    $estudiantesMostrados[] = $nombre;
                                                @endphp
                                            @endif
                                        @endforeach
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <a href="{{ route('familias.show',$familia->id) }}" class="btn btn-xs btn-info">
                                    <i class="fa fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script para el filtro de búsqueda -->
<script>
    document.getElementById("buscador").addEventListener("keyup", function() {
        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll("#tablaFamilias tr");

        filas.forEach(fila => {
            let textoFila = fila.innerText.toLowerCase();
            fila.style.display = textoFila.includes(filtro) ? "" : "none";
        });
    });
</script>
@endsection
