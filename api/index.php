<?php
/**
 * Vercel PHP Handler
 * Este archivo maneja todas las rutas de la aplicación en Vercel
 */

// Configurar headers para HTML
header('Content-Type: text/html; charset=UTF-8');

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remover la base path de Vercel
$path = str_replace('/api/', '', $path);
$path = ltrim($path, '/');

// Si es la raíz o está vacía, mostrar el login
if ($path === '' || $path === '/' || $path === 'index.php') {
    include '../public/index.php';
    return;
}

// Mapear rutas a archivos PHP
$routes = [
    'dashboard' => '../public/dashboard.php',
    'ventas' => '../public/ventas.php',
    'inventario' => '../public/inventario.php',
    'productos' => '../public/productos.php',
    'cierre' => '../public/cierre.php',
    'historial' => '../public/historial.php',
    'registro' => '../public/registro.php',
    'logout' => '../public/logout.php',
    'guardar_venta' => '../public/guardar_venta.php',
    'productos_agregar' => '../public/productos_agregar.php',
    'productos_editar' => '../public/productos_editar.php',
    'productos_eliminar' => '../public/productos_eliminar.php',
    'productos_listar' => '../public/productos_listar.php',
    'agregar_stock' => '../public/agregar_stock.php',
    'obtener_estadisticas' => '../public/obtener_estadisticas.php',
];

// Si la ruta existe, incluir el archivo
if (isset($routes[$path])) {
    include $routes[$path];
} else {
    // Si no existe, mostrar 404
    http_response_code(404);
    echo "<!DOCTYPE html><html><head><title>404 - Página no encontrada</title></head><body><h1>404 - Página no encontrada</h1><p>La página '$path' no existe.</p></body></html>";
}
?>
