<?php
/**
 * === LITZY - CONFIGURACIÓN DE BASE DE DATOS ===
 * 
 * Archivo de ejemplo para configuración de base de datos
 * Copia este archivo como 'db.php' y configura tus datos
 */

// Configuración de base de datos SQL Server
$serverName = "localhost"; // o tu servidor de SQL Server
$database = "caja_chicharron";
$username = "tu_usuario";
$password = "tu_contraseña";

// Configuración de conexión
$connectionInfo = array(
    "Database" => $database,
    "UID" => $username,
    "PWD" => $password,
    "CharacterSet" => "UTF-8"
);

// Intentar conexión
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die("Error de conexión: " . print_r(sqlsrv_errors(), true));
}

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

echo "Conexión exitosa a la base de datos";
?>
