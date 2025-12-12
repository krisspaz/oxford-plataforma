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
        <div class="box-header with-border">
            <h3 class="box-title">Listado de Estudiantes 2</h3>
        </div>

        <div class="box-body">
            <form action="{{ route('reporte.pdf') }}" method="GET">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="ciclo_escolar">Ciclo Escolar:</label>
                        <select name="ciclo_escolar" id="ciclo_escolar" class="form-control" required>
                            <option value="" selected disabled>Seleccione un ciclo</option>
                            @foreach ($ciclos as $ciclo)
                                <option value="{{ $ciclo }}">{{ $ciclo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="grado">Grado:</label>
                        <select name="grado" id="grado" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($grados as $grado)
                                <option value="{{ $grado->id }}">{{ $grado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="curso">Curso:</label>
                        <select name="curso" id="curso" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($cursos as $curso)
                                <option value="{{ $curso->id }}">{{ $curso->curso }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="nivel">Nivel:</label>
                        <select name="nivel" id="nivel" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($niveles as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nivel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="seccion">Sección:</label>
                        <select name="seccion" id="seccion" class="form-control">
                            <option value="">Todas</option>
                            @foreach ($secciones as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->seccion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="jornada">Jornada:</label>
                        <select name="jornada" id="jornada" class="form-control">
                            <option value="">Todas</option>
                            @foreach ($jornadas as $jornada)
                                <option value="{{ $jornada->id }}">{{ $jornada->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nuevo select para el tamaño de hoja -->
                    <div class="form-group col-md-6">
                        <label for="tamano_hoja">Tamaño de hoja:</label>
                        <select name="tamano_hoja" id="tamano_hoja" class="form-control">
                            <option value="A4" selected>A4</option>
                            <option value="Letter">Carta</option>
                            <option value="Legal">Oficio</option>
                        </select>
                    </div>

                    <!-- Nuevo select para la orientación -->
                    <div class="form-group col-md-6">
                        <label for="orientacion">Orientación:</label>
                        <select name="orientacion" id="orientacion" class="form-control">
                            <option value="portrait">Vertical</option>
                            <option value="landscape">Horizontal</option>
                        </select>
                    </div>
                </div>

                <div class="box-footer text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-file-pdf-o"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
