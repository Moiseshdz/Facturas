<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Verificar que se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
            alert('ID de producto no válido');
            window.location.href='index#productos';
          </script>";
    exit;
}

$producto_id = intval($_GET['id']);

// Obtener datos del producto
$query = "SELECT * FROM productos WHERE id = $producto_id";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    echo "<script>
            alert('Producto no encontrado');
            window.location.href='index#productos';
          </script>";
    exit;
}

$producto = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Editar Producto - Sistema Web de Facturación</title>
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
            <h2 class="text-2xl font-semibold text-gray-800">Editar Producto</h2>
            <a href="ver_productos" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Volver a Productos
            </a>
        </div>
        
        <div class="bg-white rounded shadow p-6">
            <form class="space-y-6 max-w-lg" action="procesar_producto" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="producto_id" value="<?php echo $producto_id; ?>">
                
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="nombre-producto">Nombre del Producto</label>
                    <input class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           id="nombre-producto" name="nombre-producto" required type="text" 
                           value="<?php echo htmlspecialchars($producto['nombre']); ?>"/>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="precio-producto">Precio Unitario</label>
                    <input class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           id="precio-producto" name="precio-producto" required type="number" step="0.01" min="0" 
                           value="<?php echo htmlspecialchars($producto['precio']); ?>"/>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="ver_productos" class="px-4 py-2 border border-gray-400 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        Cancelar
                    </a>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500" type="submit">
                        Actualizar Producto
                    </button>
                </div>
            </form>
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
    </script>
</body>
</html>
<?php
// Cerrar la conexión
$conn->close();
?>