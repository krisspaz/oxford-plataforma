@extends('crudbooster::admin_template')

@section('content')
    <p><a href="{{ route('ajuste-administrativos.create') }}" class="btn btn-success">Agregar Administrativo</a></p>

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
        <div class="box-header with-border"><h3 class="box-title">Listado de Administrativos</h3></div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Estado</th>
                        <th>Fotografía</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($administrativos as $admin)
                        <tr>
                            <td>{{ $admin->id }}</td>
                            <td>{{ $admin->persona->nombres ?? '' }} {{ $admin->persona->apellidos ?? '' }}</td>
                            <td>{{ $admin->cargo->nombre ?? '' }}</td>
                            <td>{{ $admin->estado->estado ?? '' }}</td>
                            <td>
                                @if($admin->fotografia_administrativo)
                                    <img src="{{ asset('storage/' . $admin->fotografia_administrativo) }}" width="50" height="50">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ajuste-administrativos.show', $admin->id) }}" class="btn btn-xs btn-info">Ver</a>
                                <a href="{{ route('ajuste-administrativos.edit', $admin->id) }}" class="btn btn-xs btn-warning">Editar</a>
                                <form action="{{ route('ajuste-administrativos.destroy', $admin->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
