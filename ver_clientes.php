<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Consulta para obtener todos los clientes
$clientes_query = "SELECT * FROM clientes ORDER BY nombre";
$clientes_result = $conn->query($clientes_query);

// Verificar si se solicita eliminar un cliente
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $cliente_id = intval($_GET['eliminar']);
    
    // Verificar si el cliente tiene facturas asociadas
    $check_facturas = "SELECT COUNT(*) as total FROM facturas WHERE cliente_id = $cliente_id";
    $facturas_result = $conn->query($check_facturas);
    $total_facturas = $facturas_result->fetch_assoc()['total'];
    
    if ($total_facturas > 0) {
        echo "<script>
                alert('No se puede eliminar el cliente porque tiene facturas asociadas');
                window.location.href='ver_clientes';
              </script>";
    } else {
        // Eliminar el cliente
        $eliminar_query = "DELETE FROM clientes WHERE id = $cliente_id";
        if ($conn->query($eliminar_query) === TRUE) {
            echo "<script>
                    alert('Cliente eliminado correctamente');
                    window.location.href='ver_clientes';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al eliminar el cliente: " . $conn->error . "');
                    window.location.href='ver_clientes';
                  </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Clientes - Sistema Web de Facturación</title>
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
                <a class="hover:underline" href="index#facturas">Facturas</a>
                <a class="hover:underline" href="index#clientes">Clientes</a>
                <a class="hover:underline" href="index#productos">Productos</a>
                <a class="hover:underline" href="index#reportes">Reportes</a>
            </nav>
            <button aria-label="Abrir menú" class="md:hidden focus:outline-none focus:ring-2 focus:ring-white" id="menu-btn">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        <nav class="md:hidden bg-blue-600 text-white px-4 py-2 space-y-2 hidden" id="mobile-menu">
            <a class="block py-1 hover:underline" href="index#facturas">Facturas</a>
            <a class="block py-1 hover:underline" href="index#clientes">Clientes</a>
            <a class="block py-1 hover:underline" href="index#productos">Productos</a>
            <a class="block py-1 hover:underline" href="index#reportes">Reportes</a>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Clientes Registrados</h2>
            <a href="index.php#clientes" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Nuevo Cliente
            </a>
        </div>
        
        <div class="bg-white rounded shadow overflow-x-auto">
            <?php if ($clientes_result && $clientes_result->num_rows > 0) : ?>
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700">#</th>
                            <th class="px-4 py-3 text-left text-gray-700">Nombre</th>
                            <th class="px-4 py-3 text-left text-gray-700">Email</th>
                            <th class="px-4 py-3 text-left text-gray-700">Teléfono</th>
                            <th class="px-4 py-3 text-center text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $contador = 1;
                        while ($cliente = $clientes_result->fetch_assoc()) : ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900"><?php echo $contador++; ?></td>
                                <td class="px-4 py-3 text-gray-900"><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($cliente['email']); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($cliente['telefono'] ? $cliente['telefono'] : '-'); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <a href="editar_cliente?id=<?php echo $cliente['id']; ?>" class="text-blue-600 hover:text-blue-800 mx-1" title="Editar cliente">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="confirmarEliminar(<?php echo $cliente['id']; ?>, '<?php echo addslashes(htmlspecialchars($cliente['nombre'])); ?>')" class="text-red-600 hover:text-red-800 mx-1" title="Eliminar cliente">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-300 text-5xl mb-3"></i>
                    <p class="text-gray-600">No hay clientes registrados aún.</p>
                    <a href="index.php#clientes" class="inline-block mt-4 text-blue-600 hover:underline">Registrar nuevo cliente</a>
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
        
        // Confirmar eliminación de cliente
        function confirmarEliminar(id, nombre) {
            if (confirm(`¿Está seguro que desea eliminar al cliente "${nombre}"?`)) {
                window.location.href = `ver_clientes?eliminar=${id}`;
            }
        }
    </script>
</body>
</html>
<?php
// Cerrar la conexión
$conn->close();
?>