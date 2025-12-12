@extends('crudbooster::admin_template')

@section('title', 'Create Sucursal')

@section('content')
    <h1>Create New Sucursal</h1>
    <form action="{{ route('sucursales.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre_sucursal">Sucursal Name:</label>
            <input type="text" id="nombre_sucursal" name="nombre_sucursal" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="departamento_id">Departamento:</label>
            <select id="departamento_id" name="departamento_id" class="form-control" required>
                <option value="">Select Departamento</option>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="municipio_id">Municipio:</label>
            <select id="municipio_id" name="municipio_id" class="form-control" required>
                <option value="">Select Municipio</option>
            </select>
        </div>
        <div class="form-group">
            <label for="direccion">Direccion:</label>
            <input type="text" id="direccion" name="direccion" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="status_id">Status:</label>
            <select id="status_id" name="status_id" class="form-control" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    <a href="{{ route('sucursales.index') }}" class="btn btn-secondary">Back to List</a>

    <!-- JavaScript para manejar el cambio de departamento y carga dinámica de municipios -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
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
