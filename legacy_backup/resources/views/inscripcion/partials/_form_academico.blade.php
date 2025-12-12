<div class="row"> 
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Historial Académico</h1>
                <br>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="nivel[]">Nivel</label>
                            <select name="nivel[]" id="nivel" class="form-control">
                                <option value="">Seleccione un Nivel</option>
                                @foreach($niveles as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nivel }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="grado">Grado</label>
                            <select name="grado[]" id="grado" class="form-control">
                                <option value="">Seleccione un Grado</option>
                                @foreach($grados as $grado)
                                <option value="{{ $grado->id }}">{{ $grado->nombre }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label for="curso">Curso</label>
                            <select name="curso[]" id="curso" class="form-control">
                                <option value="">Seleccione un Curso</option>
                                @foreach($ncursos as $ncurso)
                                <option value="{{ $ncurso->id }}">{{ $ncurso->curso }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="anio">Año</label>
                            <input type="number" name="anio[]" id="anio" class="form-control" >
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="establecimiento">Establecimiento</label>
                    <input type="text" name="establecimiento[]" id="establecimiento" class="form-control" >
                </div>



                
                <button type="button" class="btn btn-primary" id="addButton">Añadir</button>
              
            </div>
        </div>
    </div>

    <div class="col-sm-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Grados Cursados</h5>
                <table id="historialTable" class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Nivel</th>
                            <th>Grado</th>
                            <th>Curso</th>
                            <th>Año</th>
                            <th>Establecimiento</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los registros se añadirán aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


    <input type="hidden" name="historialAcademico" id="historialAcademico">
    <!-- Otros campos del formulario principal aquí -->



<div class="card w-50 mt-3">
  <div class="card-body">
    <h5 class="card-title text-center">Asignación de Cursos</h5>
    <p class="card-text text-center">Matricule aquí al Alumno</p>

    <div class="form-group">
      <label for="gestion_select">Gestión o Ciclo Escolar</label>
      <select id="gestion_select" name="gestion_id" class="form-control">
        <option value="">Seleccione una Gestión</option>
        @foreach ($ngestiones as $ngestion) 
        <option value="{{ $ngestion->id }}">{{ $ngestion->gestion }}</option>
          @endforeach
      </select>
    </div>

    <div class="form-group">
      <label for="nivel_select">Nivel</label>
      <select id="nivel_select" name="nivel_id" class="form-control">
        <option value="">Seleccione un Nivel</option>
      
      </select>
    </div>

    <div class="form-group">
        <label for="curso_select">Curso</label>
        <select id="curso_select" name="curso_id" class="form-control">
          <option value="">Seleccione el Curso</option>
        
        </select>
      </div>

    <div class="form-group">
      <label for="grado_select">Grado</label>
      <select name="grado_id" id="grado_select" class="form-control">
        <option value="">Seleccione un grado</option>
       
    </select>
    </div>

    <div class="form-group">
      <label for="seccion_select">Sección</label>
      <select id="seccion_select" name="seccion_id" class="form-control">
        <option value="">Seleccione un curso</option>
      </select>
    </div>

    <div class="form-group">
        <label for="jornada_select">Jornada:</label>
        <select id="jornada_select" name="jornada_id" class="form-control">
            <option value="">Seleccione Jornada</option>
        </select>
    </div>

   

  
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<script>
$(document).ready(function() {
    let historialData = [];

    function updateHiddenField() {
        $('#historialAcademico').val(JSON.stringify(historialData));
    }

    function updateTable() {
        const tableBody = $('#historialTable tbody');
        tableBody.empty();

        historialData.forEach((item, index) => {
            // Obtener el texto asociado a cada ID para mostrarlo en la tabla
            const nivelText = $('#nivel option[value="' + item.nivel + '"]').text();
            const gradoText = $('#grado option[value="' + item.grado + '"]').text();
            const cursoText = $('#curso option[value="' + item.curso + '"]').text();

            tableBody.append(`
                <tr>
                    <td>${nivelText}</td>
                    <td>${gradoText}</td>
                    <td>${cursoText}</td>
                    <td>${item.anio}</td>
                    <td>${item.establecimiento}</td>
                    <td>
                        <button type="button" class="btn btn-warning edit-button" data-index="${index}">Editar</button>
                        <button type="button" class="btn btn-danger delete-button" data-index="${index}">Eliminar</button>
                    </td>
                </tr>
            `);
        });
    }

    $('#addButton').click(function() {
        // Capturar los IDs de nivel, grado y curso
        const nivelId = $('#nivel').val();
        const gradoId = $('#grado').val();
        const cursoId = $('#curso').val();

        // Capturar el texto que deseas mostrar en la tabla (esto no afectará el almacenamiento de IDs)
        const nivelText = $('#nivel option:selected').text();
        const gradoText = $('#grado option:selected').text();
        const cursoText = $('#curso option:selected').text();

        if (!nivelId || !gradoId || !cursoId || !$('#anio').val() || !$('#establecimiento').val()) {
            alert('Por favor complete todos los campos antes de añadir.');
            return;
        }

        // Crear el objeto con los valores a guardar
        const nuevoRegistro = {
            nivel: nivelId, // Guardar el ID
            grado: gradoId, // Guardar el ID
            curso: cursoId, // Guardar el ID
            anio: $('#anio').val(), // Guardar el texto del año
            establecimiento: $('#establecimiento').val() // Guardar el texto del establecimiento
        };

        
        // Añadir el registro a la lista de historial
        historialData.push(nuevoRegistro);
        updateTable(); // Actualizar la tabla
        updateHiddenField(); // Actualizar el campo oculto

        // Limpiar los campos después de añadir
        $('#nivel').val('');
        $('#grado').val('');
        $('#curso').val('');
        $('#anio').val('');
        $('#establecimiento').val('');
    });

    $(document).on('click', '.edit-button', function() {
        const index = $(this).data('index');
        const item = historialData[index];

        $('#nivel').val(item.nivel);
        $('#grado').val(item.grado);
        $('#curso').val(item.curso);
        $('#anio').val(item.anio);
        $('#establecimiento').val(item.establecimiento);

        historialData.splice(index, 1);
        updateTable();
        updateHiddenField();
    });

    $(document).on('click', '.delete-button', function() {
        const index = $(this).data('index');
        historialData.splice(index, 1);
        updateTable();
        updateHiddenField();
    });
});
</script>
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

document.getElementById('gestion_select').addEventListener('change', function () {
    fetchOptions('/get-niveles', { gestion_id: this.value }, 'nivel_select', 'nivel');
});

document.getElementById('nivel_select').addEventListener('change', function () {
    fetchOptions('/get-cursos', { nivel_id: this.value }, 'curso_select', 'curso');
});

document.getElementById('curso_select').addEventListener('change', function () {
    fetchOptions('/get-grados', { curso_id: this.value }, 'grado_select', 'nombre');
});

document.getElementById('curso_select').addEventListener('change', function () {
    const cursoId = this.value;
    const gradoId = document.getElementById('grado_select').value; // Suponiendo que tienes un elemento con id 'grado_select'

    if (cursoId && gradoId) {
        fetchOptions('/get-secciones', { curso_id: cursoId, grado_id: gradoId }, 'seccion_select', 'seccion');
    }
});

document.getElementById('grado_select').addEventListener('change', function () {
    const cursoId = document.getElementById('curso_select').value; // Suponiendo que tienes un elemento con id 'curso_select'
    const gradoId = this.value;

    if (cursoId && gradoId) {
        fetchOptions('/get-secciones', { curso_id: cursoId, grado_id: gradoId }, 'seccion_select', 'seccion');
    }
});


document.getElementById('seccion_select').addEventListener('change', function () {
    fetchOptions('/get-jornadas', { seccion_id: this.value }, 'jornada_select', 'nombre');
});



</script>

