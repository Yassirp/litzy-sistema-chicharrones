<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    $costo_unidad = $_POST['costo_unidad'] ?? 0;
    $proveedor = $_POST['proveedor'] ?? '';

    // Validar datos
    if (empty($producto_id) || empty($cantidad) || $cantidad <= 0) {
        echo "Datos inválidos";
        exit;
    }

    // Iniciar transacción
    sqlsrv_begin_transaction($conn);

    try {
        // Verificar que el producto existe
        $sql_check = "SELECT id_producto, nombre, stock FROM productos WHERE id_producto = ? AND estado = 1";
        $stmt_check = sqlsrv_query($conn, $sql_check, [$producto_id]);
        
        if ($stmt_check === false) {
            throw new Exception("Error al verificar producto: " . print_r(sqlsrv_errors(), true));
        }
        
        $row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
        if (!$row) {
            throw new Exception("Producto no encontrado");
        }

        // Actualizar stock
        $sql_update = "UPDATE productos SET stock = stock + ? WHERE id_producto = ?";
        $params_update = [$cantidad, $producto_id];
        $stmt_update = sqlsrv_query($conn, $sql_update, $params_update);

        if ($stmt_update === false) {
            throw new Exception("Error al actualizar stock: " . print_r(sqlsrv_errors(), true));
        }

        // Registrar movimiento de inventario (opcional - puedes crear esta tabla después)
        if (!empty($costo_unidad) || !empty($proveedor)) {
            $sql_movimiento = "INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, costo_unidad, proveedor, fecha, usuario_id) 
                              VALUES (?, 'ENTRADA', ?, ?, ?, GETDATE(), 1)";
            $params_movimiento = [$producto_id, $cantidad, $costo_unidad, $proveedor];
            $stmt_movimiento = sqlsrv_query($conn, $sql_movimiento, $params_movimiento);
            
            // Si falla el movimiento, no es crítico, solo continuamos
            if ($stmt_movimiento === false) {
                // Log del error pero no fallar la transacción
                error_log("Error al registrar movimiento: " . print_r(sqlsrv_errors(), true));
            }
        }

        // Confirmar transacción
        sqlsrv_commit($conn);
        echo "ok";

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        sqlsrv_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Método no permitido";
}
?>
