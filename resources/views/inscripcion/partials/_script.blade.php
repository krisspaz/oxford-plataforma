<script>
    $(document).ready(function() {
        $('#departamento_id').change(function() {
            var departamento_id = $(this).val();
            if(departamento_id) {
                $.ajax({
                    url: '/inscripcion/get-municipios/'+departamento_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#padre_municipio_id').empty();
                        $('#padre_municipio_id').append('<option value="">Seleccione un Municipio</option>');
                        $.each(data, function(key, value) {
                            $('#padre_municipio_id').append('<option value="'+ value.id +'">'+ value.municipio +'</option>');
                        });
                    }
                });
            } else {
                $('#padre_municipio_id').empty();
            }
        });
    });



    $(document).ready(function() {
        $('#departamento_id2').change(function() {
            var departamento_id = $(this).val();
            if(departamento_id) {
                $.ajax({
                    url: '/inscripcion/get-municipios/'+departamento_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#madre_municipio_id').empty();
                        $('#madre_municipio_id').append('<option value="">Seleccione un Municipio</option>');
                        $.each(data, function(key, value) {
                            $('#madre_municipio_id').append('<option value="'+ value.id +'">'+ value.municipio +'</option>');
                        });
                    }
                });
            } else {
                $('#madre_municipio_id').empty();
            }
        });
    });



    $(document).ready(function() {
        $('#departamento_id3').change(function() {
            var departamento_id = $(this).val();
            if(departamento_id) {
                $.ajax({
                    url: '/inscripcion/get-municipios/'+departamento_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#encargado_municipio_id').empty();
                        $('#encargado_municipio_id').append('<option value="">Seleccione un Municipio</option>');
                        $.each(data, function(key, value) {
                            $('#encargado_municipio_id').append('<option value="'+ value.id +'">'+ value.municipio +'</option>');
                        });
                    }
                });
            } else {
                $('#encargado_municipio_id').empty();
            }
        });
    });



    $(document).ready(function() {
        $('#departamento_id4').change(function() {
            var departamento_id = $(this).val();
            if(departamento_id) {
                $.ajax({
                    url: '/inscripcion/get-municipios/'+departamento_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#alumno_municipio_id').empty();
                        $('#alumno_municipio_id').append('<option value="">Seleccione un Municipio</option>');
                        $.each(data, function(key, value) {
                            $('#alumno_municipio_id').append('<option value="'+ value.id +'">'+ value.municipio +'</option>');
                        });
                    }
                });
            } else {
                $('#alumno_municipio_id').empty();
            }
        });
    });

    //fechas de nacimiento y edad

    document.addEventListener('DOMContentLoaded', function() {
        var fechaNacimientoInput = document.getElementById('padre_fecha_nacimiento');
        
        if (fechaNacimientoInput) {  // Verificación adicional
            fechaNacimientoInput.addEventListener('input', function() {
                var fechaNacimiento = new Date(this.value);
                var hoy = new Date();
                var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
                var mes = hoy.getMonth() - fechaNacimiento.getMonth();
                
                if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                    edad--;
                }

                document.getElementById('padre_edad').value = edad >= 0 ? edad : '';
            });
        } else {
            console.error('Elemento con ID padre_fecha_nacimiento no encontrado.');
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        var fechaNacimientoInput = document.getElementById('madre_fecha_nacimiento');
        
        if (fechaNacimientoInput) {  // Verificación adicional
            fechaNacimientoInput.addEventListener('input', function() {
                var fechaNacimiento = new Date(this.value);
                var hoy = new Date();
                var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
                var mes = hoy.getMonth() - fechaNacimiento.getMonth();
                
                if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                    edad--;
                }

                document.getElementById('madre_edad').value = edad >= 0 ? edad : '';
            });
        } else {
            console.error('Elemento con ID padre_fecha_nacimiento no encontrado.');
        }
    });

    function previewImage(input, previewId) {
    const imagePreview = document.getElementById(previewId);

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        imagePreview.src = '';
        imagePreview.style.display = 'none';
    }
}


    
</script>