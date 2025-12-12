@extends('crudbooster::admin_template')

@section('title', 'Alumnos y Familias')


@section('content')
<div class="container">
    <h1>Detalles de la Familia</h1>
   
      
            
            <table class="table">

                <tr>
                    <th>Código Familiar</th>
                    <td>{{ $alumnosFamilia->padresTutores->codigofamiliar }}</td>
                </tr>
                <tr>
                    <th>Alumno</th>
                    <td>{{ $alumnosFamilia->alumno->nombre }}{{" "}}{{ $alumnosFamilia->alumno->apellidos }}</td>
                </tr>
               
                
                
               
               
               
               
            </table>





            <a href="{{ route('alumnos_familias.edit', $alumnosFamilia->id) }}" class="btn btn-warning">Editar</a>
            <form action="{{ route('alumnos_familias.destroy', $alumnosFamilia->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
            <a href="{{ route('alumnos_familias.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>
@endsection
