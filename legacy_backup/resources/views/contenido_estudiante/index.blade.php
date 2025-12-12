@extends('crudbooster::admin_template')

@section('content')

@if (session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ session('error') }}
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif
<div class="panel panel-default">
    <div class="panel-heading">
        <h3>Contenido para {{ $materia->gestionMateria->nombre }}</h3>
    </div>
    <div class="panel-body">
        @if(count($bimestres) > 0)
            <div class="panel-group" id="accordion">
                @foreach($bimestres as $index => $bimestre)
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $index }}">
                                    {{ ucfirst($bimestre->nombre) }}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse{{ $index }}" class="panel-collapse collapse {{ $index == 0 ? 'in' : '' }}">
                            <div class="panel-body">
                                @php
                                    $contenidosBimestre = $contenidos->where('bimestre_id', $bimestre->id);
                                @endphp

                                @if(count($contenidosBimestre) > 0)
                                    @foreach($contenidosBimestre as $contenido)
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4>Título: {{ ucfirst($contenido->titulo) }}</h4>
                                                <h4>Fecha de Creación: {{ ucfirst($contenido->created_at) }}</h4>
                                            </div>
                                            <div class="panel-body">
                                                <p><strong>Descripción:</strong></p>
                                                <textarea class="form-control" rows="3" readonly>{{ ucfirst($contenido->descripcion) }}</textarea>

                                                @php
                                                    $extension = pathinfo(Storage::path($contenido->archivo), PATHINFO_EXTENSION);
                                                @endphp

                                                @if($contenido->tipo_contenido === 'link')
                                                    <p><a href="{{ url($contenido->archivo) }}" target="_blank">Abrir enlace</a></p>
                                                @elseif($contenido->tipo_contenido === 'video')
                                                    <p><a href="{{ Storage::url($contenido->archivo) }}" download>Descargar</a></p>
                                                    <video width="75%" controls>
                                                        <source src="{{ Storage::url($contenido->archivo) }}" type="video/mp4">
                                                        Tu navegador no soporta el formato de video.
                                                    </video>
                                                @elseif($contenido->tipo_contenido === 'audio')
                                                    <p><a href="{{ Storage::url($contenido->archivo) }}" download>Descargar</a></p>
                                                    <audio controls style="width: 100%;">
                                                        <source src="{{ Storage::url($contenido->archivo) }}" type="audio/mpeg">
                                                        Tu navegador no soporta el formato de audio.
                                                    </audio>
                                                @elseif($contenido->tipo_contenido === 'imagen')
                                                    <p><a href="{{ Storage::url($contenido->archivo) }}" download>Descargar</a></p>
                                                    <img src="{{ Storage::url($contenido->archivo) }}" alt="Imagen del contenido" style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px; padding: 5px;">
                                                @else
                                                    @if($extension === 'pdf')
                                                        <p><a href="{{ Storage::url($contenido->archivo) }}" download>Descargar</a></p>
                                                        <embed src="{{ Storage::url($contenido->archivo) }}" type="application/pdf" width="100%" height="600px" style="border: 1px solid #ddd; border-radius: 5px;">
                                                    @else
                                                        <p><a href="{{ Storage::url($contenido->archivo) }}" target="_blank">Ver</a></p>
                                                        <p><a href="{{ Storage::url($contenido->archivo) }}" download>Descargar</a></p>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p>No hay contenido disponible para este bimestre.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No hay bimestres disponibles.</p>
        @endif
    </div>
</div>
@endsection

