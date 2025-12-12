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


@foreach ($familias as $codigoFamiliar => $familiaCollection)
@php
    $familia = $familiaCollection->first(); // Primera fila de la colección
    $estudiantesMostrados = [];
@endphp
<div class="box">
  <h2 class="card-title" style="color: #007bff; padding: 20px;">
    <strong>Código de Familia:</strong> {{ $familia->codigo_familiar ?? 'No disponible' }}
    &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <!-- Espacios en blanco -->
    <strong>Nombre Familiar:</strong> {{ $familia->nombre_familiar ?? 'No disponible' }}
</h2>

    <div class="box-header with-border">
      <div class="row">
        <!-- Aquí puede ir más contenido si es necesario -->
      </div>
    </div>

    <div class="box-body">
      <div class="row">
        <!-- Card para la primera sección -->
        <div class="col-sm-4">
            <div class="card" style="border: 2px solid #007bff; border-radius: 8px;">
                <div class="card-body" style="padding: 20px;">
                    <h2 class="card-title" style="color: #007bff;">Padre</h2>
                    <p><strong>Nombre:</strong> {{ $familia->padre->nombres ?? 'No disponible' }}</p>
                    <p><strong>Apellido:</strong> {{ $familia->padre->apellidos ?? 'No disponible' }}</p>
                    <p><strong>Estado Civil:</strong> {{ $familia->padre->estado_civil ?? 'No disponible' }}</p>
                    <p><strong>Tipo de Identificación:</strong> {{ $familia->padre->identificacionDocumento->nombre ?? 'No disponible' }}</p>
                    <p><strong>Profesión:</strong> {{ $familia->padre->profesion ?? 'No disponible' }}</p>
                    <p><strong>Teléfono:</strong> {{ $familia->padre->telefono ?? 'No disponible' }}</p>
                    <p><strong>Dirección:</strong> {{ $familia->padre->direccion ?? 'No disponible' }}</p>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-id="{{ $familia->padre->id }}" data-type="padre">
                        Editar
                    </button>
                </div>
            </div>
        </div>

        <!-- Card para la segunda sección -->
        <div class="col-sm-4">
            <div class="card" style="border: 2px solid #007bff; border-radius: 8px;">
                <div class="card-body" style="padding: 20px;">
                    <h2 class="card-title" style="color: #007bff;">Madre</h2>
                    <p><strong>Nombre:</strong> {{ $familia->madre->nombres ?? 'No disponible' }}</p>
                    <p><strong>Apellido:</strong> {{ $familia->madre->apellidos ?? 'No disponible' }}</p>
                    <p><strong>Estado Civil:</strong> {{ $familia->madre->estado_civil ?? 'No disponible' }}</p>
                    <p><strong>Tipo de Identificación:</strong> {{ $familia->madre->identificacionDocumento->nombre ?? 'No disponible' }}</p>
                    <p><strong>Profesión:</strong> {{ $familia->madre->profesion ?? 'No disponible' }}</p>
                    <p><strong>Teléfono:</strong> {{ $familia->madre->telefono ?? 'No disponible' }}</p>
                    <p><strong>Dirección:</strong> {{ $familia->madre->direccion ?? 'No disponible' }}</p>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalMadre" data-id="{{ $familia->madre->id }}" data-type="madre">
                        Editar
                    </button>
                </div>
            </div>
        </div>

        <!-- Card para la tercera sección -->
        <div class="col-sm-4">
            <div class="card" style="border: 2px solid #007bff; border-radius: 8px;">
                <div class="card-body" style="padding: 20px;">
                    <h2 class="card-title" style="color: #007bff;">Encargado</h2>
                    <p><strong>Nombre:</strong> {{ $familia->encargado->nombres ?? 'No disponible' }}</p>
                    <p><strong>Apellido:</strong> {{ $familia->encargado->apellidos ?? 'No disponible' }}</p>
                    <p><strong>Estado Civil:</strong> {{ $familia->encargado->estado_civil ?? 'No disponible' }}</p>
                    <p><strong>Tipo de Identificación:</strong> {{ $familia->encargado->identificacionDocumento->nombre ?? 'No disponible' }}</p>
                    <p><strong>Profesión:</strong> {{ $familia->encargado->profesion ?? 'No disponible' }}</p>
                    <p><strong>Teléfono:</strong> {{ $familia->encargado->telefono ?? 'No disponible' }}</p>
                    <p><strong>Dirección:</strong> {{ $familia->encargado->direccion ?? 'No disponible' }}</p>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalEncargado" data-id="{{ $familia->id }}" data-type="encargado">
                        Editar
                    </button>
                </div>
            </div>
        </div>
      </div>

     <!-- Estudiantes -->
<div class="row">
  <h2 class="card-title text-center" style="color: #007bff;">Estudiantes Inscritos</h2>
  @php
      $estudiantes = collect(); // Inicializa una colección vacía
      foreach ($familiaCollection as $familia) {
          $estudiantes = $estudiantes->merge($familia->estudiantes2); // Une los estudiantes de cada familia
      }
  @endphp

  @foreach ($estudiantes as $estudiante)





  <div class="col-sm-3">
      <div class="card" name="card_{{$estudiante->id}}" style="border: 2px solid #007bff; border-radius: 8px;">
          <div class="card-body" style="padding: 20px;">
              <h4 class="card-title text-center" style="color: #007bff;">


                  {{ $estudiante->persona->nombres ?? 'No disponible' }}
                  {{ $estudiante->persona->apellidos ?? 'No disponible' }}
              </h4>
              <img src="{{ asset('storage/'.$estudiante->fotografia_estudiante) }}" alt="Imagen del estudiante" style="width: 5cm; height: 4cm;" class="img-fluid" />
              <p><strong>Carnet:</strong> {{ $estudiante->carnet ?? 'No disponible' }}</p>
              <p><strong>No. Identificación:</strong> {{ $estudiante->persona->num_documento ?? 'No disponible' }}</p>
              <p><strong>Curso:</strong> {{ $estudiante->asignacion->cursos->curso ?? 'No disponible' }}</p>
              <p><strong>Grado:</strong> {{ $estudiante->asignacion->grados->nombre ?? 'No disponible' }}</p>
              <p><strong>Sección:</strong> {{ $estudiante->asignacion->secciones->seccion ?? 'No disponible' }}</p>
              <p><strong>Jornada:</strong> {{ $estudiante->asignacion->jornadas->jornada->nombre ?? 'No disponible' }}</p>
              <p><strong>Horario:</strong> {{ $estudiante->asignacion->jornadas->horario->inicio ?? 'No disponible' }} - {{ $estudiante->asignacion->jornadas->horario->fin ?? 'No disponible' }}</p>

              <button type="button" class="btn btn-primary open-modal"
                data-toggle="modal"
                data-target="#modalReutilizable"
                data-idestudiante="{{ $estudiante->id }}"
                data-fotografia="{{ asset('storage/'.$estudiante->fotografia_estudiante) }}"
                data-personaidestudiante ="{{ $estudiante->persona->id }}"
                data-nombre="{{ $estudiante->persona->nombres }}"
                data-apellido="{{ $estudiante->persona->apellidos }}"
                data-carnet="{{ $estudiante->carnet }}"
                 data-genero="{{ $estudiante->persona->genero }}"
                 data-ididocumentacion="{{ $estudiante->persona->identificacion_documentos_id }}"
                data-numdocumento="{{ $estudiante->persona->num_documento }}"
                 data-fnacimiento="{{ $estudiante->persona->fecha_nacimiento }}"
                  data-email="{{ $estudiante->persona->email }}"
                   data-telefono="{{ $estudiante->persona->telefono}}"
                    data-direccion="{{ $estudiante->persona->direccion }}"
                 data-tipo="estudiante">
                <i class="bi bi-pencil-square"></i>
            </button>

             <!-- MEDICO-->


             @foreach ($estudiante->medicos as $medico)

            <button type="button" class="btn btn-primary open-modal"
            data-toggle="modal"
            data-target="#modalMedico"
            data-idmedico="{{ $medico->id }}"
            data-personaidmedico ="{{ $estudiante->persona->id }}"
            data-gruposanguineo ="{{ $medico->grupo_sanguineo }}"
            data-alergias="{{ $medico->alergias }}"
             data-enfermedades="{{ $medico->enfermedades }}"
            data-medicamentos="{{ $medico->medicamentos }}"
            data-medico="{{ $medico->medico }}"
             data-telefonodoctor="{{ $medico->telefono_medico}}"
             data-observaciones="{{ $medico->observacion }}"
             data-tipo="medico">
             <i class="bi bi-heart-pulse-fill"></i>
        </button>
        @endforeach





        @if ($estudiante->academicos->isNotEmpty())
    @foreach ($estudiante->academicos as $academico)
        @php
            // Convertimos historial_data a un array si es un JSON
            $historiales = is_string($academico->historial_data)
                ? json_decode($academico->historial_data, true)
                : $academico->historial_data;
        @endphp

        @if (!empty($historiales) && is_array($historiales))

                <button type="button" class="btn btn-primary open-modal"
                    data-toggle="modal"
                    data-target="#modalAcademico"
                    data-historialesa='{{ json_encode($historiales) }}'
                    data-idacademico="{{ $academico->id }}"
                    data-estudianteidacademico="{{ $estudiante->id }}"
                    data-tipo="academico">
                     <i class="bi bi-clock-history"></i>
                </button>

        @endif
    @endforeach
@endif






          </div>
      </div>
  </div>
  @endforeach


</div>
</div>
        <div class="col-sm-3">
            <div class="card" style="border: 2px solid #007bff; border-radius: 8px;">
                <div class="card-body" style="padding: 20px;">
                    <h4 class="card-title text-center" style="color: #007bff;">Añadir Nuevo Estudiante</h4>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('familias.create2', $familia->id)}}" class="btn btn-primary" title="Añadir">
                            <i class="bi bi-person-plus"></i> Añadir
                        </a>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>
</div>
@endforeach
 <!-- Modal Padre-->
 <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edición de Datos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editForm" method="POST" action="{{ route('familias.update', $familia->padre ? $familia->padre->id : $familia->id) }}">

          @csrf
          @method('PUT')


          @include('familias.forms.forms_persona', ['prefijo' => 'padre'])

          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

      </div>
    </div>
  </div>
</div>


  <!-- Modal Madre-->
<div class="modal fade" id="exampleModalMadre" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edición de Datos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editForm" method="POST" action="{{ route('familias.update', $familia->madre ? $familia->madre->id : $familia->id) }}">
          @csrf
          @method('PUT')


          @include('familias.forms.forms_persona', ['prefijo' => 'madre'])

          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

      </div>
    </div>
  </div>
</div>


  <!-- Modal Encargado-->
<div class="modal fade" id="exampleModalEncargado" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalLabel">Edición de Datos</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      @if(isset($familia))
      <form id="editForm" method="POST" action="{{ route('familias.update', $familia->id) }}">
      @method('PUT')


        @include('familias.forms.forms_persona', ['prefijo' => 'encargado'])

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
    @endif
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerar</button>

    </div>
  </div>
</div>
</div>

  <!-- Modal Estudiantes-->
<div class="modal fade" id="modalReutilizable" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalTitle">Editar Información</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <form id="modalForm" method="POST" action="{{ route('familias.editarestudiante') }}" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  <input type="hidden" id="modalIdEstudiante" name="estudianteidestudiante">
                  <input type="hidden" id="modalPersonaIdEstudiante" name="idPersonaestudiante">
                  @include('familias.forms.forms_estudiante', ['prefijo' => 'estudiante'])
                  <button type="submit" class="btn btn-primary">Guardar cambios</button>
              </form>
          </div>
      </div>
  </div>
</div>

  <!-- Modal Medico-->
<div class="modal fade" id="modalMedico" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalTitle">Editar Información</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <form id="modalForm" method="POST" action="{{ route('familias.editarmedico') }}">
                  @csrf
                  @method('PUT')

                  <input type="hidden" id="modalIdMedico" name="estudianteidmedico">
                  <input type="hidden" id="modalPersonaIdMedico" name="idPersonamedico">
                  @include('familias.forms.forms_medico', ['prefijo' => 'estudiante'])
                  <button type="submit" class="btn btn-primary">Guardar cambios</button>
              </form>
          </div>
      </div>
  </div>
</div>



 <!-- Modal Academico-->
 <div class="modal fade" id="modalAcademico" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalTitle">Historial Academico</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" >
              <form id="modalForm"  method="POST" action="{{ route('familias.editar-academico') }}">
                  @csrf
                  @method('PUT')

                  <input type="hidden" id="modalIdAcademico" name="estudianteidacademico">
                  <input type="hidden" id="modalPersonaIdAcademico" name="idPersonaacademico">
                  @include('familias.partials._form_academico', ['prefijo' => 'estudiante'])
                  <button type="submit" class="btn btn-primary">Guardar cambios</button>
              </form>
          </div>
      </div>
  </div>
</div>










<script>

// Función común para todos los modales
function configureModal(modalId, button) {
  var familiaId = button.data('id'); // Extrae el ID desde un atributo data
  var actionUrl = "{{ route('familias.update', ':id') }}".replace(':id', familiaId);
  var modal = $(modalId);

  // Actualizar el formulario con la nueva acción
  modal.find('#editForm').attr('action', actionUrl);

  // Aquí puedes usar un switch o if para cargar los campos correspondientes según el tipo
  var tipo = button.data('tipo'); // Extrae el tipo desde un atributo data

  switch (tipo) {
      case 'padre':
          // Configurar campos para famili
          break;
      case 'madre':
          // Configurar campos para madre
          break;
      case 'encargado':
          // Configurar campos para encargado
          break;

          case 'estudiante':
          // Configurar campos para encargado
          break;

          case 'medico':
          // Configurar campos para encargado
          break;

          case 'academico':
          // Configurar campos para encargado
          break;
      default:
          // Si no hay tipo especificado, se puede manejar aquí si es necesario
          break;
  }
}

// Asociar la función a los eventos de mostrar los modales
$('#exampleModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Botón que disparó el modal
  configureModal('#exampleModal', button);
});

$('#exampleModalMadre').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Botón que disparó el modal
  configureModal('#exampleModalMadre', button);
});

$('#exampleModalEncargado').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Botón que disparó el modal
  configureModal('#exampleModalEncargado', button);
});

$('#exampleModalEstudiante').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Botón que disparó el modal
  configureModal('#exampleModalEstudiante', button);
});

$('#exampleModalMedico').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Botón que disparó el modal
  configureModal('#exampleModalMedico', button);
});
$('#exampleModalAcademico').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Botón que disparó el modal
  configureModal('#exampleModalMedico', button);
});

</script>

<script>
  $(document).on("click", ".open-modal", function () {
      let idestudiante = $(this).data("idestudiante");
      let fotografia = $(this).data("fotografia");

      let personaidestudiante = $(this).data("personaidestudiante");
      let idmedico = $(this).data("idmedico");
      let personaidmedico = $(this).data("personaidmedico");
      let nombre = $(this).data("nombre");
      let apellido = $(this).data("apellido");
      let carnet = $(this).data("carnet") || "";
      let genero = $(this).data("genero");
      let ididocumentacion = $(this).data("ididocumentacion");
      let numdocumento = $(this).data("numdocumento");
      let email = $(this).data("email");
      let telefono = $(this).data("telefono");
      let direccion = $(this).data("direccion");

      // Extraer solo la fecha
       let fnacimiento = $(this).data("fnacimiento"); // "2017-08-23 00:00:00"

if (fnacimiento) {
    // Tomar solo la parte de la fecha
    fnacimiento = fnacimiento.split(' ')[0]; // "2017-08-23"
}
      let gruposanguineo = $(this).data("gruposanguineo");
      let alergias= $(this).data("alergias");
      let medicamentos = $(this).data("medicamentos");
      let medico = $(this).data("medico");
      let enfermedades = $(this).data("enfermedades");
      let telefonodoctor = $(this).data("telefonodoctor");
      let observaciones = $(this).data("observaciones");
      let tipo = $(this).data("tipo");


      let idacademico = $(this).data("idacademico");
      let estudianteidacademico = $(this).data("estudianteidacademico");
      let nivelidAcademico  = $(this).data("nivelidacademico");
      let gradoid  = $(this).data("gradoid");
      let cursoid  = $(this).data("cursoid");
      let anio  = $(this).data("anio");
      let establecimiento  = $(this).data("establecimiento");
      let historialesa  = $(this).data("historialesa");
      let idnuevoestudiante  = $(this).data("nuevoestudianteidacademico");
      // Verifica si es un estudiante
      if (tipo === "estudiante") {
          $("#modalTitle").text("Editar Estudiante");
          let actionUrl = `/admin/familias/estudiante/${idestudiante}`;
          $("#editFormEstudiante").attr("action", actionUrl);
          // Carga los datos en el modal
          $("#modalIdEstudiante").val(idestudiante);
          $("#modalfotografia").attr("src", fotografia);
          $("#modalNombre").val(nombre);
          $("#modalApellido").val(apellido);
          $("#modalCarnet").val(carnet);
          $("#modalGenero").val(genero);
          $("#modalIdidocumentacion").val(ididocumentacion);
          $("#modalNumdocumento").val(numdocumento);
          $("#modalFnacimientol").val(fnacimiento);
           calcularEdad('modalFnacimientol', 'anios');
          $("#modalEmail").val(email);
          $("#modalTelefono").val(telefono);
          $("#modalDireccion").val(direccion);
          $("#modalPersonaIdEstudiante").val(personaidestudiante);
      }
      else if (tipo === "medico") {
          $("#modalTitle").text("Editar Historial Medico");
          // Carga los datos en el modal
          $("#modalIdMedico").val(idmedico);
          $("#modalPersonaIdMedico").val(personaidmedico);
          $("#modalGrupoSanguineo").val(gruposanguineo);
          $("#modalAlergias").val(alergias);
          $("#modalEnfermedades").val(enfermedades);
          $("#modalMedicamentos").val(medicamentos);
          $("#modalDoctor").val(medico);
          $("#modalTelefonoMedico").val(telefonodoctor);
          $("#modalObservaciones").val(observaciones);

      }

  });





</script>


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

      // Abrir modal con datos según el tipo
      $(document).on("click", ".open-modal", function () {
          let tipo = $(this).data("tipo");

          if (tipo === "academico") {
              $("#modalTitle").text("Editar Historial Académico");

              let idacademico = $(this).data("idacademico");
              let estudianteidacademico = $(this).data("estudianteidacademico");
              let historialesa = $(this).data("historialesa");

              $("#modalIdAcademico").val(idacademico);
              $("#modalPersonaIdAcademico").val(estudianteidacademico);

              // Verificar si hay datos en historialesa
              historialData = [];
              if (historialesa) {
                  try {
                      historialData = typeof historialesa === "string" ? JSON.parse(historialesa) : historialesa;
                  } catch (error) {
                      console.error("Error al procesar historial académico:", error);
                      historialData = [];
                  }
              }

              updateTable();
              updateHiddenField();
          }
      });

      // Agregar nuevo historial académico
      $('#addButton').click(function() {
          const nivelId = $('#nivel').val();
          const gradoId = $('#grado').val();
          const cursoId = $('#curso').val();
          const anio = $('#anio').val();
          const establecimiento = $('#establecimiento').val();

          if (!nivelId || !gradoId || !cursoId || !anio || !establecimiento) {
              alert('Por favor complete todos los campos antes de añadir.');
              return;
          }

          historialData.push({ nivel: nivelId, grado: gradoId, curso: cursoId, anio, establecimiento });

          updateTable();
          updateHiddenField();

          $('#nivel').val('');
          $('#grado').val('');
          $('#curso').val('');
          $('#anio').val('');
          $('#establecimiento').val('');
      });

      // Eliminar historial académico
      $(document).on("click", ".delete-button", function () {
          let index = $(this).data("index");
          historialData.splice(index, 1);
          updateTable();
          updateHiddenField();
      });

      // Editar historial académico
      $(document).on("click", ".edit-button", function () {
          let index = $(this).data("index");
          let item = historialData[index];

          $('#nivel').val(item.nivel);
          $('#grado').val(item.grado);
          $('#curso').val(item.curso);
          $('#anio').val(item.anio);
          $('#establecimiento').val(item.establecimiento);

          historialData.splice(index, 1);
          updateTable();
          updateHiddenField();
      });
  });

  </script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
  document.addEventListener('DOMContentLoaded', function() {
      @if(session('success'))
          Swal.fire({
              title: "Éxito",
              text: "{{ session('success') }}",
              icon: "success",
              confirmButtonText: "Aceptar"
          });
      @endif
  });
</script>



@endsection


