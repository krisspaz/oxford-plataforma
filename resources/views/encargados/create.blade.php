@extends('crudbooster::admin_template')

@section('title', 'Encargados')

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
    <h1>Crear Encargado</h1>

    <form action="{{ route('encargados.store') }}" method="POST">
        @csrf
        @include('encargados.form')
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
   

    @parent
    <script>
        $(document).ready(function() {
            $('#departamento_id').change(function() {
                var departamento_id = $(this).val();
                if(departamento_id) {
                    $.ajax({
                        url: '/encargados/get-municipios/'+departamento_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#municipio_id').empty();
                            $('#municipio_id').append('<option value="">Seleccione un Municipio</option>');
                            $.each(data, function(key, value) {
                                $('#municipio_id').append('<option value="'+ value.id +'">'+ value.municipio +'</option>');
                            });
                        }
                    });
                } else {
                    $('#municipio_id').empty();
                }
            });
        });
    </script>
@endsection
