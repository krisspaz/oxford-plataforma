<div class="modal fade" id="modalPersona" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Editar Persona</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="familia_id" value="{{ $familia_id }}">
                    <input type="hidden" name="prefijo" value="">
                    @include('familias.forms.forms_persona', ['prefijo' => ''])
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
    $('#modalPersona').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Botón que activó el modal
        var id = button.data('id');
        var tipo = button.data('tipo'); // Captura el 'tipo' de la data-attribute
        var modal = $(this);
        modal.find('form').attr('action', '/familias/update/' + id);
        modal.find('input[name="prefijo"]').val(tipo);
    });
});
</script>
