<?php
/**
 * === LITZY - HELPERS PARA NETLIFY ===
 * Funciones auxiliares para el despliegue en Netlify
 */

/**
 * Obtiene la URL base del sitio
 */
function get_base_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $path_info = pathinfo($script_name);
    $base_path = $path_info['dirname'];
    
    // Para Netlify, ajustar la ruta base
    if (strpos($host, 'netlify.app') !== false) {
        return $protocol . '://' . $host;
    }
    
    return $protocol . '://' . $host . $base_path;
}

/**
 * Obtiene la URL completa de una imagen
 */
function get_image_url($image_path) {
    if (empty($image_path)) {
        return get_base_url() . '/img/todoalbarril.jpg'; // Imagen por defecto
    }
    
    // Si es una ruta relativa, convertir a absoluta
    if (strpos($image_path, 'http') !== 0) {
        return get_base_url() . '/uploads/' . basename($image_path);
    }
    
    return $image_path;
}

/**
 * Verifica si una imagen existe
 */
function image_exists($image_path) {
    if (empty($image_path)) {
        return false;
    }
    
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . basename($image_path);
    return file_exists($full_path);
}

/**
 * Obtiene la ruta de la imagen por defecto
 */
function get_default_image() {
    return get_base_url() . '/img/todoalbarril.jpg';
}

/**
 * Configuración de base de datos para Netlify
 */
function get_netlify_db_config() {
    // En Netlify, usar variables de entorno
    $serverName = $_ENV['DB_SERVER'] ?? 'localhost';
    $database = $_ENV['DB_NAME'] ?? 'caja_chicharron';
    $username = $_ENV['DB_USER'] ?? 'sa';
    $password = $_ENV['DB_PASS'] ?? '';
    
    return [
        'serverName' => $serverName,
        'database' => $database,
        'username' => $username,
        'password' => $password
    ];
}
?>
