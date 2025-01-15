<?php
//
// Configuración de bd en localHost
$db_host = 'localhost';
$db_username = 'root';
$db_password = ''; // Cambia por una contraseña segura
$db_name = 'bd_orquideas_ver2';

// Conexión a la base de datos
$conexion = new mysqli($db_host, $db_username, $db_password, $db_name);
$conexion->set_charset("utf8");

// Manejo de errores
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configuración de bd en cloud
/*$db_host = 'localhost';
$db_username = 'u245906636_Admin';
$db_password = '2905Andres@'; // Cambia por una contraseña segura
$db_name = 'u245906636_orquideasAAO';

// Conexión a la base de datos
$conexion = new mysqli($db_host, $db_username, $db_password, $db_name);
$conexion->set_charset("utf8");

// Manejo de errores
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}*/


//configuracion en el entorno de Pruebas 