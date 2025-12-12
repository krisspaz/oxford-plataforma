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

@php $doc = $documento ?? null; @endphp

<input type="hidden" name="estudiante_id" value="{{ $doc->id }}">
<div class="form-group">
    <label>Estudiante</label>
    <input type="text"  class="form-control" value="{{ optional($estudiantes->where('id', old('estudiante_id', $doc->estudiante_id ?? ''))->first())->carnet }} - {{ optional($estudiantes->where('id', old('estudiante_id', $doc->estudiante_id ?? ''))->first())->persona->nombres }} {{ optional($estudiantes->where('id', old('estudiante_id', $doc->estudiante_id ?? ''))->first())->persona->apellidos }}" readonly>
</div>


<div class="form-group">
    <label>Tipo de Documento</label>
    <select name="tipo_documento" class="form-control">
        <option value="">Seleccione un tipo</option>
        <option value="Certificado" {{ old('tipo_documento', $doc->tipo_documento ?? '') == 'Certificado' ? 'selected' : '' }}>Certificado</option>
        <option value="Acta" {{ old('tipo_documento', $doc->tipo_documento ?? '') == 'Acta' ? 'selected' : '' }}>Acta</option>
        <option value="Constancia" {{ old('tipo_documento', $doc->tipo_documento ?? '') == 'Constancia' ? 'selected' : '' }}>Constancia</option>
    </select>
</div>


<div class="form-group">
    <label>Nombre del Documento</label>
    <input type="text" name="nombre_documento" class="form-control" value="{{ old('nombre_documento', $doc->nombre_documento ?? '') }}" >
</div>

<div class="form-group">
    <label>Archivo</label>
    <input type="file" name="documento" class="form-control" {{ isset($doc) }}>
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
    <select name="estado_id" class="form-control" >
        <option value="">Seleccione</option>
        @foreach($estados as $estado)
            <option value="{{ $estado->id }}" {{ old('estado_id', $doc->estado_id ?? '') == $estado->id ? 'selected' : '' }}>
                {{ $estado->estado }}
            </option>
        @endforeach
    </select>
</div>




