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

<div class="form-group">
    <label for="codigo">Código</label>
    <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $alumno->codigo ?? '') }}">
</div>

<div class="form-group">
    <label for="carne">Carné</label>
    <input type="text" name="carne" class="form-control" value="{{ old('carne', $alumno->carne ?? '') }}">
</div>

<div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $alumno->nombre ?? '') }}">
</div>

<div class="form-group">
    <label for="apellidos">Apellidos</label>
    <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $alumno->apellidos ?? '') }}">
</div>

<div class="form-group">
    <label for="genero">Género</label>
    <select name="genero" class="form-control">
        <option value="M" {{ old('genero', $alumno->genero ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
        <option value="F" {{ old('genero', $alumno->genero ?? '') == 'F' ? 'selected' : '' }}>Femenino</option>
    </select>
</div>

<div class="form-group">
    <label for="cui">CUI</label>
    <input type="text" name="cui" class="form-control" value="{{ old('cui', $alumno->cui ?? '') }}">
</div>

<div class="form-group">
    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
    <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento ?? '') }}">
</div>

<div class="form-group">
    <label for="departamento_id">Departamento</label>
    <select name="departamento_id" id="departamento_id" class="form-control">
        <option value="">Seleccione un departamento</option>
        @foreach($departamentos as $departamento)
            <option value="{{ $departamento->id }}" {{ old('departamento_id', $alumno->municipio->departamento_id ?? '') == $departamento->id ? 'selected' : '' }}>{{ $departamento->departamento }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="municipio_id">Municipio</label>
    <select name="municipio_id" id="municipio_id" class="form-control">
        <option value="">Seleccione un municipio</option>
        @if(isset($alumno))
            @foreach($alumno->municipio->departamento->municipios as $municipio)
                <option value="{{ $municipio->id }}" {{ old('municipio_id', $alumno->municipio_id ?? '') == $municipio->id ? 'selected' : '' }}>{{ $municipio->municipio }}</option>
            @endforeach
        @endif
    </select>
</div>

<div class="form-group">
    <label for="direccion">Dirección</label>
    <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $alumno->direccion ?? '') }}">
</div>

<div class="form-group">
    <label for="telefono">Teléfono</label>
    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $alumno->telefono ?? '') }}">
</div>

<div class="form-group">
    <label for="estado_id">Estado</label>
    <select name="estado_id" class="form-control">
        @foreach($estados as $estado)
            <option value="{{ $estado->id }}" {{ old('estado_id', $alumno->estado_id ?? '') == $estado->id ? 'selected' : '' }}>{{ $estado->estado }}</option>
        @endforeach
    </select>
</div>

<script>
document.getElementById('departamento_id').addEventListener('change', function() {
    var departamentoId = this.value;
    fetch('/municipios/' + departamentoId)
        .then(response => response.json())
        .then(data => {
            var municipioSelect = document.getElementById('municipio_id');
            municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
            data.forEach(function(municipio) {
                var option = document.createElement('option');
                option.value = municipio.id;
                option.text = municipio.municipio;
                municipioSelect.add(option);
            });
        });
});
</script>
