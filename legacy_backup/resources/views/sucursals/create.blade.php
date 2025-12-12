@extends('crudbooster::admin_template')



@section('content')
<div class="container">
    <h1>Crear Sucursal</h1>

    <form action="{{ route('sucursals.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nombre_sucursal">Nombre de la Sucursal</label>
            <input type="text" name="nombre_sucursal" id="nombre_sucursal" class="form-control" value="{{ old('nombre_sucursal') }}" required>
        </div>

        <div class="form-group">
            <label for="departamento">Departamento</label>
            <select name="departamento" id="departamento" class="form-control" required>
                <option value="">Seleccione un departamento</option>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" {{ old('departamento') == $departamento->id ? 'selected' : '' }}>
                        {{ $departamento->departamento }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="municipio_id">Municipio</label>
            <select name="municipio_id" id="municipio_id" class="form-control" required>
                <!-- Municipios cargados por AJAX -->
            </select>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion') }}" required>
        </div>

        <div class="form-group">
            <label for="estado_id">Estado</label>
            <select name="estado_id" id="estado_id" class="form-control" required>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                        {{ $estado->estado }}
                    </option>
                @endforeach
            </select>
        </div>

       

        <button type="submit" class="btn btn-success">Crear Sucursal</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Cargar municipios cuando se selecciona un departamento
        $('#departamento').on('change', function() {
            var departamentoId = $(this).val();
            $.ajax({
                url: "{{ route('sucursals.getMunicipios', '') }}/" + departamentoId,
                method: 'GET',
                success: function(data) {
                    var municipioSelect = $('#municipio_id');
                    municipioSelect.empty();
                    municipioSelect.append('<option value="">Seleccione un municipio</option>');
                    $.each(data, function(id, nombre) {
                        municipioSelect.append($('<option>', { value: id, text: nombre }));
                    });
                }
            });
        });

        // Agregar un nuevo campo de teléfono
        $('#add-phone').on('click', function() {
            $('#telefonos-container').append(`
                <div class="form-group">
                    <input type="text" name="telefonos[]" class="form-control" required>
                    <button type="button" class="btn btn-danger btn-sm remove-phone">Eliminar</button>
                </div>
            `);
            $('#telefonos-container').find('.remove-phone').show(); // Mostrar botón de eliminar
        });

        // Eliminar un campo de teléfono
        $('#telefonos-container').on('click', '.remove-phone', function() {
            $(this).parent().remove();
        });
    });
</script>
@endsection
