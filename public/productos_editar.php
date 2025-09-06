<?php
require_once "../config/db.php";

$id = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
$precio = isset($_POST['precio']) ? str_replace(',', '.', $_POST['precio']) : '0';
$stock  = isset($_POST['stock']) ? $_POST['stock'] : '0';

if ($id <= 0 || $nombre === '' || !is_numeric($precio) || !is_numeric($stock)) {
    http_response_code(400);
    echo "Datos inválidos";
    exit;
}

$sql = "UPDATE productos
        SET nombre = ?, descripcion = ?, precio = ?, stock = ?
        WHERE id_producto = ?";
$params = [$nombre, $descripcion, (float)$precio, (int)$stock, $id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(500);
    echo "SQL error: " . print_r(sqlsrv_errors(), true);
    exit;
}

echo "ok";
