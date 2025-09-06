<?php
session_start();
include '../config/db.php';
include '../includes/security.php';

// Verificar sesión activa
check_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productos = json_decode($_POST['productos'] ?? '[]', true);
    $total = sanitize_input($_POST['total'] ?? '0');

    // Validar entrada
    if (!$productos || count($productos) === 0) {
        echo "No se recibieron productos.";
        exit;
    }

    if (!validate_positive_float($total)) {
        echo "Total inválido.";
        exit;
    }

    // Iniciar transacción
    sqlsrv_begin_transaction($conn);

    try {
        // Verificar stock disponible antes de procesar la venta
        foreach ($productos as $producto) {
            $sql_check = "SELECT stock FROM productos WHERE id_producto = ? AND estado = 1";
            $stmt_check = sqlsrv_query($conn, $sql_check, [$producto['id']]);
            
            if ($stmt_check === false) {
                throw new Exception("Error al verificar stock: " . print_r(sqlsrv_errors(), true));
            }
            
            $row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
            if (!$row || $row['stock'] < $producto['cantidad']) {
                throw new Exception("Stock insuficiente para: " . $producto['nombre']);
            }
        }

        // Preparar datos para la venta
        $usuario_id = 1; // cambiar si manejas sesión de usuario
        $descripcion = implode(", ", array_map(function($p) {
            return $p['cantidad'] . "x " . $p['nombre'];
        }, $productos));

        // INSERTAR en la tabla ventas y obtener el ID usando OUTPUT
        $sql_venta_con_output = "INSERT INTO ventas (usuario_id, descripcion, monto, fecha) 
                                 OUTPUT INSERTED.id 
                                 VALUES (?, ?, ?, GETDATE())";
        $params_venta_output = [$usuario_id, $descripcion, $total];
        $stmt_venta_output = sqlsrv_query($conn, $sql_venta_con_output, $params_venta_output);
        
        if ($stmt_venta_output === false) {
            throw new Exception("Error al insertar venta con OUTPUT: " . print_r(sqlsrv_errors(), true));
        }
        
        // Obtener el ID directamente del resultado
        $row_venta = sqlsrv_fetch_array($stmt_venta_output, SQLSRV_FETCH_ASSOC);
        $venta_id = $row_venta['id'];
        
        // Verificar que se obtuvo el ID
        if (!$venta_id || $venta_id <= 0) {
            throw new Exception("No se pudo obtener el ID de la venta creada. ID obtenido: " . var_export($venta_id, true));
        }

        // Insertar detalle de ventas y descontar stock
        foreach ($productos as $producto) {
            // Insertar detalle de venta
            $sql_detalle = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal) 
                           VALUES (?, ?, ?, ?, ?)";
            $params_detalle = [
                $venta_id, 
                $producto['id'], 
                $producto['cantidad'], 
                $producto['precio'], 
                $producto['subtotal']
            ];
            $stmt_detalle = sqlsrv_query($conn, $sql_detalle, $params_detalle);

            if ($stmt_detalle === false) {
                throw new Exception("Error al insertar detalle: " . print_r(sqlsrv_errors(), true));
            }

            // Descontar stock
            $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
            $params_stock = [$producto['cantidad'], $producto['id']];
            $stmt_stock = sqlsrv_query($conn, $sql_stock, $params_stock);

            if ($stmt_stock === false) {
                throw new Exception("Error al actualizar stock: " . print_r(sqlsrv_errors(), true));
            }
        }

        // Confirmar transacción
        sqlsrv_commit($conn);
        echo "✅ Venta registrada con éxito. Stock actualizado.";

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        sqlsrv_rollback($conn);
        echo "❌ Error: " . $e->getMessage();
    }
}
