<!-- Formulario de búsqueda -->
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
        <h3 class="box-title">Buscar Estudiante</h3>
    </div>
    <div class="box-body">
        <form method="GET" action="{{ route('cuadro-notas.notaspadres') }}">
            @csrf



                <div class="form-group">
                    <label for="estudiante_id">Seleccionar Estudiante:</label>
                    <select name="estudiante_id" id="vestudiante_id" class="form-control" required style="width: 40%">
                        <option value="">Seleccione un estudiante</option>
                        @foreach($estudiantes as $estudiante)
                            <option value="{{ $estudiante->id }}">
                                {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>


              <div class="form-group">
                <label for="promedio">Tipo de Promedio</label>
                <select name="promedio" class="form-control" required style="width: 30%">
                    <option value="">Seleccione el Tipo de Promedio </option>

                        <option value="puntuacion">{{ "Nota Bimestral" }}</option>
                         <option value="porcentaje">{{ "Porcentaje Bimestral" }}</option>

                </select>
            </div>




            <div class="form-group">
                <label for="Ciclo_escolar">Ciclo_Escolar:</label>
                <select name="ciclo_escolar" id="ciclo_escolar" class="form-control" style="width: 200px" required>
                    <option value="">Seleccione un año</option>
                    @foreach($anios as $anio)
                        <option value="{{ $anio }}">{{ $anio }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        @if(session('error'))
            <div class="alert alert-danger mt-3">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>


<script>
    document.getElementById('criterio').addEventListener('change', function() {
        let label = document.getElementById('label-valor');
        if (this.value === 'familia') {
            label.textContent = 'Código Familiar o Nombre Familiar';
        } else {
            label.textContent = 'Carné o Nombre Completo';
        }
    });
</script>
@endsection
