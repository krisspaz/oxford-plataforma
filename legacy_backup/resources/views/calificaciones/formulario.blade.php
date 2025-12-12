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
            <h3 class="panel-title">Calificación de Tareas</h3>
        </div>
        <div class="panel-body">

            {{-- ✅ MENSAJES DE ERROR Y ÉXITO --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            {{-- ✅ FORMULARIO DE BÚSQUEDA --}}
            <form action="{{ route('tareas.buscar') }}" method="GET">
                <div class="form-group">
                    <label for="estudiante_id">Seleccionar Estudiante:</label>
                    <select name="estudiante_id" id="estudiante_id" class="form-control" required style="width: 40%">
                        <option value="">Seleccione un estudiante</option>
                        @foreach($estudiantes as $estudiante)
                            <option value="{{ $estudiante->id }}">
                                {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>



                <div class="form-group">
                    <label for="bimestre_id">Bimestre:</label>
                    <select name="bimestre_id" id="bimestre_id" class="form-control" style="width: 30%" required>
                        <option value="">Seleccione un bimestre</option>
                        @foreach($bimestres as $bimestre)
                            <option value="{{ $bimestre->id }}">{{ $bimestre->nombre }} {{"-"}} {{ $bimestre->gestion->ciclo_escolar }}</option>
                        @endforeach
                    </select>
                </div>


                {{-- ✅ SELECCIÓN DE CICLO ESCOLAR --}}
                <div class="form-group">
                    <label for="anio_ciclo_escolar">Ciclo Escolar:</label>
                    <select name="anio_ciclo_escolar" id="anio_ciclo_escolar" class="form-control" required style="width: 30%">
                        <option value="">Seleccione un año</option>
                        @foreach($ciclos->unique('ciclo_escolar') as $ciclo)
                        <option value="{{ $ciclo->ciclo_escolar }}">
                            {{ $ciclo->ciclo_escolar }}
                        </option>
                    @endforeach
                        {{-- Agrega más años si es necesario --}}
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>
        </div>
    </div>
@endsection
