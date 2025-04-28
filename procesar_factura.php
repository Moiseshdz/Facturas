<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Verificar que se haya enviado el formulario mediante POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar datos del formulario
    $cliente_id = intval($_POST['cliente']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $total_factura = floatval($_POST['total_factura']);
    
    // Arrays para los productos y cantidades
    $productos = isset($_POST['producto']) ? $_POST['producto'] : [];
    $cantidades = isset($_POST['cantidad']) ? $_POST['cantidad'] : [];
    
    // Validaciones básicas
    if (empty($cliente_id) || empty($fecha) || empty($productos) || empty($cantidades)) {
        echo "<script>
                alert('Todos los campos son obligatorios');
                window.location.href='index#facturas';
              </script>";
        exit;
    }
    
    // Verificar que haya al menos un producto
    if (count($productos) === 0) {
        echo "<script>
                alert('Debe agregar al menos un producto');
                window.location.href='index#facturas';
              </script>";
        exit;
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Insertar la factura
        $insert_factura = "INSERT INTO facturas (cliente_id, fecha, total) VALUES ($cliente_id, '$fecha', $total_factura)";
        $conn->query($insert_factura);
        
        // Obtener el ID de la factura recién insertada
        $factura_id = $conn->insert_id;
        
        // Insertar los detalles de la factura
        for ($i = 0; $i < count($productos); $i++) {
            $producto_id = intval($productos[$i]);
            $cantidad = intval($cantidades[$i]);
            
            // Obtener el precio del producto
            $producto_query = "SELECT precio FROM productos WHERE id = $producto_id";
            $producto_result = $conn->query($producto_query);
            
            if ($producto_result && $producto_result->num_rows > 0) {
                $producto_data = $producto_result->fetch_assoc();
                $precio_unitario = floatval($producto_data['precio']);
                $subtotal = $precio_unitario * $cantidad;
                
                $insert_detalle = "INSERT INTO factura_detalles (factura_id, producto_id, cantidad, precio_unitario, subtotal) 
                                 VALUES ($factura_id, $producto_id, $cantidad, $precio_unitario, $subtotal)";
                $conn->query($insert_detalle);
            } else {
                // Si no se encuentra el producto, hacer rollback
                throw new Exception("Producto no encontrado");
            }
        }
        
        // Confirmar la transacción
        $conn->commit();
        
        echo "<script>
                alert('Factura guardada correctamente');
                window.location.href='ver_facturas';
              </script>";
    } catch (Exception $e) {
        // Si hay algún error, hacer rollback
        $conn->rollback();
        
        echo "<script>
                alert('Error al guardar la factura: " . $e->getMessage() . "');
                window.location.href='index#facturas';
              </script>";
    }
} else {
    // Si se accede directamente a este archivo sin enviar el formulario
    header("Location: index");
    exit;
}

// Cerrar la conexión
$conn->close();
?>