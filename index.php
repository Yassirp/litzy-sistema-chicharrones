<?php
/**
 * Punto de entrada principal para Railway
 * Redirige todas las peticiones al directorio public
 */

// Cambiar al directorio public
chdir('public');

// Incluir el archivo principal
require_once 'index.php';
?>
