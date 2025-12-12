<!DOCTYPE html>
<html>
<head>
    <title>Registrar Alumnos y Tutores</title>
    <script>
        let alumnoIndex = 0;

        function addAlumno() {
            alumnoIndex++;
            let container = document.getElementById('alumnos-container');
            let newAlumno = `
                <div class="alumno" id="alumno-${alumnoIndex}">
                    <h3>Alumno ${alumnoIndex}</h3>
                    <label for="alumnos[${alumnoIndex}][codigo]">Código:</label>
                    <input type="text" id="alumnos[${alumnoIndex}][codigo]" name="alumnos[${alumnoIndex}][codigo]" required>
                    <label for="alumnos[${alumnoIndex}][nombre]">Nombre:</label>
                    <input type="text" id="alumnos[${alumnoIndex}][nombre]" name="alumnos[${alumnoIndex}][nombre]" required>
                    <label for="alumnos[${alumnoIndex}][apellido]">Apellido:</label>
                    <input type="text" id="alumnos[${alumnoIndex}][apellido]" name="alumnos[${alumnoIndex}][apellido]" required>
                    <label for="alumnos[${alumnoIndex}][fecha_nacimiento]">Fecha de Nacimiento:</label>
                    <input type="date" id="alumnos[${alumnoIndex}][fecha_nacimiento]" name="alumnos[${alumnoIndex}][fecha_nacimiento]" required>
                    <label for="alumnos[${alumnoIndex}][grado]">Grado:</label>
                    <input type="text" id="alumnos[${alumnoIndex}][grado]" name="alumnos[${alumnoIndex}][grado]" required>
                    <button type="button" onclick="removeAlumno(${alumnoIndex})">Eliminar Alumno</button>
                    <br><br>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newAlumno);
        }

        function removeAlumno(index) {
            let alumnoDiv = document.getElementById(`alumno-${index}`);
            if (alumnoDiv) {
                alumnoDiv.remove();
            }
        }
    </script>
</head>
<body>
    <h1>Registrar Alumnos y Tutores</h1>

    <form action="{{ route('padres.store') }}" method="POST">
        @csrf

        <h2>Información del Padre</h2>
        <label for="padre_nombre">Nombre:</label>
        <input type="text" id="padre_nombre" name="padre[nombre]" value="{{ old('padre.nombre') }}">
        <label for="padre_apellido">Apellido:</label>
        <input type="text" id="padre_apellido" name="padre[apellido]" value="{{ old('padre.apellido') }}">
        <label for="padre_email">Email:</label>
        <input type="email" id="padre_email" name="padre[email]" value="{{ old('padre.email') }}">
        <label for="padre_telefono">Teléfono:</label>
        <input type="text" id="padre_telefono" name="padre[telefono]" value="{{ old('padre.telefono') }}">
        <label for="padre_direccion">Dirección:</label>
        <input type="text" id="padre_direccion" name="padre[direccion]" value="{{ old('padre.direccion') }}">

        <h2>Información de la Madre</h2>
        <label for="madre_nombre">Nombre:</label>
        <input type="text" id="madre_nombre" name="madre[nombre]" value="{{ old('madre.nombre') }}">
        <label for="madre_apellido">Apellido:</label>
        <input type="text" id="madre_apellido" name="madre[apellido]" value="{{ old('madre.apellido') }}">
        <label for="madre_email">Email:</label>
        <input type="email" id="madre_email" name="madre[email]" value="{{ old('madre.email') }}">
        <label for="madre_telefono">Teléfono:</label>
        <input type="text" id="madre_telefono" name="madre[telefono]" value="{{ old('madre.telefono') }}">
        <label for="madre_direccion">Dirección:</label>
        <input type="text" id="madre_direccion" name="madre[direccion]" value="{{ old('madre.direccion') }}">

        <h2>Información del Encargado</h2>
        <label for="encargado_nombre">Nombre:</label>
        <input type="text" id="encargado_nombre" name="encargado[nombre]" value="{{ old('encargado.nombre') }}">
        <label for="encargado_apellido">Apellido:</label>
        <input type="text" id="encargado_apellido" name="encargado[apellido]" value="{{ old('encargado.apellido') }}">
        <label for="encargado_email">Email:</label>
        <input type="email" id="encargado_email" name="encargado[email]" value="{{ old('encargado.email') }}">
        <label for="encargado_telefono">Teléfono:</label>
        <input type="text" id="encargado_telefono" name="encargado[telefono]" value="{{ old('encargado.telefono') }}">
        <label for="encargado_direccion">Dirección:</label>
        <input type="text" id="encargado_direccion" name="encargado[direccion]" value="{{ old('encargado.direccion') }}">

        <h2>Información de los Alumnos</h2>
        <div id="alumnos-container">
            <!-- Campos de alumnos se agregarán aquí dinámicamente -->
        </div>
        <button type="button" onclick="addAlumno()">Agregar Alumno</button>

        <br><br>
        <button type="submit">Guardar Todos los Datos</button>
    </form>
</body>
</html>
