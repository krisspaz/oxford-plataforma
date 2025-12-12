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
<div class="container">
    <h2>Editar Datos del Personal Administrativo</h2>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
   @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


    <form action="{{ route('administrativos.update', $administrativo->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <h1>Datos del Personal Administrativo</h1>



        <div class="row mt-3">
            <!-- Columna para la fotografía (izquierda) -->
            <div class="col-md-3">
                <div class="form-group">
                    <div class="form-group">
                        <label for="fotografia_administrativo">Fotografía:</label>
                        <input type="file" class="form-control-file" id="fotografia_administrativo" name="fotografia_administrativo" accept="image/*" onchange="previewImage(event)">
                        <img id="imagePreview" 
                             src="{{ isset($administrativo) && $administrativo->fotografia_administrativo ? asset('storage/' . $administrativo->fotografia_administrativo) : '#' }}" 
                             alt="Vista Previa" 
                             style="{{ isset($administrativo) && $administrativo->fotografia_administrativo ? 'max-width: 150px;' : 'display: none; max-width: 150px; margin-top: 10px;' }}" />
                    </div>
                </div>
            </div>
            
            @php
            $noPermitidos = ['DOCENTE', 'ESTUDIANTE', 'ENCARGADO', 'PADRES'];
        @endphp
            <!-- Columna para el resto de los campos (derecha) -->
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">

                      
                        <div class="form-group">
                            <label for="privilegio_id_administrativo">Nombre del Privilegio</label>
                            <select name="privilegio_id_administrativo" id="privilegio_id_administrativo" class="form-control">
                                @foreach ($privilegios as $privilegio)
                                    @if (!in_array($privilegio->name, $noPermitidos))
                                        <option value="{{ $privilegio->id }}" {{  $administrativo->persona->cmsUser->id_cms_privileges === $privilegio->id ? 'selected' : ''}}>
                                            {{ $privilegio->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nombres_administrativo">Nombres</label>
                            <input type="text" name="nombres_administrativo" class="form-control" value="{{ $administrativo->persona->nombres }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="apellidos_administrativo">Apellidos</label>
                            <input type="text" name="apellidos_administrativo" class="form-control"  value="{{ $administrativo->persona->apellidos }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="genero_administrativo">Género</label>
                            <select name="genero_administrativo" id="genero_administrativo" class="form-control">
                                <option value="Masculino" {{ $administrativo->persona->genero == "Masculino" ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ $administrativo->persona->genero == "Femenino" ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_nacimiento_administrativo">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento_administrativo" name="fecha_nacimiento_administrativo" class="form-control" value="{{ $administrativo->persona->fecha_nacimiento }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="anios_administrativo">Años Cumplidos</label>
                            <input type="text" id="anios_administrativo" name="anios_administrativo" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_tipoidentificacion_administrativo">Tipo de Identificación</label>
                            <select name="id_tipoidentificacion_administrativo" id="id_tipoidentificacion_administrativo" class="form-control">
                                @foreach ($tiposIdentificacion as $tipo)
                                    <option value="{{ $tipo->id }}" {{ $tipo->id == $administrativo->persona->identificacion_documentos_id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="identificacion_administrativo">No. de Identificación</label>
                            <input type="text" name="identificacion_administrativo" class="form-control" value="{{ $administrativo->persona->num_documento }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cargo_id">Cargo</label>
                            <select name="cargo_id" id="cargo_id" class="form-control">
                                @foreach ($cargos as $cargo)
                                    <option value="{{ $cargo->id }}" {{ (old('cargo_id',  $administrativo->cargo_id ?? '') == $cargo->id) ? 'selected' : '' }} >{{ $cargo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="telefono_administrativo">Teléfono</label>
                            <input type="text" name="telefono_administrativo" class="form-control" value="{{ $administrativo->persona->telefono }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="correo_administrativo">Email</label>
                            <input type="email" name="correo_administrativo" class="form-control" value="{{ $administrativo->persona->email }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="direccion_administrativo">Dirección</label>
                            <textarea name="direccion_administrativo" id="direccion_administrativo" rows="3" class="form-control" placeholder="Escribe la dirección completa" required>{{ $administrativo->persona->direccion }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_defuncion_administrativo">QEPD</label>
                            <input type="date" id="fecha_defuncion_administrativo" name="fecha_defuncion_administrativo" class="form-control">
                        </div>
                    </div>


                </div>
            </div>
        </div>
        
   
          
        
        

        {{-- Botón para enviar --}}
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('administrativos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>


<script>
    document.getElementById('fecha_nacimiento_administrativo').addEventListener('change', function() {
        var fechaNacimiento = new Date(this.value);
        var hoy = new Date();
        var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        var mes = hoy.getMonth() - fechaNacimiento.getMonth();
        var dia = hoy.getDate() - fechaNacimiento.getDate();
    
        if (mes < 0 || (mes === 0 && dia < 0)) {
            edad--;
        }
    
        if (edad < 0) {
            edad = 0;
        }
    
        document.getElementById('anios_administrativo').value = edad;
    });
    </script>
    
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('imagePreview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection

