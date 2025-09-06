<?php
/**
 * === LITZY - CONFIGURACIÓN DE BD PARA NETLIFY ===
 * Configuración específica para el despliegue en Netlify
 */

// Incluir helpers de Netlify
require_once 'netlify-helpers.php';

// Obtener configuración de variables de entorno
$config = get_netlify_db_config();

$serverName = $config['serverName'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

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
    die("Error de conexión a la base de datos. Verifica la configuración.");
}

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');
?>
