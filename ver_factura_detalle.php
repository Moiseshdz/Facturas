<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Verificar que se proporcionó un ID de factura
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ver_facturas");
    exit;
}

$factura_id = intval($_GET['id']);
$modo_impresion = isset($_GET['print']) && $_GET['print'] == 1;

// Consulta para obtener los datos de la factura
$factura_query = "SELECT f.id, f.fecha, f.total, c.id as cliente_id, c.nombre as cliente_nombre, 
                 c.email as cliente_email, c.telefono as cliente_telefono
                 FROM facturas f
                 INNER JOIN clientes c ON f.cliente_id = c.id
                 WHERE f.id = $factura_id";
$factura_result = $conn->query($factura_query);

// Verificar si existe la factura
if (!$factura_result || $factura_result->num_rows === 0) {
    echo "<script>
            alert('Factura no encontrada');
            window.location.href='ver_facturas';
          </script>";
    exit;
}

$factura = $factura_result->fetch_assoc();

// Consulta para obtener los detalles de la factura (productos)
$detalles_query = "SELECT fd.id, fd.cantidad, fd.precio_unitario, fd.subtotal, p.nombre as producto_nombre
                  FROM factura_detalles fd
                  INNER JOIN productos p ON fd.producto_id = p.id
                  WHERE fd.factura_id = $factura_id";
$detalles_result = $conn->query($detalles_query);

// Contar el total de detalles
$total_detalles = $detalles_result ? $detalles_result->num_rows : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Factura #<?php echo $factura_id; ?> - Sistema Web de Facturación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .print-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <?php if (!$modo_impresion): ?>
    <!-- Header - No se imprimirá -->
    <header class="bg-blue-700 text-white shadow no-print">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center text-blue-700 font-bold text-2xl">F</div>
                <h1 class="text-xl font-semibold">Sistema de Facturación</h1>
            </div>
            <nav class="hidden md:flex space-x-6 text-sm font-medium">
                <a class="hover:underline" href="ver_facturas">Ver Facturas</a>
                <a class="hover:underline" href="index#facturas">Nueva Factura</a>
                <a class="hover:underline" href="index#reportes">Reportes</a>
            </nav>
            <button aria-label="Abrir menú" class="md:hidden focus:outline-none focus:ring-2 focus:ring-white" id="menu-btn">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        <nav class="md:hidden bg-blue-600 text-white px-4 py-2 space-y-2 hidden" id="mobile-menu">
            <a class="block py-1 hover:underline" href="ver_facturas.php">Ver Facturas</a>
            <a class="block py-1 hover:underline" href="index#facturas">Nueva Factura</a>
            <a class="block py-1 hover:underline" href="index#reportes">Reportes</a>
        </nav>
    </header>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8 print-container">
        <?php if (!$modo_impresion): ?>
        <div class="flex justify-between items-center mb-6 no-print">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Factura #<?php echo $factura_id; ?></h2>
                <p class="text-gray-600 mt-1">Total de productos: <span class="font-semibold"><?php echo $total_detalles; ?></span></p>
            </div>
            <div class="space-x-2">
                <a href="ver_facturas.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="bg-white rounded shadow p-6 mb-6">
            <!-- Cabecera de la factura -->
            <div class="flex flex-col md:flex-row justify-between mb-6 pb-6 border-b border-gray-200">
                <div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-1">Sistema de Facturación</h3>
                    <p class="text-gray-500">Factura #<?php echo $factura_id; ?></p>
                    <p class="text-gray-500">Fecha: <?php echo date('d/m/Y', strtotime($factura['fecha'])); ?></p>
                </div>
                <div class="mt-4 md:mt-0">
                    <h4 class="font-semibold text-gray-800 mb-1">Cliente:</h4>
                    <p class="text-gray-800"><?php echo htmlspecialchars($factura['cliente_nombre']); ?></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($factura['cliente_email']); ?></p>
                    <?php if (!empty($factura['cliente_telefono'])) : ?>
                        <p class="text-gray-600"><?php echo htmlspecialchars($factura['cliente_telefono']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Detalles de la factura -->
            <h4 class="font-semibold text-gray-800 mb-3">Detalles de la factura:</h4>
            <div class="overflow-x-auto">
                <table class="w-full mb-6">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700">#</th>
                            <th class="px-4 py-2 text-left text-gray-700">Producto</th>
                            <th class="px-4 py-2 text-center text-gray-700">Cantidad</th>
                            <th class="px-4 py-2 text-right text-gray-700">Precio Unitario</th>
                            <th class="px-4 py-2 text-right text-gray-700">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($detalles_result && $detalles_result->num_rows > 0) : ?>
                            <?php 
                            $contador = 1;
                            while ($detalle = $detalles_result->fetch_assoc()) : 
                            ?>
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-3 text-gray-800"><?php echo $contador++; ?></td>
                                    <td class="px-4 py-3 text-gray-800"><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                                    <td class="px-4 py-3 text-center text-gray-800"><?php echo $detalle['cantidad']; ?></td>
                                    <td class="px-4 py-3 text-right text-gray-800">$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                    <td class="px-4 py-3 text-right text-gray-800">$<?php echo number_format($detalle['subtotal'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-500">No hay detalles disponibles</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="border-t-2 border-gray-300">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-semibold">Total:</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">$<?php echo number_format($factura['total'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Pie de la factura -->
            <div class="text-sm text-gray-600 mt-6 pt-6 border-t border-gray-200">
                <p>Gracias por su preferencia.</p>
                <p>Esta factura sirve como comprobante de pago.</p>
                <div class="mt-4 text-center text-xs text-gray-500">
                    <p>Sistema Web de Facturación © <?php echo date('Y'); ?></p>
                </div>
            </div>
        </div>
    </main>
    
    <?php if (!$modo_impresion): ?>
    <!-- Footer - No se imprimirá -->
    <footer class="bg-blue-700 text-white py-4 text-center text-sm no-print">
        © <?php echo date('Y'); ?> Sistema Web de Facturación. Todos los derechos reservados.
    </footer>
    <?php endif; ?>
    
    <script>
        // Mobile menu toggle
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        <?php if ($modo_impresion): ?>
        // Imprimir automáticamente en modo impresión
        window.addEventListener('load', function() {
            // Pequeña demora para asegurar que todo el contenido se cargue
            setTimeout(function() {
                window.print();
            }, 500);
        });
        <?php endif; ?>
    </script>
</body>
</html>
<?php
// Cerrar la conexión
$conn->close();
?>