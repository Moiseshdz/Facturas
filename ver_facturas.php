<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Consulta para obtener todas las facturas con información del cliente
$facturas_query = "SELECT f.id, f.fecha, f.total, c.nombre as cliente_nombre, c.email as cliente_email
                  FROM facturas f
                  INNER JOIN clientes c ON f.cliente_id = c.id
                  ORDER BY f.fecha DESC";
$facturas_result = $conn->query($facturas_query);

// Contar el total de facturas
$total_facturas = $facturas_result ? $facturas_result->num_rows : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Facturas - Sistema Web de Facturación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet"/>
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
                <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center text-blue-700 font-bold text-2xl">F</div>
                <h1 class="text-xl font-semibold">Sistema de Facturación</h1>
            </div>
            <nav class="hidden md:flex space-x-6 text-sm font-medium">
                <a class="hover:underline" href="index#facturas">Nueva Factura</a>
                <a class="hover:underline" href="index#clientes">Clientes</a>
                <a class="hover:underline" href="index#productos">Productos</a>
                <a class="hover:underline" href="index#reportes">Reportes</a>
            </nav>
            <button aria-label="Abrir menú" class="md:hidden focus:outline-none focus:ring-2 focus:ring-white" id="menu-btn">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        <nav class="md:hidden bg-blue-600 text-white px-4 py-2 space-y-2 hidden" id="mobile-menu">
            <a class="block py-1 hover:underline" href="index#facturas">Nueva Factura</a>
            <a class="block py-1 hover:underline" href="index#clientes">Clientes</a>
            <a class="block py-1 hover:underline" href="index#productos">Productos</a>
            <a class="block py-1 hover:underline" href="index#reportes">Reportes</a>
        </nav>
    </header>
    
    <!-- Barra de navegación secundaria -->
    <div class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3">
            <ul class="flex flex-wrap -mx-2 text-sm">
                <li class="px-2 py-1">
                    <a href="index" class="flex items-center text-blue-600 hover:text-blue-800">
                        <i class="fas fa-home mr-1"></i> Inicio
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
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Facturas Emitidas</h2>
                <p class="text-gray-600 mt-1">Total de facturas: <span class="font-semibold"><?php echo $total_facturas; ?></span></p>
            </div>
            <a href="index.php#facturas" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Nueva Factura
            </a>
        </div>
        
        <div class="bg-white rounded shadow overflow-x-auto">
            <?php if ($facturas_result && $facturas_result->num_rows > 0) : ?>
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700">#</th>
                            <th class="px-4 py-3 text-left text-gray-700">ID</th>
                            <th class="px-4 py-3 text-left text-gray-700">Fecha</th>
                            <th class="px-4 py-3 text-left text-gray-700">Cliente</th>
                            <th class="px-4 py-3 text-left text-gray-700">Total</th>
                            <th class="px-4 py-3 text-center text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $contador = 1;
                        while ($factura = $facturas_result->fetch_assoc()) : 
                        ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900"><?php echo $contador++; ?></td>
                                <td class="px-4 py-3 text-gray-900">#<?php echo $factura['id']; ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo date('d/m/Y', strtotime($factura['fecha'])); ?></td>
                                <td class="px-4 py-3">
                                    <div class="text-gray-900"><?php echo htmlspecialchars($factura['cliente_nombre']); ?></div>
                                    <div class="text-gray-500 text-sm"><?php echo htmlspecialchars($factura['cliente_email']); ?></div>
                                </td>
                                <td class="px-4 py-3 text-gray-900 font-medium">$<?php echo number_format($factura['total'], 2); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <a href="ver_factura_detalle?id=<?php echo $factura['id']; ?>" class="text-blue-600 hover:text-blue-800 mx-1" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" onclick="printFactura(<?php echo $factura['id']; ?>)" class="text-green-600 hover:text-green-800 mx-1" title="Imprimir">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="text-center py-8">
                    <i class="fas fa-file-invoice text-gray-300 text-5xl mb-3"></i>
                    <p class="text-gray-600">No hay facturas registradas aún.</p>
                    <a href="index.php#facturas" class="inline-block mt-4 text-blue-600 hover:underline">Crear nueva factura</a>
                </div>
            <?php endif; ?>
        </div>
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
        
        // Función para imprimir factura (versión real)
        function printFactura(id) {
            // Abrir una nueva ventana con la versión imprimible de la factura
            const printWindow = window.open('ver_factura_detalle?id=' + id + '&print=1', '_blank');
            
            // Agregar un listener para imprimir automáticamente cuando se cargue la página
            printWindow.addEventListener('load', function() {
                printWindow.print();
                // Opcionalmente, cerrar la ventana después de imprimir
                // printWindow.close();
            });
        }
    </script>
</body>
</html>
<?php
// Cerrar la conexión
$conn->close();
?>