<?php
require_once "../config/db.php";

$sql = "SELECT id_producto, nombre, descripcion, precio, stock
        FROM productos
        WHERE estado = 1
        ORDER BY id_producto DESC";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    http_response_code(500);
    die("SQL error: " . print_r(sqlsrv_errors(), true));
}
?>
<table class="table table-striped table-bordered align-middle">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Descripción</th>
      <th class="text-end">Precio</th>
      <th class="text-end">Stock</th>
      <th style="width:160px;">Acciones</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
    <tr>
      <td><?= htmlspecialchars($row['id_producto']) ?></td>
      <td><?= htmlspecialchars($row['nombre']) ?></td>
      <td><?= htmlspecialchars($row['descripcion']) ?></td>
      <td class="text-end">$<?= number_format((float)$row['precio'], 2) ?></td>
      <td class="text-end"><?= (int)$row['stock'] ?></td>
      <td>
        <button
          class="btn btn-warning btn-sm btn-editar"
          data-id="<?= htmlspecialchars($row['id_producto']) ?>"
          data-nombre="<?= htmlspecialchars($row['nombre']) ?>"
          data-descripcion="<?= htmlspecialchars($row['descripcion']) ?>"
          data-precio="<?= htmlspecialchars($row['precio']) ?>"
          data-stock="<?= htmlspecialchars($row['stock']) ?>"
        >Editar</button>
        <button class="btn btn-danger btn-sm btn-eliminar"
          data-id="<?= htmlspecialchars($row['id_producto']) ?>">Eliminar</button>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
