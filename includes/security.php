<?php
// Funciones de seguridad para el proyecto

/**
 * Sanitizar entrada del usuario
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validar email
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validar número entero positivo
 */
function validate_positive_int($number) {
    return filter_var($number, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)));
}

/**
 * Validar número decimal positivo
 */
function validate_positive_float($number) {
    return filter_var($number, FILTER_VALIDATE_FLOAT, array("options" => array("min_range" => 0)));
}

/**
 * Generar token CSRF
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verificar sesión activa
 */
function check_session() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit();
    }
    
    // Verificar timeout de sesión
    if (defined('SESSION_TIMEOUT') && isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            session_destroy();
            header('Location: index.php?timeout=1');
            exit();
        }
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Log de seguridad
 */
function security_log($message, $level = 'INFO') {
    $log_message = date('Y-m-d H:i:s') . " [$level] " . $message . PHP_EOL;
    error_log($log_message, 3, '../logs/security.log');
}

/**
 * Limpiar datos de entrada para SQL
 */
function clean_sql_input($data) {
    return trim(strip_tags($data));
}

/**
 * Verificar si es una petición AJAX
 */
function is_ajax_request() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Redirigir con mensaje
 */
function redirect_with_message($url, $message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit();
}

/**
 * Mostrar mensaje si existe
 */
function show_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        unset($_SESSION['message'], $_SESSION['message_type']);
        
        $alert_class = '';
        switch ($type) {
            case 'success': $alert_class = 'alert-success'; break;
            case 'error': $alert_class = 'alert-danger'; break;
            case 'warning': $alert_class = 'alert-warning'; break;
            default: $alert_class = 'alert-info';
        }
        
        echo "<div class='alert $alert_class' role='alert'>$message</div>";
    }
}
?>
