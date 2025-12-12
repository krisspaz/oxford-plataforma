<!-- resources/views/sucursal_telefonos/create.blade.php -->

@extends('crudbooster::admin_template')

@section('content')
    <div class="container">
        <h1>Crear Nueva Relación Sucursal-Telefono</h1>
        <form action="{{ route('sucursal_telefonos.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="sucursal_id">Sucursal</label>
                <select class="form-control" id="sucursal_id" name="sucursal_id" required>
                    @foreach ($sucursals as $sucursal)
                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre_sucursal }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="telefono_id">Telefono</label>
                <select class="form-control" id="telefono_id" name="telefono_id" required>
                    @foreach ($telefonos as $telefono)
                        <option value="{{ $telefono->id }}">{{ $telefono->telefono }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
@endsection
