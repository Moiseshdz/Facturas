<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Consultas para la sección de reportes
$total_facturas_query = "SELECT COUNT(*) as total FROM facturas";
$total_clientes_query = "SELECT COUNT(*) as total FROM clientes";
$total_productos_query = "SELECT COUNT(*) as total FROM productos";

$total_facturas_result = $conn->query($total_facturas_query);
$total_clientes_result = $conn->query($total_clientes_query);
$total_productos_result = $conn->query($total_productos_query);

$total_facturas = $total_facturas_result ? $total_facturas_result->fetch_assoc()['total'] : 0;
$total_clientes = $total_clientes_result->fetch_assoc()['total'];
$total_productos = $total_productos_result->fetch_assoc()['total'];

// Obtener la lista de clientes
$clientes_query = "SELECT * FROM clientes ORDER BY nombre";
$clientes_result = $conn->query($clientes_query);

// Obtener la lista de productos
$productos_query = "SELECT * FROM productos ORDER BY nombre";
$productos_result = $conn->query($productos_query);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>Sistema Web de Facturación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-blue-700 text-white shadow">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div
                    class="bg-white rounded-full w-10 h-10 flex items-center justify-center text-blue-700 font-bold text-2xl">
                    F</div>
                <h1 class="text-xl font-semibold">Sistema de Facturación</h1>
            </div>
            <nav class="hidden md:flex space-x-6 text-sm font-medium">
                <a class="hover:underline" href="#facturas">Facturas</a>
                <a class="hover:underline" href="#clientes">Clientes</a>
                <a class="hover:underline" href="#productos">Productos</a>
                <a class="hover:underline" href="#reportes">Reportes</a>
            </nav>
            <button aria-label="Abrir menú" class="md:hidden focus:outline-none focus:ring-2 focus:ring-white"
                id="menu-btn">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        <nav class="md:hidden bg-blue-600 text-white px-4 py-2 space-y-2 hidden" id="mobile-menu">
            <a class="block py-1 hover:underline" href="#facturas">Facturas</a>
            <a class="block py-1 hover:underline" href="#clientes">Clientes</a>
            <a class="block py-1 hover:underline" href="#productos">Productos</a>
            <a class="block py-1 hover:underline" href="#reportes">Reportes</a>
        </nav>
    </header>

    <!-- Barra de navegación secundaria -->
    <div class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3">
            <ul class="flex flex-wrap -mx-2 text-sm">
                <li class="px-2 py-1">
                    <a href="ver_facturas" class="flex items-center text-blue-600 hover:text-blue-800">
                        <i class="fas fa-file-invoice mr-1"></i> Ver Facturas
                    </a>
                </li>
                <li class="px-2 py-1">
                    <a href="ver_clientes" class="flex items-center text-blue-600 hover:text-blue-800">
                        <i class="fas fa-users mr-1"></i> Ver Clientes
                    </a>
                </li>
                <li class="px-2 py-1">
                    <a href="ver_productos" class="flex items-center text-blue-600 hover:text-blue-800">
                        <i class="fas fa-box mr-1"></i> Ver Productos
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <!-- Facturas Section -->
        <section class="mb-12" id="facturas">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Nueva Factura</h2>
                <a href="ver_facturas" class="text-blue-600 hover:underline">
                    <i class="fas fa-list mr-1"></i> Ver todas las facturas
                </a>
            </div>
            <div class="bg-white rounded shadow p-6">
                <form class="space-y-6" id="invoice-form" action="procesar_factura" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1" for="cliente">Cliente</label>
                            <select
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                id="cliente" name="cliente" required>
                                <option disabled selected value="">Seleccione un cliente</option>
                                <?php
                                if ($clientes_result && $clientes_result->num_rows > 0) {
                                    while ($cliente = $clientes_result->fetch_assoc()) {
                                        echo "<option value=\"{$cliente['id']}\">{$cliente['nombre']} - {$cliente['email']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1" for="fecha">Fecha</label>
                            <input
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                id="fecha" name="fecha" required type="date" value="<?php echo date('Y-m-d'); ?>" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Productos</label>
                        <div class="w-full overflow-x-auto">
                            <table class="w-full border border-gray-300 rounded">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border-b border-gray-300 px-3 py-2 text-left">Producto</th>
                                        <th class="border-b border-gray-300 px-3 py-2 text-left">Cantidad</th>
                                        <th class="border-b border-gray-300 px-3 py-2 text-left">Precio Unitario</th>
                                        <th class="border-b border-gray-300 px-3 py-2 text-left">Total</th>
                                        <th class="border-b border-gray-300 px-3 py-2 text-center">Acción</th>
                                    </tr>
                                </thead>

                                <tbody id="productos-list">
                                    <tr class="producto-row">
                                        <td class="border-b border-gray-300 px-3 py-2">
                                            <select
                                                class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                name="producto[]" required>
                                                <option disabled selected value="">Seleccione producto</option>
                                                <?php
                                                if ($productos_result && $productos_result->num_rows > 0) {
                                                    $productos_result->data_seek(0); // Reiniciar el puntero del resultado
                                                    while ($producto = $productos_result->fetch_assoc()) {
                                                        echo "<option value=\"{$producto['id']}\" data-precio=\"{$producto['precio']}\">{$producto['nombre']}</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="border-b border-gray-300 px-3 py-2">
                                            <input
                                                class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                min="1" name="cantidad[]" required type="number" value="1" />
                                        </td>
                                        <td class="border-b border-gray-300 px-3 py-2 precio-unitario">0.00</td>
                                        <td class="border-b border-gray-300 px-3 py-2 total-producto">0.00</td>
                                        <td class="border-b border-gray-300 px-3 py-2 text-center">
                                            <button aria-label="Eliminar producto"
                                                class="remove-producto text-red-600 hover:text-red-800 focus:outline-none"
                                                type="button">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button
                            class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            id="add-producto" type="button">
                            <i class="fas fa-plus mr-2"></i>Agregar Producto
                        </button>
                    </div>
                    <div class="text-right text-lg font-semibold text-gray-800">
                        Total: $<span id="total-factura">0.00</span>
                        <input type="hidden" name="total_factura" id="total_factura_input" value="0.00">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            class="px-4 py-2 border border-gray-400 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400"
                            type="reset">Limpiar</button>
                        <button
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                            type="submit">Guardar Factura</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Clientes Section -->
        <section class="mb-12" id="clientes">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Nuevo Cliente</h2>
                <a href="ver_clientes" class="text-blue-600 hover:underline">
                    <i class="fas fa-list mr-1"></i> Ver todos los clientes
                </a>
            </div>
            <div class="bg-white rounded shadow p-6">
                <form class="space-y-6 max-w-lg" id="cliente-form" action="procesar_cliente" method="POST">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="nombre-cliente">Nombre Completo</label>
                        <input
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            id="nombre-cliente" name="nombre-cliente" placeholder="Ej: Juan Pérez" required
                            type="text" />
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="email-cliente">Correo
                            Electrónico</label>
                        <input
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            id="email-cliente" name="email-cliente" placeholder="ejemplo@correo.com" required
                            type="email" />
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="telefono-cliente">Teléfono</label>
                        <input
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            id="telefono-cliente" name="telefono-cliente" placeholder="+34 600 000 000" type="tel" />
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            class="px-4 py-2 border border-gray-400 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400"
                            type="reset">Limpiar</button>
                        <button
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            type="submit">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Productos Section -->
        <section class="mb-12" id="productos">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Nuevo Producto</h2>
                <a href="ver_productos" class="text-blue-600 hover:underline">
                    <i class="fas fa-list mr-1"></i> Ver todos los productos
                </a>
            </div>
            <div class="bg-white rounded shadow p-6 max-w-lg">
                <form class="space-y-6" id="producto-form" action="procesar_producto" method="POST">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="nombre-producto">Nombre del
                            Producto</label>
                        <input
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            id="nombre-producto" name="nombre-producto" placeholder="Ej: Producto 1" required
                            type="text" />
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1" for="precio-producto">Precio
                            Unitario</label>
                        <input
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            id="precio-producto" min="0" name="precio-producto" placeholder="Ej: 100.00" required
                            step="0.01" type="number" />
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            class="px-4 py-2 border border-gray-400 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400"
                            type="reset">Limpiar</button>
                        <button
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            type="submit">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Reportes Section -->
        <section class="mb-12" id="reportes">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Reportes</h2>
            <div class="bg-white rounded shadow p-6">
                <p class="text-gray-700 mb-4">Aquí podrá ver reportes básicos de facturación, clientes y productos.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-100 rounded p-4 text-center">
                        <div class="bg-blue-200 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-file-invoice text-blue-700 text-3xl"></i>
                        </div>
                        <h3 class="font-semibold text-lg text-blue-800">Facturas Emitidas</h3>
                        <p class="text-3xl font-bold text-blue-900 mt-1" id="total-facturas">
                            <?php echo $total_facturas; ?></p>
                        <a href="ver_facturas.php" class="inline-block mt-2 text-blue-600 hover:underline text-sm">Ver
                            detalles</a>
                    </div>
                    <div class="bg-green-100 rounded p-4 text-center">
                        <div class="bg-green-200 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-users text-green-700 text-3xl"></i>
                        </div>
                        <h3 class="font-semibold text-lg text-green-800">Clientes Registrados</h3>
                        <p class="text-3xl font-bold text-green-900 mt-1" id="total-clientes">
                            <?php echo $total_clientes; ?></p>
                        <a href="ver_clientes.php" class="inline-block mt-2 text-green-600 hover:underline text-sm">Ver
                            detalles</a>
                    </div>
                    <div class="bg-yellow-100 rounded p-4 text-center">
                        <div class="bg-yellow-200 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-box text-yellow-700 text-3xl"></i>
                        </div>
                        <h3 class="font-semibold text-lg text-yellow-800">Productos Disponibles</h3>
                        <p class="text-3xl font-bold text-yellow-900 mt-1" id="total-productos">
                            <?php echo $total_productos; ?></p>
                        <a href="ver_productos.php"
                            class="inline-block mt-2 text-yellow-600 hover:underline text-sm">Ver detalles</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-blue-700 text-white py-4 text-center text-sm">
        © <?php echo date('Y'); ?> Sistema Web de Facturación. Todos los derechos reservados.
    </footer>

    <script>
        // Mobile menu toggle
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Actualizar precios y totales en la factura
        function updateTotals() {
            const rows = document.querySelectorAll('#productos-list tr.producto-row');
            let totalFactura = 0;

            rows.forEach((row) => {
                const selectProducto = row.querySelector('select[name="producto[]"]');
                const cantidadInput = row.querySelector('input[name="cantidad[]"]');
                const precioUnitarioCell = row.querySelector('.precio-unitario');
                const totalProductoCell = row.querySelector('.total-producto');

                let precioUnitario = 0;
                if (selectProducto.selectedIndex > 0) {
                    const selectedOption = selectProducto.options[selectProducto.selectedIndex];
                    precioUnitario = parseFloat(selectedOption.getAttribute('data-precio') || 0);
                }

                precioUnitarioCell.textContent = precioUnitario.toFixed(2);

                const cantidad = parseInt(cantidadInput.value) || 0;
                const totalProducto = precioUnitario * cantidad;
                totalProductoCell.textContent = totalProducto.toFixed(2);

                totalFactura += totalProducto;
            });

            document.getElementById('total-factura').textContent = totalFactura.toFixed(2);
            document.getElementById('total_factura_input').value = totalFactura.toFixed(2);
        }

        // Agregar fila de producto
        const addProductoBtn = document.getElementById('add-producto');
        addProductoBtn.addEventListener('click', () => {
            const tbody = document.getElementById('productos-list');
            const productosSelects = document.querySelectorAll('select[name="producto[]"]');

            // Clonar el primer select para obtener todas las opciones
            const selectOriginal = productosSelects[0];
            const newRow = document.createElement('tr');
            newRow.classList.add('producto-row');

            newRow.innerHTML = `
                <td class="border-b border-gray-300 px-3 py-2">
                    <select
                        name="producto[]"
                        class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        ${selectOriginal.innerHTML}
                    </select>
                </td>
                <td class="border-b border-gray-300 px-3 py-2">
                    <input
                        type="number"
                        name="cantidad[]"
                        min="1"
                        value="1"
                        class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    />
                </td>
                <td class="border-b border-gray-300 px-3 py-2 precio-unitario">0.00</td>
                <td class="border-b border-gray-300 px-3 py-2 total-producto">0.00</td>
                <td class="border-b border-gray-300 px-3 py-2 text-center">
                    <button
                        type="button"
                        class="remove-producto text-red-600 hover:text-red-800 focus:outline-none"
                        aria-label="Eliminar producto"
                    >
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(newRow);
            attachRowListeners(newRow);
            updateTotals();
        });

        // Asociar listeners a los elementos de la fila de producto
        function attachRowListeners(row) {
            const selectProducto = row.querySelector('select[name="producto[]"]');
            const cantidadInput = row.querySelector('input[name="cantidad[]"]');
            const removeBtn = row.querySelector('.remove-producto');

            selectProducto.addEventListener('change', updateTotals);
            cantidadInput.addEventListener('input', updateTotals);

            removeBtn.addEventListener('click', () => {
                // No eliminar si es la única fila
                const rows = document.querySelectorAll('#productos-list tr.producto-row');
                if (rows.length > 1) {
                    row.remove();
                    updateTotals();
                } else {
                    alert('Debe haber al menos un producto en la factura.');
                }
            });
        }

        // Asociar listeners a la fila inicial de producto
        document.querySelectorAll('#productos-list tr.producto-row').forEach(attachRowListeners);

        // Inicializar totales
        updateTotals();
    </script>
</body>

</html>
<?php
// Cerrar la conexión
$conn->close();
?>