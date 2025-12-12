<div class="row">
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Historial Académico</h1>
                <input type="hidden" name="familia_id" value="{{ $familia->id }}">
                <input type="hidden" name="estudiante_id" value="{{ $familia->estudiante->id }}">
               
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

                <button type="button" class="btn btn-primary" id="addButton2">Añadir</button>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    let historialData = [];

    // Rellenar tabla al cargar la página con los datos actuales de estudiante
    @foreach($estudiante->academicos as $academico)
    @php
        // Asegurándonos de que historial_data es un array válido
        $historiales = $academico->historial_data;
    @endphp

    @foreach($historiales as $historial)
        historialData.push({
            nivel: "{{ $historial['nivel'] }}",
            grado: "{{ $historial['grado'] }}",
            curso: "{{ $historial['curso'] }}",
            anio: "{{ $historial['anio'] }}",
            establecimiento: "{{ $historial['establecimiento'] }}"
        });
    @endforeach
@endforeach

    // Actualiza la tabla con los datos guardados
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

    // Actualiza el campo oculto con los datos de historial
    function updateHiddenField() {
        $('#historialAcademico').val(JSON.stringify(historialData));
    }

    // Manejo de añadir nuevo historial
    $('#addButton2').click(function() {
        const nivelId = $('#nivel').val();
        const gradoId = $('#grado').val();
        const cursoId = $('#curso').val();

        if (!nivelId || !gradoId || !cursoId || !$('#anio').val() || !$('#establecimiento').val()) {
            alert('Por favor complete todos los campos antes de añadir.');
            return;
        }

        // Crear objeto con los valores a guardar
        const nuevoRegistro = {
            nivel: nivelId, // Guardar el ID
            grado: gradoId, // Guardar el ID
            curso: cursoId, // Guardar el ID
            anio: $('#anio').val(),
            establecimiento: $('#establecimiento').val()
        };

        historialData.push(nuevoRegistro);
        updateTable();
        updateHiddenField();

        // Limpiar campos después de añadir
        $('#nivel').val('');
        $('#grado').val('');
        $('#curso').val('');
        $('#anio').val('');
        $('#establecimiento').val('');
    });

    // Manejo de edición
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

    // Manejo de eliminación
    $(document).on('click', '.delete-button', function() {
        const index = $(this).data('index');
        historialData.splice(index, 1);
        updateTable();
        updateHiddenField();
    });

    // Inicializa la tabla con los datos al cargar
    updateTable();
});
</script>
