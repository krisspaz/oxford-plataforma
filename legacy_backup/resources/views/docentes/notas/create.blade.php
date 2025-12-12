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
        <h3 class="box-title">Agregar Notas por Alumno</h3>
    </div>
    <div class="box-body">
        <form action="{{ route('cuadro-notas.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="materia_id">Materia</label>
                <select name="materia_id" class="form-control" required>
                    <option value="">Seleccione una materia</option>
                    @foreach($materias as $materia)
                        <option value="{{ $materia->id }}">{{ $materia->gestionMateria->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="bimestre">Bimestre</label>
                <select name="bimestre" class="form-control" required>
                    @foreach($bimestres as $bimestre)
                        <option value="{{ $bimestre->nombre }}" {{ $bimestre->id == $bimestreActual->id ? 'selected' : '' }}>
                            {{ $bimestre->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="ciclo_escolar">Ciclo Escolar</label>
                <input type="text" name="ciclo_escolar" class="form-control" value="{{ date('Y') }}" required>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Nota Final del Bimestre</th>
                      
                    </tr>
                </thead>
                <tbody>
                    @foreach($estudiantes as $index => $estudiante)
                        <tr>
                            <td>
                                {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}
                                <input type="hidden" name="notas[{{ $index }}][estudiante_id]" value="{{ $estudiante->id }}">
                            </td>
                          
                            <td>
                                <input type="number" name="notas[{{ $index }}][nota_acumulada]" class="form-control" min="0" max="100" step="0.01" required>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-success">Guardar Todas las Notas</button>
        </form>
    </div>
</div>
@endsection
