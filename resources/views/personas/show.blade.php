

@extends('crudbooster::admin_template')


@section('content')

<div class="container">
    <h1>Detalles de Persona</h1>

    <div class="card">
        <div class="card-header">
            <h2> Detalles de {{ $persona->nombres }} {{ $persona->apellidos }} </h2>
        </div>
        <div class="card-body">
            <div class="row">
                
                <div class="col-md-4">
            <img src="{{ asset('storage/fotografias/' . $persona->fotografia) }}" alt="300" width="350">
            <ul class="list-group">
           
                <li class="list-group-item"><strong>Registrado por:</strong> {{ $persona->cmsUser->name }} </li>
                <li class="list-group-item"><strong>Fecha de Registro:</strong> {{ Carbon\Carbon::parse($persona->created_at)->format('d/m/Y') }}</li>
                <li class="list-group-item"><strong>Fecha de Modificación:</strong> {{ Carbon\Carbon::parse($persona->updated_at)->format('d/m/Y') }}</li>
            </ul>     
        </div>
                <div class="col-md-8">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Codigo Personal:</strong> {{ $persona->codigo }}</li>
                        <li class="list-group-item"><strong>Nombres:</strong> {{ $persona->nombres }}</li>
                        <li class="list-group-item"><strong>Apellidos:</strong> {{ $persona->apellidos }}</li>
                        <li class="list-group-item"><strong>Género:</strong> {{ $persona->genero }}</li>
                        <li class="list-group-item"><strong>Estado Civil:</strong> {{ $persona->estado_civil }}</li>
                        <li class="list-group-item"><strong>Apellido de Casada:</strong> {{ $persona->apellido_casada }}</li>
                        <li class="list-group-item"><strong>Nacionalidad:</strong> {{ $persona->nacionalidad }}</li>
                        <li class="list-group-item"><strong>Identificación:</strong> {{ $persona->identificacionDocumento->nombre }} - {{ $persona->num_documento }}</li>
                        <li class="list-group-item"><strong>Lugar de Nacimiento:</strong> {{ $persona->lugar_nacimiento }}</li>
                        <li class="list-group-item"><strong>Fecha de Nacimiento:</strong> {{ Carbon\Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y') }}</li>
                        <li class="list-group-item"><strong>NIT:</strong> {{ $persona->nit }}</li>
                        <li class="list-group-item"><strong>Email:</strong> {{ $persona->email }}</li>
                        <li class="list-group-item"><strong>Municipio:</strong> {{ $persona->municipio->municipio }}</li>
                        <li class="list-group-item"><strong>Dirección:</strong> {{ $persona->direccion }}</li>
                        <li class="list-group-item"><strong>Teléfono de Casa:</strong> {{ $persona->telefono_casa }}</li>
                        <li class="list-group-item"><strong>Teléfono Móvil:</strong> {{ $persona->telefono_mobil }}</li>
                        <li class="list-group-item"><strong>Fecha de Defunción:</strong> {{ $persona->fecha_defuncion ? Carbon\Carbon::parse($persona->fecha_defuncion)->format('d/m/Y') : 'No disponible' }}</li>
                        <li class="list-group-item"><strong>País de Origen:</strong> {{ $persona->paisOrigen->nombre }}</li>
                        <li class="list-group-item"><strong>Tipo de Sangre:</strong> {{ $persona->tipo_sangre }}</li>
                        <li class="list-group-item"><strong>Registrado por:</strong> {{ $persona->cmsUser->name }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('personas.edit', $persona->id) }}" class="btn btn-primary">Editar</a>
            <form action="{{ route('personas.destroy', $persona->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
            <a href="{{ route('personas.index') }}" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </div>
</div>
@endsection
