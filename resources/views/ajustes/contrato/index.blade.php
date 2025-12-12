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
        <h3 class="panel-title">Contratos de Estudiantes</h3>

        
    </div>

    <div class="panel-body">
        <div id="filtro-letras" style="margin-bottom: 15px;"></div>

        <table class="table table-striped" id="datatable">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Carné</th>
                    <th>Inscripción</th>
                    <th>Ciclo Escolar</th>
               
                   
                   
                    <th>Contrato</th>
                    <th>Contrato Firmado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contratos as $contrato)
                    <tr>
                        <td>{{ $contrato->estudiante->persona->nombres }} {{ $contrato->estudiante->persona->apellidos }}</td>
                        <td>
                            {{ $contrato->estudiante->carnet }}
                        </td>
                       
                        <td>
                            {{ $contrato->contrato->inscripcion_id }}
                        </td>

                        <td>
                            {{ $contrato->contrato->inscripcion->ciclo_escolar }}
                        </td>
                      
                      
                        <td>
                            @if ($contrato->contrato && $contrato->contrato->archivo)
                                <a href="{{ asset('storage/' . $contrato->contrato->archivo) }}" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="fa fa-download"></i> Descargar
                                </a>
                            @else
                                <span class="label label-default">No disponible</span>
                            @endif
                        </td>
                        <td>

                                        <!-- Descargar contrato si ya está generado -->
                                @if ($contrato->contrato && $contrato->contrato_firmado)
                                <a href="{{ asset('storage/' . $contrato->contrato_firmado) }}" class="btn btn-warning" target="_blank">Ver Contrato Firmado</a>
                            @else
                                <!-- Subir contrato firmado -->
                               
                                    <form action="{{ route('contratos.uploadSignedContract', $contrato->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="contrato_firmado" required class="form-control mb-2">
                                    <button type="submit" class="btn btn-primary">Subir Contrato Firmado</button>
                                </form>
                            @endif
                            <!-- Enlace para ver el contrato -->
                        </td>
                        <td>{{ $contrato->estado }}</td>
                        <td>

                          
                            <a href="{{ route('contrato.regenerar', ['inscripcion' => $contrato->contrato->inscripcion_id, 'numero' => $contrato->contrato->numero_contrato]) }}" class="btn btn-primary btn-sm" target="_blank" title="Recrear Contrato">
                                <i class="fa fa-refresh"></i> 
                            </a>
                           

                            <a href="{{ route('ajustes_contrato.edit', $contrato->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                <i class="fa fa-pencil"></i>
                            </a>

                            <form action="{{ route('ajustes_contrato.destroy', $contrato->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este contrato?')">
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
</div>



@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@push('bottom')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#datatable').DataTable({
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
                table.column(0).search(regex, true, false).draw();
            });
        });
    </script>
@endpush

@endsection
