<?php
require_once "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $imagen = null;
    if (!empty($_FILES['imagen']['name'])) {
        $carpeta = "../uploads/";
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        $nombreArchivo = time() . "_" . basename($_FILES['imagen']['name']);
        $rutaDestino = $carpeta . $nombreArchivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagen = $nombreArchivo;
        }
    }

    $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen, estado, fecha_registro)
            VALUES (?, ?, ?, ?, ?, 1, GETDATE())";

    $params = [$nombre, $descripcion, $precio, $stock, $imagen];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo "ok"; // ✅ ahora solo devuelve "ok"
    } else {
        echo "error: " . print_r(sqlsrv_errors(), true);
    }
}
?>
