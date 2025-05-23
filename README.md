# Sistema Web de Facturación con PHP y MySQL

Este es un sistema web de facturación completo desarrollado con PHP y MySQL que permite gestionar facturas, clientes y productos de manera sencilla y eficiente.

## Requisitos previos

- Servidor web (Apache, Nginx, etc.)
- PHP 7.0 o superior
- MySQL 5.6 o superior
- Navegador web moderno

## Instalación

1. **Descarga los archivos** del sistema a tu servidor web.

2. **Configura la conexión a la base de datos**:
   - Abre el archivo `config.php`
   - Modifica los valores de las siguientes variables según tu configuración:
     ```php
     $db_host = 'localhost';      // Host de la base de datos
     $db_user = 'root';           // Usuario de la base de datos
     $db_password = '';           // Contraseña de la base de datos
     $db_name = 'facturacion_db'; // Nombre de la base de datos
     ```

3. **Inicializa la base de datos**:
   - Accede a través del navegador a: `http://tuservidor/inicializar_bd.php`
   - Este script creará automáticamente la base de datos, las tablas necesarias y algunos datos de prueba.

4. **Accede al sistema**:
   - Una vez inicializada la base de datos, accede a: `http://tuservidor/index.php`

## Estructura del Sistema

### Archivos principales:

- **index.php**: Página principal con formularios para crear facturas, clientes y productos.
- **config.php**: Configuración de la conexión a la base de datos.
- **inicializar_bd.php**: Script para crear la base de datos y tablas necesarias.
- **procesar_cliente.php**: Procesa la creación y actualización de clientes.
- **procesar_producto.php**: Procesa la creación y actualización de productos.
- **procesar_factura.php**: Procesa la creación de nuevas facturas.
- **ver_facturas.php**: Muestra un listado de todas las facturas generadas.
- **ver_factura_detalle.php**: Muestra el detalle completo de una factura.
- **ver_clientes.php**: Muestra un listado de todos los clientes con opciones para editar y eliminar.
- **ver_productos.php**: Muestra un listado de todos los productos con opciones para editar y eliminar.
- **editar_cliente.php**: Formulario para editar clientes existentes.
- **editar_producto.php**: Formulario para editar productos existentes.

### Base de datos:

El sistema utiliza las siguientes tablas:

- **clientes**: Almacena información de los clientes.
- **productos**: Almacena información de los productos.
- **facturas**: Almacena información general de las facturas.
- **factura_detalles**: Almacena los productos incluidos en cada factura.

## Funcionalidades

### Gestión de Clientes
- Registrar nuevos clientes
- Ver lista de clientes
- Editar información de clientes
- Eliminar clientes (si no tienen facturas asociadas)

### Gestión de Productos
- Registrar nuevos productos
- Ver lista de productos
- Editar información de productos
- Eliminar productos (si no están en facturas)

### Gestión de Facturas
- Crear nuevas facturas
- Seleccionar cliente y fecha
- Agregar múltiples productos a una factura
- Cálculo automático de subtotales y total
- Ver lista de facturas emitidas
- Ver detalles completos de cada factura
- Imprimir facturas

### Reportes
- Visualización de estadísticas básicas (total de facturas, clientes y productos)

## Uso del Sistema

### Crear una factura
1. En la sección "Facturas", selecciona un cliente de la lista desplegable.
2. Selecciona la fecha de la factura.
3. Agrega productos a la factura, indicando la cantidad de cada uno.
4. Utiliza el botón "Agregar Producto" para incluir más productos.
5. Utiliza el botón "Guardar Factura" para finalizar.

### Gestionar clientes y productos
- Utiliza las secciones correspondientes para agregar, editar o eliminar clientes y productos.
- Para editar o eliminar, accede a las vistas de lista completa a través de los enlaces "Ver todos...".

## Notas de seguridad

Este sistema incluye medidas básicas de seguridad como:
- Sanitización de entradas de usuario
- Validación de datos antes de procesar
- Uso de consultas preparadas para evitar inyección SQL
- Verificación de dependencias antes de eliminar registros

## Personalización

Puedes personalizar el sistema según tus necesidades:
- Modificar el diseño ajustando el HTML y CSS
- Agregar nuevas funcionalidades extendiendo los archivos PHP existentes
- Agregar campos adicionales a las tablas para capturar más información

## Licencia

Este sistema es de código abierto y puede ser utilizado, modificado y distribuido libremente.
