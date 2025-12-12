@extends('crudbooster::admin_template')

@section('content')
<div class="box">

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
    <div class="box-header">
        <h1 class="box-title">Agregar Contenido a Materia</h1>
    </div>
    <div class="box-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contenido_materias.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group col-md-6">
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" id="titulo" class="form-control" required>
            </div>

            <div class="form-group col-md-12">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="5" required></textarea>
            </div>



              <!-- Bimestre -->

              <div class="form-group">
                <label for="bimestre_id">Bimestre</label>
                <select name="bimestre_id" id="bimestre_id" class="form-control" style="width: 15%">
                    @if($bimestreActual)
                        <option value="{{ $bimestreActual->id }}">
                            {{ $bimestreActual->nombre }}
                        </option>
                    @endif
                </select>
                @error('bimestre_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

          <!-- Materia -->
            <div class="form-group">
                <label for="materia_id">Materia</label>
                <select name="materia_id[]" id="materia_id" class="form-control" multiple style="width: 80%">
                    @foreach ($materias->materiasCursos as $materia)
                        <option value="{{ $materia->id }}"
                            {{ in_array($materia->id, old('materia_id', [])) ? 'selected' : '' }}>
                            {{ $materia->materia->gestionMateria->nombre }}
                            ({{ $materia->cgshges->grados->nombre }}
                            {{ $materia->cgshges->curso->curso }}
                            '{{ $materia->cgshges->secciones->seccion }}'
                            {{ $materia->cgshges->jornadas->jornada->nombre }})
                        </option>
                    @endforeach
                </select>
                @error('materia_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>







            <!-- Tipo de Contenido -->
            <div class="form-group">
                <label for="tipo_contenido">Tipo de Contenido</label>
                <select name="tipo_contenido" id="tipo_contenido" class="form-control" onchange="toggleInputFields()">
                    <option value="">Seleccione un tipo</option>
                    <option value="video" {{ old('tipo_contenido') == 'video' ? 'selected' : '' }}>Video</option>
                    <option value="audio" {{ old('tipo_contenido') == 'audio' ? 'selected' : '' }}>Audio</option>
                    <option value="documento" {{ old('tipo_contenido') == 'documento' ? 'selected' : '' }}>Documento</option>
                    <option value="imagen" {{ old('tipo_contenido') == 'imagen' ? 'selected' : '' }}>Imagen</option>
                    <option value="link" {{ old('tipo_contenido') == 'link' ? 'selected' : '' }}>Link</option>
                </select>
                @error('tipo_contenido')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Archivo -->
            <div class="form-group" id="archivo-field">
                <label for="archivo">Archivo</label>
                <input type="file" name="archivo" id="archivo" class="form-control" accept="video/*,audio/*,image/*,.pdf,.doc,.docx">
                @error('archivo')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Link -->
            <div class="form-group" id="link-field" style="display: none;">
                <label for="link">Link</label>
                <input type="text" name="link" id="link" class="form-control" value="{{ old('link') }}">

            </div>

            <!-- Docente -->
            <div class="form-group">
                <label for="docente_id">Docente</label>
                <select name="docente_id" id="docente_id" class="form-control">
                    <option value="">Seleccione un docente</option>

                        <option value="{{ $docente->id }}" {{ old('docente_id') == $docente->id ? 'selected' : '' }}>
                            {{ $docente->persona->nombres}}  {{ $docente->persona->apellidos}}
                        </option>

                </select>
                @error('docente_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Botones -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('contenido_materias.index') }}" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', toggleInputFields);

    function toggleInputFields() {
        const tipoContenido = document.getElementById('tipo_contenido').value;
        const archivoField = document.getElementById('archivo-field');
        const linkField = document.getElementById('link-field');

        if (tipoContenido === 'link') {
            archivoField.style.display = 'none';
            linkField.style.display = 'block';
        } else {
            archivoField.style.display = 'block';
            linkField.style.display = 'none';
        }
    }
</script>
@endsection
