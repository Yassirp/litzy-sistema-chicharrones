<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Incluir conexión a la base de datos
include "../config/db.php";

// Obtener estadísticas reales
$ventas_hoy = 0;
$transacciones_hoy = 0;
$total_productos = 0;

try {
    // Ventas de hoy (monto total)
    $sql_ventas = "SELECT ISNULL(SUM(monto), 0) as total_ventas 
                   FROM ventas 
                   WHERE CAST(fecha AS DATE) = CAST(GETDATE() AS DATE)";
    $stmt_ventas = sqlsrv_query($conn, $sql_ventas);
    if ($stmt_ventas === false) {
        throw new Exception("Error en consulta de ventas: " . print_r(sqlsrv_errors(), true));
    }
    if ($row = sqlsrv_fetch_array($stmt_ventas, SQLSRV_FETCH_ASSOC)) {
        $ventas_hoy = $row['total_ventas'];
    }
    
    // Número de transacciones de hoy
    $sql_transacciones = "SELECT COUNT(*) as total_transacciones 
                          FROM ventas 
                          WHERE CAST(fecha AS DATE) = CAST(GETDATE() AS DATE)";
    $stmt_transacciones = sqlsrv_query($conn, $sql_transacciones);
    if ($stmt_transacciones === false) {
        throw new Exception("Error en consulta de transacciones: " . print_r(sqlsrv_errors(), true));
    }
    if ($row = sqlsrv_fetch_array($stmt_transacciones, SQLSRV_FETCH_ASSOC)) {
        $transacciones_hoy = $row['total_transacciones'];
    }
    
    // Total de productos activos
    $sql_productos = "SELECT COUNT(*) as total_productos 
                      FROM productos 
                      WHERE estado = 1";
    $stmt_productos = sqlsrv_query($conn, $sql_productos);
    if ($stmt_productos === false) {
        throw new Exception("Error en consulta de productos: " . print_r(sqlsrv_errors(), true));
    }
    if ($row = sqlsrv_fetch_array($stmt_productos, SQLSRV_FETCH_ASSOC)) {
        $total_productos = $row['total_productos'];
    }
    
    // Retornar datos en formato JSON
    echo json_encode([
        'success' => true,
        'ventas_hoy' => (float)$ventas_hoy,
        'transacciones_hoy' => (int)$transacciones_hoy,
        'total_productos' => (int)$total_productos,
        'debug' => [
            'fecha_actual' => date('Y-m-d'),
            'ventas_hoy_raw' => $ventas_hoy,
            'transacciones_hoy_raw' => $transacciones_hoy
        ]
    ]);
    
} catch (Exception $e) {
    // En caso de error, retornar valores en 0 con información de debug
    echo json_encode([
        'success' => false,
        'ventas_hoy' => 0,
        'transacciones_hoy' => 0,
        'total_productos' => 0,
        'error' => $e->getMessage()
    ]);
}
?>
