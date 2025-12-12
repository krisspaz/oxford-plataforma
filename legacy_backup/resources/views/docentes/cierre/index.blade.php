@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <div class="box-header">


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
        <h3 class="box-title">Cierre de Bimestres</h3>
    </div>

    <div class="box-body">
        <form method="POST" action="{{ route('cuadro_notas.cierre') }}">
            @csrf

            <div class="form-group">
                <label>Bimestre</label>
                <select name="bimestre_id" class="form-control" required>
                    <option value="">Seleccione un bimestre</option>
                    @foreach ($bimestres as $bimestre)
                        <option value="{{ $bimestre->id }}"> {{ $bimestre->nombre}} - {{ $bimestre->gestion->ciclo_escolar}}</option>
                    @endforeach
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

            <div class="form-group">
                <label>Cierre</label>
                <select name="solicitud" class="form-control" required>
                    <option value="Si">Si</option>
                    <option value="No">No</option>
                </select>
            </div>

            <button type="submit" class="btn btn-danger">
                <i class="fa fa-lock"></i> Cerrar Bimestre
            </button>
        </form>
    </div>
</div>
@endsection
