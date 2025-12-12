@extends('crudbooster::admin_template')

@section('title', 'Crear Registro')

@section('content')
<h1>Crear Registro</h1>

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

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('personas.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Navegación de pestañas -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="datospadre-tab" data-toggle="tab" href="#datospadre" role="tab" aria-controls="datospadre" aria-selected="true">Datos del Padre</a>
        </li>
          <!--
        <li class="nav-item">
            <a class="nav-link" id="datosmadre-tab" data-toggle="tab" href="#datosmadre" role="tab" aria-controls="datosmadre" aria-selected="false">Datos de la Madre</a>
        </li>
       
        <li class="nav-item">
            <a class="nav-link" id="datosencargado-tab" data-toggle="tab" href="#datosencargado" role="tab" aria-controls="datosencargado" aria-selected="false">Datos del Encargad@</a>
        </li>
         -->
        <li class="nav-item">
            <a class="nav-link" id="direccion-tab" data-toggle="tab" href="#direccion" role="tab" aria-controls="direccion" aria-selected="false">Dirección Domiciliar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="otros-tab" data-toggle="tab" href="#otros" role="tab" aria-controls="otros" aria-selected="false">Otros</a>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="myTabContent">
        <!-- Datos del Padre -->
        <div class="tab-pane fade show active" id="datospadre" role="tabpanel" aria-labelledby="datospadre-tab">
            <br>
            @include('personas.extensiones.formspadre')
        </div>

       

       

        <!-- Identificación -->
        <div class="tab-pane fade" id="direccion" role="tabpanel" aria-labelledby="direccion-tab">
            <br>
            @include('personas.extensiones.formsdireccion')
            <!-- Aquí podrías agregar el contenido relacionado con la identificación -->
        </div>

        <!-- Otros -->
        <div class="tab-pane fade" id="otros" role="tabpanel" aria-labelledby="otros-tab">
            <br>
            <!-- Aquí podrías agregar el contenido relacionado con "Otros" -->
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Guardar</button>
</form>

 

    <script>
        $(document).ready(function () {
            // Selecciona y activa el tab Datos Generales al cargar la página
            $('#myTab a[href="#datospadre"]').tab('show');
        });
    </script>
@endsection
