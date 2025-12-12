<div class="form-group">
    <label>Nombre Familiar</label>
    <input type="text" name="nombre_familiar" class="form-control"
        value="{{ old('nombre_familiar', $ajuste_familiar->nombre_familiar ?? '') }}" required>
</div>

<div class="form-group">
    <label>Código Familiar</label>
    <input type="text" name="codigo_familiar" class="form-control"
        value="{{ old('codigo_familiar', $ajuste_familiar->codigo_familiar ?? $codigoFamiliarTemporal) }}" required>
</div>

<div class="form-group">
    <label>Padre</label>
    <select name="padre_persona_id" id="padre_persona_id" class="form-control select2">
        <option value="">Seleccione</option>
        @foreach($personas as $p)
            <option value="{{ $p->id }}" {{ old('padre_persona_id', $ajuste_familiar->padre_persona_id ?? '') == $p->id ? 'selected' : '' }}>
                {{ $p->nombres }} {{ $p->apellidos }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Madre</label>
    <select name="madre_persona_id" id="madre_persona_id" class="form-control select2">
        <option value="">-- Seleccione --</option>
        @foreach($personas as $persona)
            <option value="{{ $persona->id }}" {{ old('madre_persona_id', $ajuste_familiar->madre_persona_id ?? '') == $persona->id ? 'selected' : '' }}>
                {{ $persona->nombres }} {{ $persona->apellidos }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Encargado</label>
    <select name="encargado_persona_id" id="encargado_persona_id" class="form-control select2">
        <option value="">-- Seleccione --</option>
        @foreach($personas as $persona)
            <option value="{{ $persona->id }}" {{ old('encargado_persona_id', $ajuste_familiar->encargado_persona_id ?? '') == $persona->id ? 'selected' : '' }}>
                {{ $persona->nombres }} {{ $persona->apellidos }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Estado</label>
    <select name="estado_id" class="form-control" required>
        <option value="">-- Seleccione --</option>
        @foreach($estados as $estado)
            <option value="{{ $estado->id }}" {{ old('estado_id', $ajuste_familiar->estado_id ?? '') == $estado->id ? 'selected' : '' }}>
                {{ $estado->estado }}
            </option>
        @endforeach
    </select>
</div>

@if(isset($ajuste_familiar) && $ajuste_familiar->id)
    <hr>
    <h4>Estudiantes de la familia</h4>
    <table class="table table-bordered" id="tabla-estudiantes">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estudiantesFamilia as $item)
                <tr data-id="{{ $item->estudiante->id }}">
                    <td>{{ $item->estudiante->persona->nombres ?? '' }} {{ $item->estudiante->persona->apellidos ?? '' }}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm btn-eliminar">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button type="button" id="btn-agregar-estudiante" class="btn btn-success">Agregar Estudiante</button>

    <!-- Input oculto para enviar los IDs -->
    <input type="hidden" name="estudiantes_ids" id="estudiantes_ids">
@endif

@push('bottom')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({ theme: 'bootstrap4', placeholder: 'Seleccione', allowClear: true });

    @if(isset($ajuste_familiar) && $ajuste_familiar->id)
    // Mostrar formulario para agregar estudiante
    $('#btn-agregar-estudiante').click(function() {
        let options = '';
        @foreach($estudiantes as $est)
            options += `<option value="{{ $est->id }}">{{ $est->carnet }} - {{ $est->persona->nombres }} {{ $est->persona->apellidos }}</option>`;
        @endforeach

        let newRow = `<tr data-id="">
            <td>
                <select class="form-control select2 select-estudiante">${options}</select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btn-eliminar">Eliminar</button>
            </td>
        </tr>`;
        $('#tabla-estudiantes tbody').append(newRow);
        $('#tabla-estudiantes tbody .select2').last().select2({ theme: 'bootstrap4', placeholder: 'Seleccione', allowClear: true });
    });

    // Eliminar estudiante
    $(document).on('click', '.btn-eliminar', function() {
        $(this).closest('tr').remove();
    });

    // Preparar IDs antes de enviar el formulario
    $('form').submit(function() {
        let ids = [];
        $('#tabla-estudiantes tbody tr').each(function() {
            let select = $(this).find('select.select-estudiante');
            if(select.length) {
                ids.push(select.val());
            } else {
                ids.push($(this).data('id'));
            }
        });
        $('#estudiantes_ids').val(ids.join(','));
    });
    @endif
});
</script>
@endpush
