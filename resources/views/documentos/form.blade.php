@php $doc = $documento ?? null; @endphp

<div class="form-group">
    <label>Estudiante</label>
    <select name="estudiante_id" id="estudiante_id"  class="form-control select2" required>
        <option value="">Seleccione</option>
        @foreach($estudiantes as $e)
            <option value="{{ $e->id }}" {{ old('estudiante_id', $doc->estudiante_id ?? '') == $e->id ? 'selected' : '' }}>
               {{"Carné: "}} {{ $e->carnet }} {{ "Nombre Completo: "}}{{ $e->persona->nombres }} {{ $e->persona->apellidos }}
            </option>
        @endforeach
    </select>
</div>


<div class="form-group">
    <label>Ciclo Escolar</label>
    <select name="ciclo_escolar" id="ciclo_escolar"  class="form-control select2" required>
        <option value="">Seleccione un estudiante primero</option>
    </select>
</div>


<div class="form-group">
    <label>Inscripción</label>
    <select name="inscripcion_id" id="inscripcion_id" class="form-control select2" required>
        <option value="">Seleccione un ciclo escolar primero</option>
    </select>
</div>


<div class="form-group">
    <label>Tipo de Documento</label>
    <select name="tipo_documento" class="form-control" required>
        <option value="">Seleccione un tipo</option>
        <option value="Certificado" {{ old('tipo_documento', $doc->tipo_documento ?? '') == 'Certificado' ? 'selected' : '' }}>Certificado</option>
        <option value="Acta" {{ old('tipo_documento', $doc->tipo_documento ?? '') == 'Acta' ? 'selected' : '' }}>Acta</option>
        <option value="Constancia" {{ old('tipo_documento', $doc->tipo_documento ?? '') == 'Constancia' ? 'selected' : '' }}>Constancia</option>
    </select>
</div>


<div class="form-group">
    <label>Nombre del Documento</label>
    <input type="text" name="nombre_documento" class="form-control" value="{{ old('nombre_documento', $doc->nombre_documento ?? '') }}" required>
</div>

<div class="form-group">
    <label>Archivo</label>
    <input type="file" name="documento" class="form-control" {{ isset($doc) ? '' : 'required' }}>
    @if(isset($doc) && $doc->documento)
        <small>Archivo actual: <a href="{{ asset('storage/' . $doc->documento) }}" target="_blank">Ver</a></small>
    @endif
</div>

<div class="form-group">
    <label>Fecha de Expiración</label>
    <input type="date" name="fexpiracion" class="form-control" value="{{ old('fexpiracion', $doc->fexpiracion ?? '') }}">
</div>

<div class="form-group">
    <label>Estado</label>
    <select name="estado_id" class="form-control" required>
        <option value="">Seleccione</option>
        @foreach($estados as $estado)
            <option value="{{ $estado->id }}" {{ old('estado_id', $doc->estado_id ?? '') == $estado->id ? 'selected' : '' }}>
                {{ $estado->estado }}
            </option>
        @endforeach
    </select>
</div>

@push('bottom')
<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

<script>
   $(document).ready(function () {
    $('.select2').select2();

    $('#estudiante_id').change(function () {
        let estudianteId = $(this).val();

        $('#ciclo_escolar').html('<option value="">Cargando...</option>');
        $('#inscripcion_id').html('<option value="">Seleccione un ciclo escolar primero</option>');

        if (estudianteId) {
            $.get(`/obtener-ciclos/${estudianteId}`, function (data) {
                console.log("Ciclos escolares:", data); // 👈 para depurar

                $('#ciclo_escolar').empty().append('<option value="">Seleccione</option>');

                data.forEach(ciclo => {
                    $('#ciclo_escolar').append(`<option value="${ciclo}">${ciclo}</option>`);
                });
            }).fail(function(xhr) {
                console.error("Error al obtener ciclos:", xhr.responseText);
            });
        }
    });

    $('#ciclo_escolar').change(function () {
        let estudianteId = $('#estudiante_id').val();
        let cicloEscolar = $(this).val();

        $('#inscripcion_id').html('<option value="">Cargando...</option>');

        if (estudianteId && cicloEscolar) {
            $.get(`/obtener-inscripciones/${estudianteId}/${cicloEscolar}`, function (data) {
                console.log("Inscripciones:", data); // 👈 para depurar

                $('#inscripcion_id').empty().append('<option value="">Seleccione</option>');

                data.forEach(insc => {
                    $('#inscripcion_id').append(`<option value="${insc.id}">${insc.id}</option>`);
                });
            }).fail(function(xhr) {
                console.error("Error al obtener inscripciones:", xhr.responseText);
            });
        }
    });
});

    </script>
    
@endpush




