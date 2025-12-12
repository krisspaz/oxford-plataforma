@extends('crudbooster::admin_template')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Filtrar Pagos</h3>
    </div>
    <div class="box-body">
        <form action="{{ route('pagos.reporte') }}" method="GET">
            <div class="form-group">
                <label for="criterio">Buscar por:</label>
                <select name="criterio" id="criterio" class="form-control" required>
                    <option value="todo">Buscar en todo (Filtre por fecha)</option>
                    <option value="carne">Carné</option>
                    <option value="nombre_completo">Nombre completo</option>
                </select>
            </div>

            <div class="form-group" id="campo-carne" style="display: none;">
                <label for="carne">Carné:</label>
                <input type="text" name="carne" id="carne" class="form-control">
            </div>

            <div class="form-group" id="campo-nombre" style="display: none;">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" name="nombre" id="nombre" class="form-control">
            </div>

            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Fecha Final:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
            </div>

              <!-- Nuevo select para el tamaño de hoja -->
              <div class="form-group col-md-6">
                <label for="tamano_hoja">Tamaño de hoja:</label>
                <select name="tamano_hoja" id="tamano_hoja" class="form-control">
                    <option value="A4" selected>A4</option>
                    <option value="Letter">Carta</option>
                    <option value="Legal">Oficio</option>
                </select>
            </div>

            <!-- Nuevo select para la orientación -->
            <div class="form-group col-md-6">
                <label for="orientacion">Orientación:</label>
                <select name="orientacion" id="orientacion" class="form-control">
                    <option value="portrait">Vertical</option>
                    <option value="landscape">Horizontal</option>
                </select>
            </div>
        </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-file-pdf-o"></i> Generar Reporte
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('criterio').addEventListener('change', function () {
    let criterio = this.value;
    document.getElementById('campo-carne').style.display = (criterio === 'carne') ? 'block' : 'none';
    document.getElementById('campo-nombre').style.display = (criterio === 'nombre_completo') ? 'block' : 'none';
});
</script>
@endsection
