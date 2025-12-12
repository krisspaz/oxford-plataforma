@extends('crudbooster::admin_template')

@section('content')
    <div class="box box-default">

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
        <div class="box-header with-border">
            <h3 class="box-title">Parametrizar Materias</h3>
        </div>
        <div class="box-body">
            <form action="{{ route('materias.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="materia_id">Materia</label>
                    <select name="materia_id" id="materia_id" class="form-control select2" required style="width: 30%">
                        <option value="">Selecciona una materia</option>
                        @foreach ($gestionMaterias as $gestionMateria)
                            <option value="{{ $gestionMateria->id }}" {{ old('materia_id') == $gestionMateria->id ? 'selected' : '' }}>
                                {{ $gestionMateria->codigo }} {{ $gestionMateria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('materia_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
             

                <div class="col-md-4">
                   
    
                    <div class="form-group">
                        <label for="gestion_select">Gestión Escolar</label>
                        <select id="gestion_select" name="gestion_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione una Gestión</option>
                            @foreach ($ngestiones as $ngestion)
                                <option value="{{ $ngestion->id }}">{{ $ngestion->gestion }}</option>
                            @endforeach
                        </select>
                    </div>
    
                    <div class="form-group">
                        <label for="nivel_select">Nivel</label>
                        <select id="nivel_select" name="nivel_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione un Nivel</option>
                        </select>
                    </div>
    
                    <div class="form-group">
                        <label for="curso_select">Curso</label>
                        <select id="curso_select" name="curso_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione el Curso</option>
                        </select>
                    </div>
    
                    <div class="form-group">
                        <label for="grado_select">Grado</label>
                        <select id="grado_select" name="grado_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione un Grado</option>
                        </select>
                    </div>
                </div>
    
                <!-- Tercera columna -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="seccion_select">Sección</label>
                        <select id="seccion_select" name="seccion_id" class="form-control" style="width: 200px" required> 
                            <option value="">Seleccione una Sección</option>
                        </select>
                    </div>
    
                    <div class="form-group">
                        <label for="jornada_select">Jornada:</label>
                        <select id="jornada_select" name="jornada_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione Jornada</option>
                        </select>
                    </div>

                <div class="form-group">
                    <label for="estado_id">Estado</label>
                    <select name="estado_id" id="estado_id" class="form-control select2" required>
                        <option value="">Selecciona un estado</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Guardar Materia</button>
                    <a href="{{ route('materias.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>


@push('javascript')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>


@endpush

<script>
    function fetchOptions(url, params, targetSelectId, textField) {
        const query = new URLSearchParams(params).toString();
    
        fetch(`${url}?${query}`)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById(targetSelectId);
                select.innerHTML = '<option value="">Seleccione una opción</option>';
                data.forEach(item => {
                    select.innerHTML += `<option value="${item.id}">${item[textField]}</option>`;
                });
            })
            .catch(error => console.error('Error:', error));
    }
    
    document.getElementById('gestion_select').addEventListener('change', function () {
        fetchOptions('/get-niveles', { gestion_id: this.value }, 'nivel_select', 'nivel');
    });
    
    document.getElementById('nivel_select').addEventListener('change', function () {
        fetchOptions('/get-cursos', { nivel_id: this.value }, 'curso_select', 'curso');
    });
    
    document.getElementById('curso_select').addEventListener('change', function () {
        fetchOptions('/get-grados', { curso_id: this.value }, 'grado_select', 'nombre');
    });
    
    document.getElementById('curso_select').addEventListener('change', function () {
        const cursoId = this.value;
        const gradoId = document.getElementById('grado_select').value; // Suponiendo que tienes un elemento con id 'grado_select'
    
        if (cursoId && gradoId) {
            fetchOptions('/get-secciones', { curso_id: cursoId, grado_id: gradoId }, 'seccion_select', 'seccion');
           
        }
        fetchOptions('/get-paquetes', { curso_id: cursoId }, 'paquete_select', 'nombre');
    });
    
    document.getElementById('grado_select').addEventListener('change', function () {
        const cursoId = document.getElementById('curso_select').value; // Suponiendo que tienes un elemento con id 'curso_select'
        const gradoId = this.value;
    
        if (cursoId && gradoId) {
            fetchOptions('/get-secciones', { curso_id: cursoId, grado_id: gradoId }, 'seccion_select', 'seccion');
        }
    });
    
    
    document.getElementById('seccion_select').addEventListener('change', function () {
        fetchOptions('/get-jornadas', { seccion_id: this.value }, 'jornada_select', 'nombre');
    });

    </script>
@endsection
