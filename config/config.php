<?php
// Configuración del proyecto
define('DB_HOST', 'localhost');
define('DB_NAME', 'caja_chicharron');
define('DB_USER', 'Yassir');
define('DB_PASS', 'Yassirpaez1');
define('DB_CHARSET', 'UTF-8');

// Configuración de seguridad
define('SECURE_SESSION', true);
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configuración de la aplicación
define('APP_NAME', 'Litzy - Sistema de Caja');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', false); // Cambiar a true solo para desarrollo

// Configuración de archivos
define('UPLOAD_PATH', '../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Configuración de seguridad adicional
if (SECURE_SESSION) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
}
?>
