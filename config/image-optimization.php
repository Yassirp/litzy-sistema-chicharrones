<?php
/**
 * Configuración de optimización de imágenes
 */

// Configuración de optimización
define('IMAGE_MAX_WIDTH', 800);
define('IMAGE_MAX_HEIGHT', 600);
define('IMAGE_QUALITY', 85);
define('IMAGE_LAZY_LOADING', true);

// Formatos soportados
define('SUPPORTED_FORMATS', ['jpg', 'jpeg', 'png', 'webp']);

// Tamaños máximos por tipo de imagen
define('MAX_FILE_SIZE', 500000); // 500KB
define('MAX_PRODUCT_IMAGE_SIZE', 200000); // 200KB

// Configuración de lazy loading
define('LAZY_LOADING_MARGIN', '50px');
define('LAZY_LOADING_THRESHOLD', 0.1);

// URLs de compresión online (para recomendaciones)
define('COMPRESSION_URLS', [
    'TinyPNG' => 'https://tinypng.com/',
    'Squoosh' => 'https://squoosh.app/',
    'Compressor.io' => 'https://compressor.io/'
]);

// Función para obtener configuración
function getImageConfig() {
    return [
        'max_width' => IMAGE_MAX_WIDTH,
        'max_height' => IMAGE_MAX_HEIGHT,
        'quality' => IMAGE_QUALITY,
        'lazy_loading' => IMAGE_LAZY_LOADING,
        'supported_formats' => SUPPORTED_FORMATS,
        'max_file_size' => MAX_FILE_SIZE,
        'max_product_image_size' => MAX_PRODUCT_IMAGE_SIZE,
        'lazy_loading_margin' => LAZY_LOADING_MARGIN,
        'lazy_loading_threshold' => LAZY_LOADING_THRESHOLD,
        'compression_urls' => COMPRESSION_URLS
    ];
}

// Función para verificar si una imagen necesita optimización
function needsOptimization($imagePath) {
    if (!file_exists($imagePath)) {
        return false;
    }
    
    $fileSize = filesize($imagePath);
    $imageInfo = getimagesize($imagePath);
    
    if (!$imageInfo) {
        return false;
    }
    
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    
    // Verificar si necesita optimización
    return $fileSize > MAX_FILE_SIZE || 
           $width > IMAGE_MAX_WIDTH || 
           $height > IMAGE_MAX_HEIGHT;
}

// Función para obtener recomendaciones de optimización
function getOptimizationRecommendations($imagePath) {
    if (!file_exists($imagePath)) {
        return [];
    }
    
    $recommendations = [];
    $fileSize = filesize($imagePath);
    $imageInfo = getimagesize($imagePath);
    
    if (!$imageInfo) {
        return $recommendations;
    }
    
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    
    if ($fileSize > MAX_FILE_SIZE) {
        $recommendations[] = "Archivo muy pesado ({$fileSize} bytes). Recomendado: " . MAX_FILE_SIZE . " bytes máximo.";
    }
    
    if ($width > IMAGE_MAX_WIDTH || $height > IMAGE_MAX_HEIGHT) {
        $recommendations[] = "Imagen muy grande ({$width}x{$height}). Recomendado: " . IMAGE_MAX_WIDTH . "x" . IMAGE_MAX_HEIGHT . " máximo.";
    }
    
    if (!empty($recommendations)) {
        $recommendations[] = "Herramientas recomendadas: " . implode(', ', array_keys(COMPRESSION_URLS));
    }
    
    return $recommendations;
}
?>
