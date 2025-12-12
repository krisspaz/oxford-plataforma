@extends('crudbooster::admin_template')



@section('content')
<div class="container">
    <h1>Editar Sucursal</h1>

    <form action="{{ route('sucursals.update', $sucursal->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre_sucursal">Nombre de la Sucursal</label>
            <input type="text" name="nombre_sucursal" id="nombre_sucursal" class="form-control" value="{{ old('nombre_sucursal', $sucursal->nombre_sucursal) }}" required>
        </div>

        <div class="form-group">
            <label for="departamento">Departamento</label>
            <select name="departamento" id="departamento" class="form-control" required>
                <option value="">Seleccione un departamento</option>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" {{ $departamento->id == old('departamento', $sucursal->municipio->departamento_id) ? 'selected' : '' }}>
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
            <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion', $sucursal->direccion) }}" required>
        </div>

        <div class="form-group">
            <label for="estado_id">Estado</label>
            <select name="estado_id" id="estado_id" class="form-control" required>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ $estado->id == old('estado_id', $sucursal->estado_id) ? 'selected' : '' }}>
                        {{ $estado->estado }}
                    </option>
                @endforeach
            </select>
        </div>

       

        <button type="submit" class="btn btn-success">Actualizar Sucursal</button>
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
        });

        // Eliminar un campo de teléfono
        $('#telefonos-container').on('click', '.remove-phone', function() {
            $(this).parent().remove();
        });
    });
</script>
@endsection

