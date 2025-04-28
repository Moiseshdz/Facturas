<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Verificar que se haya enviado el formulario mediante POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Determinar la acción (crear o actualizar)
    $action = isset($_POST['action']) ? $_POST['action'] : 'create';
    
    // Recuperar datos del formulario y sanitizarlos
    $nombre = $conn->real_escape_string($_POST['nombre-producto']);
    $precio = floatval($_POST['precio-producto']);
    
    // Validaciones básicas
    if (empty($nombre) || $precio <= 0) {
        echo "<script>
                alert('El nombre es obligatorio y el precio debe ser mayor que 0');
                window.location.href='index#productos';
              </script>";
        exit;
    }
    
    // ACCIÓN: CREAR NUEVO PRODUCTO
    if ($action === 'create') {
        // Verificar si el nombre del producto ya existe
        $check_query = "SELECT id FROM productos WHERE nombre = '$nombre'";
        $check_result = $conn->query($check_query);
        
        if ($check_result && $check_result->num_rows > 0) {
            echo "<script>
                    alert('Ya existe un producto con ese nombre');
                    window.location.href='index#productos';
                  </script>";
            exit;
        }
        
        // Insertar el nuevo producto en la base de datos
        $insert_query = "INSERT INTO productos (nombre, precio) VALUES ('$nombre', $precio)";
        
        if ($conn->query($insert_query) === TRUE) {
            echo "<script>
                    alert('Producto guardado correctamente');
                    window.location.href='ver_productos';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al guardar el producto: " . $conn->error . "');
                    window.location.href='index#productos';
                  </script>";
        }
    }
    // ACCIÓN: ACTUALIZAR PRODUCTO EXISTENTE
    else if ($action === 'update' && isset($_POST['producto_id']) && is_numeric($_POST['producto_id'])) {
        $producto_id = intval($_POST['producto_id']);
        
        // Verificar si el nombre del producto ya existe (excluyendo el producto actual)
        $check_query = "SELECT id FROM productos WHERE nombre = '$nombre' AND id != $producto_id";
        $check_result = $conn->query($check_query);
        
        if ($check_result && $check_result->num_rows > 0) {
            echo "<script>
                    alert('Ya existe otro producto con ese nombre');
                    window.location.href='editar_producto?id=$producto_id';
                  </script>";
            exit;
        }
        
        // Actualizar el producto en la base de datos
        $update_query = "UPDATE productos 
                        SET nombre = '$nombre', 
                            precio = $precio
                        WHERE id = $producto_id";
        
        if ($conn->query($update_query) === TRUE) {
            echo "<script>
                    alert('Producto actualizado correctamente');
                    window.location.href='ver_productos';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al actualizar el producto: " . $conn->error . "');
                    window.location.href='editar_producto?id=$producto_id';
                  </script>";
        }
    } else {
        // Si no se proporcionó una acción válida
        echo "<script>
                alert('Acción no válida');
                window.location.href='index.php#productos';
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