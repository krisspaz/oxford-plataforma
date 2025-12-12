<!DOCTYPE html>
<html>
<head>
    <title>Crear Nueva Factura</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Crear Nueva Factura</h1>

        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf

            <div class="mb-3">
                <label for="nit">NIT del Receptor:</label>
                <input type="text" name="nit" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="nombre">Nombre del Receptor:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <table class="table" id="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bien o Servicio</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="item-row-1">
                        <td>1</td>
                        <td><input type="text" name="items[0][bienOServicio]" class="form-control" required></td>
                        <td><input type="number" name="items[0][cantidad]" class="form-control" required></td>
                        <td><input type="number" name="items[0][precioUnitario]" class="form-control" required></td>
                        <td class="subtotal"></td>
                        <td><button type="button" class="btn btn-danger remove-item">Eliminar</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total:</strong></td>
                        <td id="total"></td>
                    </tr>
                </tfoot>
            </table>

            <button type="button" class="btn btn-primary" id="add-item">Agregar Item</button>
            <button type="submit" class="btn btn-primary">Crear Factura</button>
           
        </form>

        @if ($invoice)
    <a href="{{ route('download-xml', $invoice->id) }}" class="btn btn-primary">Descargar XML</a>
@endif


   


        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Función para calcular el subtotal de una fila
                function calcularSubtotal(row) {
                    const cantidad = parseFloat(row.find('input[name^="items"]').eq(1).val());
                    const precioUnitario = parseFloat(row.find('input[name^="items"]').eq(2).val());
                    const subtotal = cantidad * precioUnitario;
                    row.find('.subtotal').text(subtotal.toFixed(2));
                }

                // Función para calcular el total
                function calcularTotal() {
                    let total = 0;
                    $('#items-table tbody tr').each(function() {
                        total += parseFloat($(this).find('.subtotal').text());
                    });
                    $('#total').text(total.toFixed(2));
                }

                // Evento para calcular subtotales al cambiar la cantidad o el precio unitario
                $('#items-table').on('input', 'input[name^="items"]', function() {
                    const row = $(this).closest('tr');
                    calcularSubtotal(row);
                    calcularTotal();
                });

                // Evento para agregar una nueva fila
                $('#add-item').click(function() {
                    const rowCount = $('#items-table tbody tr').length;
                    const newRow = $('#item-row-1').clone();
                    newRow.attr('id', 'item-row-' + (rowCount + 1));
                    newRow.find('input').val('');
                    $('#items-table tbody').append(newRow);
                    calcularTotal();
                });

                // Evento para eliminar una fila
                $('#items-table').on('click', '.remove-item', function() {
                    $(this).closest('tr').remove();
                    calcularTotal();
                });

                // Calcular el total inicial
                calcularTotal();
            });
        </script>

        <script>


            </script>
    </div>
</body>
</html>