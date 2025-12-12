@extends('crudbooster::admin_template')

@section('content')
<div class="box">
    <h1>Generar Recibo</h1>
    <form action="{{ route('factura.recibo') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nit" class="form-label">NIT</label>
            <input type="text" class="form-control" id="nit" name="nit"  value="" 
            placeholder="Ingrese el NIT del receptor" required>
            <button type="button" class="btn btn-info" id="buscar-btn">Buscar NIT</button>
        </div>
        <div class="mb-3">
            <label for="cliente" class="form-label">Cliente</label>
            <input type="text" class="form-control" id="cliente" name="cliente" value="" 
            placeholder="Ingrese el nombre del cliente" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="" required>
        </div>

       <!-- Campo oculto para almacenar los ítems -->
       <input type="hidden" id="items" name="items">

       <h3>Items</h3>
       <table class="table table-bordered" id="items-table">
           <thead>
               <tr>
                
                  
                   <th>Cantidad</th>
                 
                   <th>Descripción</th>
                   <th>Precio Unitario</th>
                   <th>Precio</th>
                   <th>Descuento</th>
             
                   <th>Acciones</th>
               </tr>
           </thead>
           <tbody>
               <!-- Las filas se agregarán dinámicamente aquí -->
           </tbody>
       </table>

       <button type="button" class="btn btn-primary" id="add-item">Agregar Ítem</button>

       <div class="mt-3">
           <h4>Total: <span id="total">0.00</span></h4>
           <button type="submit" class="btn btn-success">Generar Recibo</button>
       </div>
   </form>
</div>

<script>
   document.addEventListener('DOMContentLoaded', () => {
       const itemsTable = document.getElementById('items-table').querySelector('tbody');
       const itemsInput = document.getElementById('items');
       const addItemButton = document.getElementById('add-item');
       const totalDisplay = document.getElementById('total');

       const calculateRow = (row) => {
           const cantidad = parseInt(row.querySelector('.cantidad').value) || 0;
           const precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
           const descuento = parseFloat(row.querySelector('.descuento').value) || 0;

           const precio = (cantidad * precioUnitario) - descuento;
           const montoGravable = precio / 1.12;
           const montoImpuesto = precio - montoGravable;

           row.querySelector('.precio').value = precio.toFixed(3);
           row.querySelector('.monto-gravable').value = montoGravable.toFixed(3);
           row.querySelector('.monto-impuesto').value = montoImpuesto.toFixed(3);
       };

       const updateItems = () => {
    const rows = itemsTable.querySelectorAll('tr');
    let total = 0;
    const items = Array.from(rows).map((row, index) => {
        calculateRow(row);
        const precio = parseFloat(row.querySelector('.precio').value) || 0;
        total += precio;

        return {
            BienOServicio: 'S',
            NumeroLinea: index + 1,
            Cantidad: parseFloat(row.querySelector('.cantidad').value) || 1.000,
            UnidadMedida: 'UN',
            Descripcion: row.querySelector('.descripcion').value,
            PrecioUnitario: parseFloat(row.querySelector('.precio-unitario').value) || 0.000,
            Precio: precio.toFixed(3),
            Descuento: parseFloat(row.querySelector('.descuento').value) || 0.000,
            MontoGravable: parseFloat(row.querySelector('.monto-gravable').value) || 0.000,
            MontoImpuesto: parseFloat(row.querySelector('.monto-impuesto').value) || 0.000,
            Total: precio.toFixed(3) // Incluye el total aquí
        };
    });
    totalDisplay.textContent = total.toFixed(2);
    itemsInput.value = JSON.stringify(items);
};

       addItemButton.addEventListener('click', () => {
           const rowCount = itemsTable.querySelectorAll('tr').length + 1;
           const newRow = document.createElement('tr');
           newRow.innerHTML = `
             
             
               <td><input type="number" class="form-control cantidad" value="1" step="0" required></td>
               
                
                 
               
               <td><input type="text" class="form-control descripcion" placeholder="Descripción" required></td>
               <td><input type="number" class="form-control precio-unitario" value="0.000" step="0.001" required></td>
               <td><input type="number" class="form-control precio" value="0.000" step="0.001" readonly></td>
               <td><input type="number" class="form-control descuento" value="0.000" step="0.001" required></td>
               <td><input type="hidden" class="form-control monto-gravable" value="0.000" step="0.001" readonly></td>
               <td><input type="hidden" class="form-control monto-impuesto" value="0.000" step="0.001" readonly></td>
               <td><button type="button" class="btn btn-danger btn-remove">Eliminar</button></td>
           `;
           itemsTable.appendChild(newRow);
           updateItems();
       });

       itemsTable.addEventListener('click', (event) => {
           if (event.target.classList.contains('btn-remove')) {
               event.target.closest('tr').remove();
               updateItems();
           }
       });

       itemsTable.addEventListener('input', (event) => {
           if (event.target.closest('tr')) {
               updateItems();
           }
       });
   });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nitInput = document.getElementById('nit');
        const clienteInput = document.getElementById('cliente');
        const buscarBtn = document.getElementById('buscar-btn');
 
        // Función para buscar el nombre del cliente usando el NIT
        const buscarCliente = async () => {
            const nit = nitInput.value.trim(); // Eliminar espacios en blanco
 
            if (nit.length > 0) {
                try {
                    const response = await fetch(`/consultar-nit/${nit}`);
                    const data = await response.json();
 
                    if (data.success) {
                        clienteInput.value = data.nombre; // Asignar nombre al campo cliente
                    } else {
                        clienteInput.value = ''; // Limpiar si el NIT no se encuentra
                        alert("NIT no encontrado");
                    }
                } catch (error) {
                    console.error("Error al consultar el NIT:", error);
                    alert("Hubo un error al consultar el NIT. Intente nuevamente.");
                    clienteInput.value = '';
                }
            } else {
                alert("Por favor ingrese un NIT válido.");
            }
        };
 
        // Evento del botón "Buscar NIT"
        buscarBtn.addEventListener('click', buscarCliente);
    });
 </script>
@endsection