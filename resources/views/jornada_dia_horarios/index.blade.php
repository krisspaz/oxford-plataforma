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
    <h1>Jornada-Dia-Horarios</h1>
    <a href="{{ route('jornada-dia-horarios.create') }}">Crear Jornada-Dia-Horario</a>

    @if ($message = Session::get('success'))
        <div>
            {{ $message }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Jornada</th>
                <th>Dia</th>
                <th>Horario</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jornadaDiaHorarios as $jornadaDiaHorario)
                <tr>
                    <td>{{ $jornadaDiaHorario->id }}</td>
                    <td>{{ $jornadaDiaHorario->jornada->nombre }}</td>
                    <td>{{ $jornadaDiaHorario->dia->nombre }}</td>
                    <td>{{ $jornadaDiaHorario->horario->inicio }} - {{ $jornadaDiaHorario->horario->fin }}</td>
                    <td>{{ $jornadaDiaHorario->estado->estado }}</td>
                    <td>
                        <a href="{{ route('jornada-dia-horarios.show', $jornadaDiaHorario->id) }}">Ver</a>
                        <a href="{{ route('jornada-dia-horarios.edit', $jornadaDiaHorario->id) }}">Editar</a>
                        <form action="{{ route('jornada-dia-horarios.destroy', $jornadaDiaHorario->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
