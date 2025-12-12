@extends('crudbooster::admin_template')


@section('content')


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
            <h3 class="box-title">Inscripciones nuevo</h>
        </div>

        <div class="box-body">
            <!-- Pestañas principales -->
            <ul class="nav nav-tabs" id="mainTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="perfil-estudiante-tab" data-toggle="tab" href="#perfil-estudiante" role="tab" aria-controls="perfil-estudiante" aria-selected="true">Perfil de Estudiante</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="perfil-padre-tab" data-toggle="tab" href="#perfil-padre" role="tab" aria-controls="perfil-padre" aria-selected="false">Perfil del Padre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="perfil-madre-tab" data-toggle="tab" href="#perfil-madre" role="tab" aria-controls="perfil-madre" aria-selected="false">Perfil de la Madre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="encargado-tab" data-toggle="tab" href="#encargado" role="tab" aria-controls="encargado" aria-selected="false">Encargado</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contrato-tab" data-toggle="tab" href="#contrato" role="tab" aria-controls="contrato" aria-selected="false">Contrato</a>
                </li>
        
                
            </ul>

            <!-- Contenido de las Pestañas principales -->
            <div class="tab-content" id="mainTabContent">
                <!-- Perfil del Estudiante -->
                <div class="tab-pane fade show active" id="perfil-estudiante" role="tabpanel" aria-labelledby="perfil-estudiante-tab">
                    <!-- Sub-Pestañas dentro del Perfil del Estudiante -->
                    <ul class="nav nav-tabs mt-3" id="studentProfileTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="datos-generales-tab" data-toggle="tab" href="#datos-generales" role="tab" aria-controls="datos-generales" aria-selected="true">Datos Generales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="historial-academico-tab" data-toggle="tab" href="#historial-academico" role="tab" aria-controls="historial-academico" aria-selected="false">Historial Académico</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="historial-medico-tab" data-toggle="tab" href="#historial-medico" role="tab" aria-controls="historial-medico" aria-selected="false">Historial Médico</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="documentacion-tab" data-toggle="tab" href="#documentacion" role="tab" aria-controls="documentacion" aria-selected="false">Documentación</a>
                        </li>
                    </ul>

                    <!-- Contenido de las Sub-Pestañas dentro del Perfil del Estudiante -->
                    <div class="tab-content mt-3" id="studentProfileTabContent">
                        <!-- Datos Generales -->
                        <div class="tab-pane fade show active" id="datos-generales" role="tabpanel" aria-labelledby="datos-generales-tab">
                              
                            @include('inscripcion.partials._form_alumno')  
                            <!-- Agrega más campos según lo que necesites -->
                        </div>

                        <!-- Historial Académico -->
                        <div class="tab-pane fade" id="historial-academico" role="tabpanel" aria-labelledby="historial-academico-tab">
                            <!-- Campos para el historial académico -->
                            @include('inscripcion.partials._form_academico')  
                           
                        </div>

                        <!-- Historial Médico -->
                        <div class="tab-pane fade" id="historial-medico" role="tabpanel" aria-labelledby="historial-medico-tab">
                            <!-- Campos para el historial médico -->
                            @include('inscripcion.partials._form_medicos')  
                            <!-- Agrega más campos según lo que necesites -->
                        </div>

                        <!-- Documentación -->
                        <div class="tab-pane fade" id="documentacion" role="tabpanel" aria-labelledby="documentacion-tab">
                            <!-- Campos para la documentación -->
                            @include('inscripcion.partials._form_documentacion')  
                            <!-- Agrega más campos según lo que necesites -->
                        </div>
                    </div>
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

<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 1500
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: '¡Oops!',
            text: "{{ implode(', ', $errors->all()) }}",
            showConfirmButton: true
        });
    @endif

    @if (Session::get('message') != '')
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: "{{ Session::get('message') }}",
            showConfirmButton: true
        });
    @endif
</script>



@endsection

@section('footer')
    <!-- Asegúrate de que jQuery se carga antes de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
