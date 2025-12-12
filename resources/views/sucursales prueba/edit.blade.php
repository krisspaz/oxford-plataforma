@extends('crudbooster::admin_template')



@section('title', 'Edit Sucursal')

@section('content')
    <h1>Edit Sucursal</h1>
    <form action="{{ route('sucursales.update', $sucursale->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nombre_sucursal">Sucursal Name:</label>
            <input type="text" id="nombre_sucursal" name="nombre_sucursal" class="form-control" value="{{ old('nombre_sucursal', $sucursale->nombre_sucursal) }}" required>
        </div>
        <div class="form-group">
            <label for="departamento_id">Departamento:</label>
            <select id="departamento_id" name="departamento_id" class="form-control" required>
                <option value="">Select Departamento</option>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" {{ $sucursale->municipio->departamento_id == $departamento->id ? 'selected' : '' }}>
                        {{ $departamento->departamento }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="municipio_id">Municipio:</label>
            <select id="municipio_id" name="municipio_id" class="form-control" required>
                <!-- Los municipios se cargarán aquí mediante JavaScript -->
            </select>
        </div>
        <div class="form-group">
            <label for="direccion">Direccion:</label>
            <input type="text" id="direccion" name="direccion" class="form-control" value="{{ old('direccion', $sucursale->direccion) }}" required>
        </div>
        <div class="form-group">
            <label for="status_id">Status:</label>
            <select id="status_id" name="status_id" class="form-control" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" {{ $sucursale->status_id == $status->id ? 'selected' : '' }}>
                        {{ $status->status_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
    <a href="{{ route('sucursales.index') }}" class="btn btn-secondary">Back to List</a>

    <!-- JavaScript para manejar el cambio de departamento y carga dinámica de municipios -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Pre-cargar los municipios al cargar la página
            var selectedDepartamentoId = $('#departamento_id').val();
            if (selectedDepartamentoId) {
                $.ajax({
                    url: '{{ route('sucursales.getMunicipiosByDepartamento') }}',
                    type: 'GET',
                    data: { departamento_id: selectedDepartamentoId },
                    success: function(data) {
                        $('#municipio_id').empty();
                        $('#municipio_id').append('<option value="">Select Municipio</option>');
                        $.each(data, function(index, municipio) {
                            $('#municipio_id').append('<option value="' + municipio.id + '" ' + (municipio.id == {{ $sucursale->municipio_id }} ? 'selected' : '') + '>' + municipio.municipio + '</option>');
                        });
                    }
                });
            }

            // Manejo del cambio de departamento
            $('#departamento_id').change(function() {
                var departamentoId = $(this).val();
                if (departamentoId) {
                    $.ajax({
                        url: '{{ route('sucursales.getMunicipiosByDepartamento') }}',
                        type: 'GET',
                        data: { departamento_id: departamentoId },
                        success: function(data) {
                            $('#municipio_id').empty();
                            $('#municipio_id').append('<option value="">Select Municipio</option>');
                            $.each(data, function(index, municipio) {
                                $('#municipio_id').append('<option value="' + municipio.id + '">' + municipio.municipio + '</option>');
                            });
                        }
                    });
                } else {
                    $('#municipio_id').empty();
                    $('#municipio_id').append('<option value="">Select Municipio</option>');
                }
            });
        });
    </script>
@endsection

