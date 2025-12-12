@extends('crudbooster::admin_template')


@section('content')
    <h1>Crear Jornada-Dia-Horario</h1>

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

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('jornada-dia-horarios.store') }}" method="POST">
        @csrf
        <div>
            <label for="jornada_id">Jornada:</label>
            <select name="jornada_id" id="jornada_id" required>
                @foreach ($jornadas as $jornada)
                    <option value="{{ $jornada->id }}">{{ $jornada->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="dia_id">Dia:</label>
            <select name="dia_id" id="dia_id" required>
                @foreach ($dias as $dia)
                    <option value="{{ $dia->id }}">{{ $dia->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="horario_id">Horario:</label>
            <select name="horario_id" id="horario_id" required>
                @foreach ($horarios as $horario)
                    <option value="{{ $horario->id }}">{{ $horario->inicio }} - {{ $horario->fin }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="estado_id">Estado:</label>
            <select name="estado_id" id="estado_id" required>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Guardar</button>
    </form>
@endsection
