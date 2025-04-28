<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Verificar que se haya enviado el formulario mediante POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Determinar la acción (crear o actualizar)
    $action = isset($_POST['action']) ? $_POST['action'] : 'create';
    
    // Recuperar datos del formulario y sanitizarlos
    $nombre = $conn->real_escape_string($_POST['nombre-cliente']);
    $email = $conn->real_escape_string($_POST['email-cliente']);
    $telefono = $conn->real_escape_string($_POST['telefono-cliente']);
    
    // Validaciones básicas
    if (empty($nombre) || empty($email)) {
        echo "<script>
                alert('El nombre y el email son obligatorios');
                window.location.href='index.php#clientes';
              </script>";
        exit;
    }
    
    // ACCIÓN: CREAR NUEVO CLIENTE
    if ($action === 'create') {
        // Verificar si el email ya existe
        $check_query = "SELECT id FROM clientes WHERE email = '$email'";
        $check_result = $conn->query($check_query);
        
        if ($check_result && $check_result->num_rows > 0) {
            echo "<script>
                    alert('Ya existe un cliente con ese correo electrónico');
                    window.location.href='index.php#clientes';
                  </script>";
            exit;
        }
        
        // Insertar el nuevo cliente en la base de datos
        $insert_query = "INSERT INTO clientes (nombre, email, telefono) VALUES ('$nombre', '$email', '$telefono')";
        
        if ($conn->query($insert_query) === TRUE) {
            echo "<script>
                    alert('Cliente guardado correctamente');
                    window.location.href='ver_clientes.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al guardar el cliente: " . $conn->error . "');
                    window.location.href='index.php#clientes';
                  </script>";
        }
    }
    // ACCIÓN: ACTUALIZAR CLIENTE EXISTENTE
    else if ($action === 'update' && isset($_POST['cliente_id']) && is_numeric($_POST['cliente_id'])) {
        $cliente_id = intval($_POST['cliente_id']);
        
        // Verificar si el email ya existe (excluyendo el cliente actual)
        $check_query = "SELECT id FROM clientes WHERE email = '$email' AND id != $cliente_id";
        $check_result = $conn->query($check_query);
        
        if ($check_result && $check_result->num_rows > 0) {
            echo "<script>
                    alert('Ya existe otro cliente con ese correo electrónico');
                    window.location.href='editar_cliente.php?id=$cliente_id';
                  </script>";
            exit;
        }
        
        // Actualizar el cliente en la base de datos
        $update_query = "UPDATE clientes 
                        SET nombre = '$nombre', 
                            email = '$email', 
                            telefono = '$telefono' 
                        WHERE id = $cliente_id";
        
        if ($conn->query($update_query) === TRUE) {
            echo "<script>
                    alert('Cliente actualizado correctamente');
                    window.location.href='ver_clientes.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al actualizar el cliente: " . $conn->error . "');
                    window.location.href='editar_cliente.php?id=$cliente_id';
                  </script>";
        }
    } else {
        // Si no se proporcionó una acción válida
        echo "<script>
                alert('Acción no válida');
                window.location.href='index.php#clientes';
              </script>";
    }
} else {
    // Si se accede directamente a este archivo sin enviar el formulario
    header("Location: index.php");
    exit;
}

// Cerrar la conexión
$conn->close();
?>