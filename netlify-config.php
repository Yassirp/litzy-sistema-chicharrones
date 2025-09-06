<?php
/**
 * === LITZY - CONFIGURACIÓN PARA NETLIFY ===
 * Configuración específica para el despliegue en Netlify
 */

// Configuración de base de datos para Netlify
define('DB_SERVER', $_ENV['DB_SERVER'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'caja_chicharron');
define('DB_USER', $_ENV['DB_USER'] ?? 'sa');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Configuración de la aplicación
define('APP_URL', $_ENV['APP_URL'] ?? 'https://tu-sitio.netlify.app');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');

// Configuración de imágenes
define('UPLOADS_PATH', '/uploads/');
define('DEFAULT_IMAGE', '/img/todoalbarril.jpg');

// Configuración de seguridad
define('SESSION_SECRET', $_ENV['SESSION_SECRET'] ?? 'clave_secreta_por_defecto');

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores para producción
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '../logs/netlify_errors.log');
}
?>
