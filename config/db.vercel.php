<?php
/**
 * === LITZY - CONFIGURACIÓN DE BD PARA VERCEL ===
 * Configuración específica para el despliegue en Vercel
 */

// Obtener configuración de variables de entorno de Vercel
$serverName = $_ENV['DB_SERVER'] ?? 'localhost';
$database = $_ENV['DB_NAME'] ?? 'caja_chicharron';
$username = $_ENV['DB_USER'] ?? 'sa';
$password = $_ENV['DB_PASS'] ?? '';

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
    // En caso de error, mostrar mensaje amigable
    die("Error de conexión a la base de datos. Verifica la configuración en Vercel.");
}

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');
?>
