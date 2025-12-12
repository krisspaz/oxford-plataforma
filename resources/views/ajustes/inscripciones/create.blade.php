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
            <h3 class="box-title">Crear Nueva Inscripción</h3>
        </div>

        <div class="box-body">
            <form action="{{ route('ajustes_inscripciones.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="estudiante_id">Estudiante</label>
                    <select name="estudiante_id" id="estudiante_id" class="form-control select2">
                        <option value="">Seleccione</option>
                        @foreach ($estudiantes as $estudiante)
                            <option value="{{ $estudiante->id }}">{{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="cgshges_id">Asignación</label>
                    <select name="cgshges_id" id="cgshges_id" class="form-control select2">
                        <option value="">Seleccione</option>
                        @foreach ($cgshges as $cgshge)
                        <option value="{{ $cgshge->id }}" {{ $inscripcion->cgshges_id == $cgshge->id ? 'selected' : '' }}>{{ $cgshge->grados->nombre }} {{ $cgshge->cursos->curso }} {{ $cgshge->secciones->seccion }} {{ $cgshge->jornadas->jornada->nombre }}
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="paquete_id">Paquete</label>
                    <select name="paquete_id" id="paquete_id" class="form-control select2">
                        <option value="">Seleccione</option>
                        @foreach ($paquetes as $paquete)
                            <option value="{{ $paquete->id }}">{{ $paquete->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_inscripcion">Fecha de Inscripción</label>
                    <input type="date" name="fecha_inscripcion" id="fecha_inscripcion" class="form-control" required style="width: 30%">
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="Ciclo_escolar">Ciclo_Escolar:</label>
                        <select name="ciclo_escolar" id="ciclo_escolar" class="form-control select2" style="width: 200px">
                            <option value="">Seleccione un año</option>
                            @foreach($anios as $anio)
                                <option value="{{ $anio }}">{{ $anio }}</option>
                            @endforeach
                        </select>
                    </div>

                <div class="form-group">
                    <label for="estado_id">Estado</label>
                    <select name="estado_id" id="estado_id" class="form-control">
                        <option value="">Seleccione</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="cms_users_id">Usuario del Sistema</label>
                    <select name="cms_users_id" id="cms_users_id" class="form-control select2">
                        <option value="">Seleccione</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="{{ route('ajustes_inscripciones.index') }}" class="btn btn-default">Cancelar</a>
            </form>
        </div>
    </div>

    @push('bottom')
    <!-- Select2 CSS & JS -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
    
    <script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap4', // Opcional, puedes quitar si no usas Bootstrap 4
            placeholder: 'Seleccione',
            allowClear: true,
            theme: 'bootstrap4'
        });
    });
    </script>
    @endpush

@endsection
