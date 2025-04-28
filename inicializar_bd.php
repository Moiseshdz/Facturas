<?php
// Este script creará la base de datos completa si no existe
// Configuración de la conexión
$db_host = 'localhost';
$db_user = 'root';  // Ajusta según tu configuración
$db_password = '';  // Ajusta según tu configuración

// Conectar sin seleccionar una base de datos
$conn = new mysqli($db_host, $db_user, $db_password);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear la base de datos si no existe
$sql_create_db = "CREATE DATABASE IF NOT EXISTS facturacion_db";
if ($conn->query($sql_create_db) === TRUE) {
    echo "Base de datos creada o ya existente.<br>";
} else {
    die("Error al crear la base de datos: " . $conn->error);
}

// Seleccionar la base de datos
$conn->select_db("facturacion_db");

// Crear tabla de clientes
$sql_create_clientes = "CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefono VARCHAR(50)
)";

if ($conn->query($sql_create_clientes) === TRUE) {
    echo "Tabla de clientes creada o ya existente.<br>";
} else {
    die("Error al crear la tabla clientes: " . $conn->error);
}

// Crear tabla de productos
$sql_create_productos = "CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    precio DECIMAL(10,2) NOT NULL
)";

if ($conn->query($sql_create_productos) === TRUE) {
    echo "Tabla de productos creada o ya existente.<br>";
} else {
    die("Error al crear la tabla productos: " . $conn->error);
}

// Crear tabla de facturas
$sql_create_facturas = "CREATE TABLE IF NOT EXISTS facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
)";

if ($conn->query($sql_create_facturas) === TRUE) {
    echo "Tabla de facturas creada o ya existente.<br>";
} else {
    die("Error al crear la tabla facturas: " . $conn->error);
}

// Crear tabla de detalles de factura
$sql_create_detalles = "CREATE TABLE IF NOT EXISTS factura_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (factura_id) REFERENCES facturas(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
)";

if ($conn->query($sql_create_detalles) === TRUE) {
    echo "Tabla de detalles de factura creada o ya existente.<br>";
} else {
    die("Error al crear la tabla factura_detalles: " . $conn->error);
}

// Verificar si ya existen datos en las tablas
$check_clientes = $conn->query("SELECT COUNT(*) as count FROM clientes");
$cliente_count = $check_clientes->fetch_assoc()['count'];

$check_productos = $conn->query("SELECT COUNT(*) as count FROM productos");
$producto_count = $check_productos->fetch_assoc()['count'];

// Insertar datos de prueba si las tablas están vacías
if ($cliente_count == 0) {
    // Datos de prueba para clientes
    $sql_insert_clientes = "INSERT INTO clientes (nombre, email, telefono) VALUES
        ('Juan Pérez', 'juan.perez@email.com', '1234567890'),
        ('María Gómez', 'maria.gomez@email.com', '0987654321'),
        ('Empresa XYZ', 'contacto@xyz.com', '5551234567')";

    if ($conn->query($sql_insert_clientes) === TRUE) {
        echo "Datos de prueba para clientes insertados correctamente.<br>";
    } else {
        echo "Error al insertar datos de prueba para clientes: " . $conn->error . "<br>";
    }
}

if ($producto_count == 0) {
    // Datos de prueba para productos
    $sql_insert_productos = "INSERT INTO productos (nombre, precio) VALUES
        ('Producto 1', 100),
        ('Producto 2', 200),
        ('Producto 3', 300),
        ('Producto 4', 400),
        ('Producto 5', 500)";

    if ($conn->query($sql_insert_productos) === TRUE) {
        echo "Datos de prueba para productos insertados correctamente.<br>";
    } else {
        echo "Error al insertar datos de prueba para productos: " . $conn->error . "<br>";
    }
}

echo "<br>Inicialización completada. <a href='index.php'>Ir al sistema de facturación</a>";

// Cerrar la conexión
$conn->close();
?>