@extends('crudbooster::admin_template')



@section('title', 'Municipio List')

@section('content')
    <h1>Crear Alumno</h1>

    <form action="{{ route('alumnos.store') }}" method="POST">
        @csrf

        <h2>Información del Padre</h2>
        <div class="row g-3">
            <div class="col-md-6">
              <label for="padre_nombre" class="form-label">Nombres</label>
              <input type="text" class="form-control" id="padre_nombre" name="padre_nombre">
            </div>
            <div class="col-md-6">
              <label for="padre_apellido" class="form-label">Apellidos</label>
              <input type="text" class="form-control" id="padre_apellido" name="padre_apellido">
            </div>
            <div class="col-md-6">
              <label for="padre_email" class="form-label">Email</label>
              <input type="email" class="form-control" id="padre_email" name="padre_email">
            </div>
            <div class="col-md-6">
                <label for="padre_telefono" class="form-label">Telefono</label>
                <input type="text" class="form-control" id="padre_telefono" name="padre_telefono">
              </div>
            <div class="col-md-12">
                <label for="padre_direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="padre_direccion" placeholder="Apartment, studio, or floor" name="padre_direccion">
              </div>
        </div>

   
        <h2>Información de la Madre</h2>


        <div class="row g-3">
            <div class="col-md-6">
              <label for="madre_nombre" class="form-label">Nombres</label>
              <input type="text" class="form-control" id="madre_nombre" name="madre_nombre">
            </div>
            <div class="col-md-6">
              <label for="madre_apellido" class="form-label">Apellidos</label>
              <input type="text" class="form-control" id="madre_apellido" name="madre_apellido">
            </div>
            <div class="col-md-6">
              <label for="madre_email" class="form-label">Email</label>
              <input type="email" class="form-control" id="madre_email" name="madre_email">
            </div>
            <div class="col-md-6">
                <label for="madre_telefono" class="form-label">Telefono</label>
                <input type="text" class="form-control" id="madre_telefono" name="madre_telefono">
              </div>
            <div class="col-md-12">
                <label for="madre_direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="madre_direccion" placeholder="Apartment, studio, or floor" name="madre_direccion">
              </div>
        </div>
      
       


        <h2>Información del Encargado</h2> 
       
        <div class="row g-3">
            <div class="col-md-6">
              <label for="encargado_nombre" class="form-label">Nombres</label>
              <input type="text" class="form-control" id="encargado_nombre" name="encargado_nombre">
            </div>
            <div class="col-md-6">
              <label for="encargado_apellido" class="form-label">Apellidos</label>
              <input type="text" class="form-control" id="encargado_apellido" name="encargado_apellido">
            </div>
            <div class="col-md-6">
              <label for="encargado_email" class="form-label">Email</label>
              <input type="email" class="form-control" id="encargado_email" name="encargado_email">
            </div>
            <div class="col-md-6">
                <label for="encargado_telefono" class="form-label">Telefono</label>
                <input type="text" class="form-control" id="encargado_telefono" name="encargado_direccion">
              </div>
            <div class="col-md-12">
                <label for="encargado_direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="encargado_direccion" placeholder="Apartment, studio, or floor" name="encargado_direccion">
              </div>
        </div>

    


        <h2>Información del Alumno</h2>


        <div class="row g-3">
            <div class="col-md-6">
              <label for="alumno_codigo" class="form-label">Codigo</label>
              <input type="text" class="form-control" id="alumno_codigo" name="alumno_codigo" required>
            </div>
            <div class="col-md-6">
              <label for="alumno_nombre" class="form-label">Nombres</label>
              <input type="text" class="form-control" id="alumno_nombre" name="alumno_nombre" required>
            </div>
            <div class="col-md-6">
                <label for="alumno_apellido" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="alumno_apellido" name="alumno_apellido" required>
              </div>
            <div class="col-md-6">
              <label for="madre_email" class="form-label">Email</label>
              <input type="email" class="form-control" id="madre_email" name="madre_email">
            </div>
            <div class="col-md-6">
                <label for="alumno_fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="alumno_fecha_nacimiento" name="alumno_fecha_nacimiento">
              </div>
              <div class="col-md-6">
                <label for="alumno_grado" class="form-label">Grado:</label>
                <input type="text" class="form-control" id="alumno_grado" name="alumno_grado" required>
              </div>
            <div class="col-md-12">
                <label for="madre_direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="madre_direccion" placeholder="Apartment, studio, or floor" name="madre_direccion" required>
              </div>
        </div>
    <br>
       
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
       
    </form>
    @endsection

