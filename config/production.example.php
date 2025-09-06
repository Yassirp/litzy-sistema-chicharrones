<?php
/**
 * === LITZY - CONFIGURACIÓN DE PRODUCCIÓN ===
 * 
 * Archivo de ejemplo para configuración de producción
 * Copia este archivo como 'production.php' y configura tus datos
 */

// Configuración de base de datos para producción
$serverName = "tu_servidor_produccion";
$database = "caja_chicharron_prod";
$username = "usuario_produccion";
$password = "contraseña_segura_produccion";

// Configuración de conexión
$connectionInfo = array(
    "Database" => $database,
    "UID" => $username,
    "PWD" => $password,
    "CharacterSet" => "UTF-8",
    "Encrypt" => true,
    "TrustServerCertificate" => false
);

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores para producción
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../logs/production_errors.log');

// Configuración de seguridad
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

echo "Configuración de producción cargada";
?>
