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
        <h3 class="box-title">Crear Estudiante</h3>
    </div>
    <div class="box-body">
        <form action="{{ route('ajustes_estudiantes.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            {{-- Datos del Estudiante --}}
            <fieldset>
                <legend>Información del Estudiante</legend>

                <div class="form-group">
                    <label for="fotografia_estudiante">Fotografía:</label>
                    <input 
                        type="file" 
                        class="form-control-file" 
                        id="fotografia_estudiante" 
                        name="fotografia_estudiante" 
                        accept="image/*" 
                        value="{{ old('fotografia_estudiante') }}"
                        onchange="previewImage(event)">
                    
                    <img 
                        id="imagePreview" 
                        src="{{ isset($persona) && $persona->fotografia ? asset('uploads/fotografias/' . $persona->fotografia) : '#' }}" 
                        alt="Vista Previa" 
                        style="{{ isset($persona) && $persona->fotografia ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
                </div>

       

                <div class="form-group">
                    <label>Persona</label>
                    <select name="persona_id" class="form-control select2" required>
                        <option value="">-- Seleccione --</option>
                        @foreach($personas as $persona)
                            <option value="{{ $persona->id }}">{{ $persona->nombres }} {{ $persona->apellidos }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Carnet</label>
                    <input type="text" name="carnet" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Grado/Sección/Jornada</label>
                    <select name="cgshges_id" class="form-control" required>
                        <option value="">-- Seleccione --</option>
                        @foreach($cgshges as $id => $asignacion)
                            <option value="{{ $asignacion->id }}">{{ $asignacion->grados->nombre }} {{ $asignacion->cursos->curso }} {{"'"}}{{ $asignacion->secciones->seccion}} {{"'"}} {{ $asignacion->jornadas->jornada->nombre}}

                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado_id" class="form-control" required>
                        @foreach($estados as $id => $estado)
                            <option value="{{ $id }}">{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
            </fieldset>


        
            {{-- Botones --}}
            <div class="box-footer">
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="{{ route('ajustes_estudiantes.index') }}" class="btn btn-default">Cancelar</a>
            </div>

        </form>
    </div>
</div>


@push('bottom')
<script>
    // Agregar más Historiales Académicos
    let acadIndex = 1;
    $('#add-academico').click(function() {
        let html = $('.academico-item:first').clone();
        html.find('textarea, input').each(function() {
            let name = $(this).attr('name').replace(/\d+/, acadIndex);
            $(this).attr('name', name).val('');
        });
        $('#academicos-wrapper').append(html);
        acadIndex++;
    });

    // Agregar más Historiales Médicos
    let medIndex = 1;
    $('#add-medico').click(function() {
        let html = $('.medico-item:first').clone();
        html.find('input, textarea').each(function() {
            let name = $(this).attr('name').replace(/\d+/, medIndex);
            $(this).attr('name', name).val('');
        });
        $('#medicos-wrapper').append(html);
        medIndex++;
    });
</script>
@endpush

@push('bottom')
<!-- Select2 CSS & JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        theme: 'bootstrap4', // Opcional, puedes quitar si no usas Bootstrap 4
        placeholder: 'Seleccione',
        allowClear: true,
        theme: 'bootstrap4'
    });
});
</script>
@endpush

@endsection