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
            <h3 class="box-title">Editar Materia</h3>
        </div>
        <div class="box-body">
            <form action="{{ route('materias.update', $materia->id) }}" method="POST">
                @csrf
                @method('PUT') <!-- Indica que se está realizando una actualización -->
                
                <div class="form-group">
                    <label for="materia_id">Materia</label>
                    <select name="materia_id" id="materia_id" class="form-control select2" required style="width: 30%"> 
                        <option value="">Selecciona una materia</option>
                        @foreach ($gestionMaterias as $gestionMateria)
                            <option value="{{ $gestionMateria->id }}" 
                                {{ old('materia_id', $materia->materia_id) == $gestionMateria->id ? 'selected' : '' }}>
                                {{ $gestionMateria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('materia_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="gestion_select">Gestión Escolar</label>
                        <select id="gestion_select" name="gestion_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione una Gestión</option>
                              
                            @foreach ($ngestiones as $ngestion)
                            <option value="{{ $ngestion->id }}" {{ old('gestion_id',$materia->cgshe->gestion_id) == $ngestion->id ? 'selected' : '' }}>
                                {{ $ngestion->gestion }}
                            </option>
                        @endforeach
                        
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nivel_select">Nivel</label>
                        <select id="nivel_select" name="nivel_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione un Nivel</option>
                           
                            @foreach ($niveles as $nivel)
                            <option value="{{ $nivel->id }}" {{ old('nivel_id',$materia->cgshe->nivel_id) == $nivel->id ? 'selected' : '' }}>
                                {{ $nivel->nivel }}
                            </option>
                        @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="curso_select">Curso</label>
                        <select id="curso_select" name="curso_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione un Curso</option>
                                @foreach ($cursos as $curso)
                                <option value="{{ $curso->id }}" {{ old('curso_id', $materia->cgshe->curso_id) == $curso->id ? 'selected' : '' }}>
                                    {{ $curso->curso }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="grado_select">Grado</label>
                        <select id="grado_select" name="grado_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione un Grado</option>
                            @foreach ($grados as $grado)
                            <option value="{{ $grado->id }}" {{ old('grado_id',$materia->cgshe->grado_id) == $grado->id ? 'selected' : '' }}>
                                {{ $grado->nombre }}
                            </option>
                        @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tercera columna -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="seccion_select">Sección</label>
                        <select id="seccion_select" name="seccion_id" class="form-control" style="width: 200px" required> 
                            <option value="">Seleccione una Sección</option>
                                @foreach ($secciones as $seccion)
                                <option value="{{ $seccion->id }}" {{ old('seccion_id',$materia->cgshe->seccion_id) == $seccion->id ? 'selected' : '' }}>
                                    {{ $seccion->seccion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jornada_select">Jornada:</label>
                        <select id="jornada_select" name="jornada_id" class="form-control" style="width: 200px" required>
                            <option value="">Seleccione Jornada</option>
                                @foreach ($jornadas as $jornada)
                                <option value="{{ $jornada->id }}" {{ old('jornada_id', $materia->cgshe->jornada_id) == $jornada->id ? 'selected' : '' }}>
                                    {{ $jornada->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado_id">Estado</label>
                        <select name="estado_id" id="estado_id" class="form-control select2" required style="width: 40%">
                            <option value="">Selecciona un estado</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id }}" 
                                    {{ old('estado_id', $materia->estado_id) == $estado->id ? 'selected' : '' }}>
                                    {{ $estado->estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Actualizar Materia</button>
                        <a href="{{ route('materias.index') }}" class="btn btn-default">Cancelar</a>
                    </div>
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

    // Similar a la vista create, agrega tus eventos para llenar selectores dinámicamente
    document.getElementById('gestion_select').addEventListener('change', function () {
        fetchOptions('/get-niveles', { gestion_id: this.value }, 'nivel_select', 'nivel');
    });

    document.getElementById('nivel_select').addEventListener('change', function () {
        fetchOptions('/get-cursos', { nivel_id: this.value }, 'curso_select', 'curso');
    });

    document.getElementById('curso_select').addEventListener('change', function () {
        fetchOptions('/get-grados', { curso_id: this.value }, 'grado_select', 'nombre');
    });

    document.getElementById('grado_select').addEventListener('change', function () {
        const cursoId = document.getElementById('curso_select').value;
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
