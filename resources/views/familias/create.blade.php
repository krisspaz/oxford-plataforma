@extends('crudbooster::admin_template')


@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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


@if (session('success'))
<div>
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if ( Session::get('message') != '' )
<div class='alert alert-warning'>
    {{ Session::get('message') }}
</div>
@endif
<form action="{{ route('familias.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Agregar Estudiante a  Familia (
                 {{$familia->nombre_familiar}} ) Codigo: {{$familia->codigo_familiar}}</h>
                 <input type="hidden" name="codigo" value="{{$familia->codigo_familiar}}">
                 <input type="hidden" name="id_padre" value="{{ $familia->padre->id }}">

                 <input type="hidden" name="id_madre" value="{{ $familia->madre->id }}">
                 <input type="hidden" name="id_encargado" value="{{ $familia->encargado->id }}">
                 <input type="hidden" name="nombre_familiar" value="{{ $familia->nombre_familiar }}">
                </div>

        <div class="box-body">
            <!-- Pestañas principales -->
            <ul class="nav nav-tabs" id="mainTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="perfil-estudiante-tab" data-toggle="tab" href="#perfil-estudiante" role="tab" aria-controls="perfil-estudiante" aria-selected="true">Datos Generales</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="perfil-madre-tab" data-toggle="tab" href="#perfil-madre" role="tab" aria-controls="perfil-madre" aria-selected="false">Hisotrial Medico</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="encargado-tab" data-toggle="tab" href="#encargado" role="tab" aria-controls="encargado" aria-selected="false">Documentos</a>
                </li>



            </ul>

            <!-- Contenido de las Pestañas principales -->
            <div class="tab-content" id="mainTabContent">
                <!-- Perfil del Estudiante -->
                <div class="tab-pane fade show active" id="perfil-estudiante" role="tabpanel" aria-labelledby="perfil-estudiante-tab">
                    <!-- Sub-Pestañas dentro del Perfil del Estudiante -->
                    @include('inscripcion.partials._form_alumno')

                    <!-- Contenido de las Sub-Pestañas dentro del Perfil del Estudiante -->
                    <div class="tab-content mt-3" id="studentProfileTabContent">
                        <!-- Datos Generales -->






                    </div>
                </div>



                <!-- Perfil de la Madre -->
                <div class="tab-pane fade" id="perfil-madre" role="tabpanel" aria-labelledby="perfil-madre-tab">
                    <!-- Campos del perfil de la madre -->
                    @include('inscripcion.partials._form_medicos')

                    <!-- Agrega más campos según lo que necesites -->
                </div>

                <!-- Encargado -->
                <div class="tab-pane fade" id="encargado" role="tabpanel" aria-labelledby="encargado-tab">
                    <!-- Campos del encargado -->
                    @include('inscripcion.partials._form_documentacion')
                    <!-- Agrega más campos según lo que necesites -->
                </div>



            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Registrar Estudiante</button>

</form>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Selección de los checkbox y del contenedor de campos
        const assignMotherCheckbox = document.getElementById('datos_madre');
        const assignFatherCheckbox = document.getElementById('datos_padre');
        const fieldsContainer = document.getElementById('ocultar');
        const tabContainer = document.getElementById('encargado-tab');


        // Función para manejar la visibilidad
        const toggleFieldsVisibility = () => {
            if (assignMotherCheckbox.checked || assignFatherCheckbox.checked) {
                fieldsContainer.style.display = 'none'; // Oculta los campos
                tabContainer.style.display = 'none'; // Oculta los campos
            } else {
                fieldsContainer.style.display = 'block'; // Muestra los campos
                tabContainer.style.display = 'block'; // Oculta los campos
            }
        };

        const toggleCheckboxes = () => {
            if (assignMotherCheckbox.checked) {
                assignFatherCheckbox.disabled = true; // Bloquea el checkbox del padre
            } else {
                assignFatherCheckbox.disabled = false; // Desbloquea el checkbox del padre
            }

            if (assignFatherCheckbox.checked) {
                assignMotherCheckbox.disabled = true; // Bloquea el checkbox de la madre
            } else {
                assignMotherCheckbox.disabled = false; // Desbloquea el checkbox de la madre
            }
        };

        // Asignar eventos a los checkbox
        assignMotherCheckbox.addEventListener('change', () => {
            toggleFieldsVisibility();
            toggleCheckboxes();
        });

        assignFatherCheckbox.addEventListener('change', () => {
            toggleFieldsVisibility();
            toggleCheckboxes();
        });
    });



</script>



    <!-- Asegúrate de que jQuery se carga antes de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
