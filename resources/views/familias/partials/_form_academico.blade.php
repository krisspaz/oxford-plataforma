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
                                <option value="{{ $nivel->nivel }}">{{ $nivel->nivel }}</option>
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
                                <option value="{{ $grado->nombre }}">{{ $grado->nombre }}</option>
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
                                <option value="{{ $ncurso->curso }}">{{ $ncurso->curso }}</option>
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



    <input type="hidden" name="familia_id" value="{{ $familia->id }}">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>




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

