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
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        });
    </script>
    @endif


         <div id="filtro-letras" style="margin-bottom: 15px;"></div>

        <table id="tabla-contratos" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Carnet</th>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Contrato</th>
                    <th>Estado</th>
                    <th>Subir Contrato Firmado</th>
                </tr>
            </thead>

            <tbody>
                @foreach($contratos as $contrato)
                <tr>
                    <td>{{ $contrato->id }}</td>
                    <td>{{ $contrato->estudiante->carnet ?? 'N/A' }}</td>

                    <td>
                        {{ $contrato->estudiante->persona->nombres ?? 'N/A' }}
                        {{ $contrato->estudiante->persona->apellidos ?? '' }}
                    </td>

                    <td>{{ $contrato->contrato->ciclo_escolar }}</td>

                    <td>
                        @if ($contrato->contrato)
                            <a href="{{ asset('storage/' . $contrato->contrato->archivo) }}"
                               class="btn btn-success" target="_blank">
                                Ver Contrato
                            </a>
                        @else
                            <span>No disponible</span>
                        @endif
                    </td>

                    <td>{{ $contrato->estado }}</td>

                    <td>
                        {{-- Formulario para subir contrato firmado --}}
                        @if ($contrato->contrato && $contrato->contrato_firmado)
                            <a href="{{ asset('storage/' . $contrato->contrato_firmado) }}"
                               class="btn btn-primary" target="_blank">
                               Ver Contrato Firmado
                            </a>
                        @else
                            <form action="{{ route('estudiante_contratos.uploadSignedContract', $contrato->id) }}"
                                  method="POST" enctype="multipart/form-data">
                                @csrf

                                <input type="file" name="contrato_firmado" class="form-control mb-2" required>

                                <button type="submit" class="btn btn-warning btn-sm">
                                    Subir Contrato Firmado
                                </button>
                            </form>
                        @endif
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>


</div>

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@push('bottom')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>


<script>

    $(document).ready(function () {
       const table =  $('#tabla-contratos').DataTable({
            language: {
                    decimal: "",
                    emptyTable: "No hay datos disponibles en la tabla",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    infoEmpty: "Mostrando 0 a 0 de 0 entradas",
                    infoFiltered: "(filtrado de _MAX_ entradas totales)",
                    thousands: ",",
                    lengthMenu: "Mostrar _MENU_ entradas",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar:",
                    zeroRecords: "No se encontraron registros coincidentes",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    aria: {
                        sortAscending: ": activar para ordenar la columna ascendente",
                        sortDescending: ": activar para ordenar la columna descendente"
                    }
                }
            });

            // Crear botones A-Z + Todos
            const letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
            const contenedor = $('#filtro-letras');
            contenedor.append(`<button class="btn btn-info filtro-letra" data-letra="">Todos</button> `);
            letras.forEach(function(letra) {
                contenedor.append(`<button class="btn btn-default filtro-letra" data-letra="${letra}">${letra}</button> `);
            });

            // Filtrado por letra (columna 0 = Estudiante)
            $('.filtro-letra').on('click', function() {
                $('.filtro-letra').removeClass('btn-info').addClass('btn-default');
                $(this).removeClass('btn-default').addClass('btn-info');

                let letra = $(this).data('letra');
                let regex = letra ? '^' + letra : '';
                table.column(2).search(regex, true, false).draw();
            });
        });
    </script>
@endpush
@endsection
