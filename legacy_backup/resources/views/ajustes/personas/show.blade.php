@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle de Persona</h3>
    </div>

    <div class="box-body">
        <table class="table table-striped">
            <tr><th>Nombres</th><td>{{ $persona->nombres }}</td></tr>
            <tr><th>Apellidos</th><td>{{ $persona->apellidos }}</td></tr>
            <tr><th>Género</th><td>{{ $persona->genero == 'Masculino' ? 'Masculino' : 'Femenino' }}</td></tr>
           @if(!empty($persona->estado_civil))
             <tr>
                    <th>Estado Civil</th>
                    <td>{{ $persona->estado_civil }}</td>
                </tr>
            @endif


            @if(!empty($persona->profesion))
             <tr>
                    <th>Profesión</th>
                    <td>{{ $persona->profesion  }}</td>
                </tr>
            @endif
            <tr><th>Tipo de Documento</th><td>{{ $persona->identificacionDocumento->nombre ?? 'N/A' }}</td></tr>
            <tr><th>No. Documento</th><td>{{ $persona->num_documento }}</td></tr>
            <tr><th>Fecha de Nacimiento</th><td>{{ $persona->fecha_nacimiento }}</td></tr>
            <tr><th>Correo Electrónico</th><td>{{ $persona->email }}</td></tr>
            <tr><th>Teléfono</th><td>{{ $persona->telefono }}</td></tr>
            <tr><th>Dirección</th><td>{{ $persona->direccion }}</td></tr>
            <tr><th>Fecha de Defunción</th><td>{{ $persona->fecha_defuncion ?? 'Vivo(a)' }}</td></tr>


        </table>

        <a href="{{ route('ajuste-persona.index') }}" class="btn btn-default">Volver</a>
    </div>
</div>
@endsection
