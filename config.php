
<?php
// Configuración de la base de datos
$db_host = 'localhost';      // Host de la base de datos
$db_user = 'root';           // Usuario de la base de datos (ajusta según tu configuración)
$db_password = '';           // Contraseña (ajusta según tu configuración)
$db_name = 'facturacion_db'; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar el conjunto de caracteres
$conn->set_charset("utf8");
?>