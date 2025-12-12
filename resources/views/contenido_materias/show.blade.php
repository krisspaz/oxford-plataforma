@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Detalles del Contenido de la Materia</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Materia</th>
                    <td>{{ $contenidoMateria->materiaCursos->materia->gestionMateria->nombre }}</td>
                </tr>
                <tr>
                    <th>Bimestre</th>
                    <td>{{ $contenidoMateria->bimestre->nombre }}</td>
                </tr>
                <tr>
                    <th>Tipo de Contenido</th>
                    <td>{{ ucfirst($contenidoMateria->tipo_contenido) }}</td>
                </tr>
                <tr>
                    <th>Archivo</th>
                    <td>
                        @if ($contenidoMateria->tipo_contenido === 'video')
                            <a href="{{  Storage::url($contenidoMateria->archivo)}}" target="_blank">Ver Video</a>
                        @elseif ($contenidoMateria->tipo_contenido === 'audio')
                            <audio controls>
                                <source src="{{ Storage::url($contenidoMateria->archivo) }}" type="audio/mpeg">
                                Tu navegador no soporta el elemento de audio.
                            </audio>
                        @elseif ($contenidoMateria->tipo_contenido === 'imagen')
                            <img src="{{  Storage::url($contenidoMateria->archivo) }}" alt="Imagen" class="img-fluid" style="max-width: 300px;">
                        @elseif ($contenidoMateria->tipo_contenido === 'documento')
                            <a href="{{ Storage::url($contenidoMateria->archivo) }}" target="_blank">Descargar Documento</a>
                
                        @elseif ($contenidoMateria->tipo_contenido === 'link')
                            <a href="{{ url($contenidoMateria->archivo) }}" target="_blank">Ver Link</a>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Docente</th>
                    <td>{{ $contenidoMateria->docente->persona->nombres }} {{ $contenidoMateria->docente->persona->apellidos }}</td>
                </tr>
                <tr>
                    <th>Creado el</th>
                    <td>{{ $contenidoMateria->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Actualizado el</th>
                    <td>{{ $contenidoMateria->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4">
            <a href="{{ route('contenido_materias.index') }}" class="btn btn-default">Regresar</a>
        </div>
    </div>
</div>
@endsection
