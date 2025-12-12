@extends('crudbooster::admin_template')



@section('content')
    <h1>Crear Madre</h1>
    <form action="{{ route('madres.store') }}" method="POST">
        @csrf
        <div>
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required>
        </div>
        <div>
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" value="{{ old('apellido') }}" required>
        </div>
        <div>
            <label for="identificacion_documentos_id">Documento de Identificación</label>
            <select name="identificacion_documentos_id" id="identificacion_documentos_id" required>
                @foreach($identificacionDocumentos as $documento)
                    <option value="{{ $documento->id }}">{{ $documento->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="num_documento">Número de Documento</label>
            <input type="text" name="num_documento" id="num_documento" value="{{ old('num_documento') }}" required>
        </div>
        <div>
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required>
        </div>
        <div>
            <label for="profesion">Profesión</label>
            <input type="text" name="profesion" id="profesion" value="{{ old('profesion') }}" required>
        </div>
        <div>
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" required>
        </div>
        <div>
            <label for="departamento_id">Departamento</label>
            <select name="departamento_id" id="departamento_id" required>
                <option value="">Seleccione un Departamento</option>
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="municipio_id">Municipio</label>
            <select name="municipio_id" id="municipio_id" required>
                <option value="">Seleccione un Municipio</option>
            </select>
        </div>
        <div>
            <label for="direccion">Dirección</label>
            <textarea name="direccion" id="direccion" required>{{ old('direccion') }}</textarea>
        </div>
        <button type="submit">Guardar</button>
    </form>

    @parent
    <script>
        $(document).ready(function() {
            $('#departamento_id').change(function() {
                var departamento_id = $(this).val();
                if(departamento_id) {
                    $.ajax({
                        url: '/madres/get-municipios/'+departamento_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#municipio_id').empty();
                            $('#municipio_id').append('<option value="">Seleccione un Municipio</option>');
                            $.each(data, function(key, value) {
                                $('#municipio_id').append('<option value="'+ value.id +'">'+ value.municipio +'</option>');
                            });
                        }
                    });
                } else {
                    $('#municipio_id').empty();
                }
            });
        });
    </script>
@endsection
