<?php
/**
 * Netlify Function para PHP
 * Esta función maneja todas las rutas de la aplicación
 */

// Incluir la configuración
require_once '../../config/db.php';

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remover la base path de Netlify
$path = str_replace('/.netlify/functions/index.php', '', $path);

// Si es la raíz, mostrar el dashboard
if ($path === '/' || $path === '') {
    include '../../public/dashboard.php';
    return;
}

// Mapear rutas a archivos PHP
$routes = [
    '/dashboard' => '../../public/dashboard.php',
    '/ventas' => '../../public/ventas.php',
    '/inventario' => '../../public/inventario.php',
    '/productos' => '../../public/productos.php',
    '/cierre' => '../../public/cierre.php',
    '/historial' => '../../public/historial.php',
    '/registro' => '../../public/registro.php',
    '/logout' => '../../public/logout.php',
];

// Si la ruta existe, incluir el archivo
if (isset($routes[$path])) {
    include $routes[$path];
} else {
    // Si no existe, mostrar 404
    http_response_code(404);
    echo "Página no encontrada: " . $path;
}
?>
