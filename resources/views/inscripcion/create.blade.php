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

<form action="{{ route('inscripcion.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Inscripciones</h3>
        </div>

        <div class="box-body">
            <!-- Pestañas principales -->
            <ul class="nav nav-tabs" id="mainTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="perfil-estudiante-tab" data-toggle="tab" href="#perfil-estudiante" role="tab" aria-controls="perfil-estudiante" aria-selected="true">Perfil de Estudiante</a>
                </li>
                <li class="nav-item" id="perfil-padre-tab-container">
                    <a class="nav-link" id="perfil-padre-tab" data-toggle="tab" href="#perfil-padre" role="tab" aria-controls="perfil-padre" aria-selected="false">Perfil del Padre</a>
                </li>
                <li class="nav-item" id="perfil-madre-tab-container">
                    <a class="nav-link" id="perfil-madre-tab" data-toggle="tab" href="#perfil-madre" role="tab" aria-controls="perfil-madre" aria-selected="false">Perfil de la Madre</a>
                </li>
                <li class="nav-item" id="encargado-tab-container">
                    <a class="nav-link" id="encargado-tab" data-toggle="tab" href="#encargado" role="tab" aria-controls="encargado" aria-selected="false">Encargado</a>
                </li>
            </ul>

            <!-- Contenido de las Pestañas principales -->
            <div class="tab-content mt-3" id="mainTabContent">
                <!-- Perfil del Estudiante -->
                <div class="tab-pane fade show active" id="perfil-estudiante" role="tabpanel" aria-labelledby="perfil-estudiante-tab">

                    <!-- Sub-Pestañas del estudiante -->
                    <ul class="nav nav-tabs mb-2" id="studentProfileTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="datos-generales-tab" data-toggle="tab" href="#datos-generales" role="tab" aria-controls="datos-generales" aria-selected="true">Datos Generales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="historial-medico-tab" data-toggle="tab" href="#historial-medico" role="tab" aria-controls="historial-medico" aria-selected="false">Historial Médico</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="documentacion-tab" data-toggle="tab" href="#documentacion" role="tab" aria-controls="documentacion" aria-selected="false">Documentación</a>
                        </li>
                    </ul>

                    <!-- Contenido Sub-Tabs -->
                    <div class="tab-content" id="studentProfileTabContent" style="overflow: hidden;">
                        <div class="tab-pane fade show active" id="datos-generales" role="tabpanel" aria-labelledby="datos-generales-tab">
                            @include('inscripcion.partials._form_alumno')
                        </div>

                        <div class="tab-pane fade" id="historial-medico" role="tabpanel" aria-labelledby="historial-medico-tab">
                            @include('inscripcion.partials._form_medicos')
                        </div>

                        <div class="tab-pane fade" id="documentacion" role="tabpanel" aria-labelledby="documentacion-tab">
                            @include('inscripcion.partials._form_documentacion')
                        </div>
                    </div>
                </div>

                <!-- Perfil del Padre -->
                <div class="tab-pane fade" id="perfil-padre" role="tabpanel" aria-labelledby="perfil-padre-tab">
                    @include('inscripcion.partials._form_padre')
                </div>

                <!-- Perfil de la Madre -->
                <div class="tab-pane fade" id="perfil-madre" role="tabpanel" aria-labelledby="perfil-madre-tab">
                    @include('inscripcion.partials._form_madre')
                </div>

                <!-- Encargado -->
                <div class="tab-pane fade" id="encargado" role="tabpanel" aria-labelledby="encargado-tab">
                    @include('inscripcion.partials.form_encargado')
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Registrar Estudiante</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const assignMotherCheckbox = document.getElementById('datos_madre');
    const assignUnicoMotherCheckbox = document.getElementById('unico_madre');
    const assignUnicoFatherCheckbox = document.getElementById('unico_padre');
    const assignFatherCheckbox = document.getElementById('datos_padre');

    const tabContainerEncargado = document.getElementById('encargado-tab-container');
    const tabContainerMadre = document.getElementById('perfil-madre-tab-container');
    const tabContainerPadre = document.getElementById('perfil-padre-tab-container');

    const toggleTabsVisibility = () => {
        tabContainerEncargado.classList.remove('d-none');
        tabContainerMadre.classList.remove('d-none');
        tabContainerPadre.classList.remove('d-none');

        if(assignMotherCheckbox.checked || assignFatherCheckbox.checked){
            tabContainerEncargado.classList.add('d-none');
        }

        if(assignUnicoFatherCheckbox.checked){
            tabContainerMadre.classList.add('d-none');
        }

        if(assignUnicoMotherCheckbox.checked){
            tabContainerPadre.classList.add('d-none');
        }
    }

    const toggleCheckboxes = () => {
        assignFatherCheckbox.disabled = assignMotherCheckbox.checked;
        assignMotherCheckbox.disabled = assignFatherCheckbox.checked;
        assignUnicoMotherCheckbox.disabled = assignUnicoFatherCheckbox.checked;
        assignUnicoFatherCheckbox.disabled = assignUnicoMotherCheckbox.checked;
    }

    [assignMotherCheckbox, assignFatherCheckbox, assignUnicoFatherCheckbox, assignUnicoMotherCheckbox].forEach(chk => {
        chk.addEventListener('change', () => {
            toggleTabsVisibility();
            toggleCheckboxes();
        });
    });
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

@endsection


