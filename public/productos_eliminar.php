<?php
require_once "../config/db.php";

$id = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo "ID inválido";
    exit;
}

// Borrado lógico (recomendado)
$sql = "UPDATE productos SET estado = 0 WHERE id_producto = ?";
$params = [$id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(500);
    echo "SQL error: " . print_r(sqlsrv_errors(), true);
    exit;
}

echo "ok";
