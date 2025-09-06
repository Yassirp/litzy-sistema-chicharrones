<?php
// Si usas una plantilla general, puedes incluirla aquí:
// include("dashboard.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">    
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <link rel="stylesheet" href="../css/app-base.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>Gestión de Productos - Litzy</title>
</head>
<body>
  <div class="app-container">
    <!-- Header -->
    <header class="app-header">
      <div class="header-content">
        <div class="user-info">
          <div class="user-avatar">
            <i class="fas fa-box"></i>
          </div>
          <div class="user-details">
            <h1>📦 Gestión de Productos</h1>
            <p>Administra el catálogo de productos</p>
          </div>
        </div>
        <div class="header-actions">
          <button class="btn-modern success" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="fas fa-plus"></i>
            Agregar Producto
          </button>
        </div>
      </div>
    </header>

    <!-- Tabla de productos -->
    <section class="section">
      <h2 class="section-title">Lista de Productos</h2>
      <div id="tabla"></div>
    </section>
  </div>

  <!-- Modal Agregar -->
  <div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formAgregar" class="modal-content" method="POST" enctype="multipart/form-data" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div class="modal-header" style="background: linear-gradient(135deg, #ff6a00, #ff8c00); color: white; border-radius: 20px 20px 0 0; border: none;">
          <h5 class="modal-title" style="font-weight: 700;">Agregar Producto</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body" style="padding: 2rem;">
          <div class="form-group">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Descripción (opcional)</label>
            <input type="text" name="descripcion" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="precio" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Imagen</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
          </div>
        </div>
        <div class="modal-footer" style="border: none; padding: 1rem 2rem 2rem;">
          <button type="button" class="btn-modern secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn-modern success" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar -->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formEditar" class="modal-content" method="POST" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div class="modal-header" style="background: linear-gradient(135deg, #ff6a00, #ff8c00); color: white; border-radius: 20px 20px 0 0; border: none;">
          <h5 class="modal-title" style="font-weight: 700;">Editar Producto</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body" style="padding: 2rem;">
          <input type="hidden" name="id_producto" id="edit_id">
          <div class="form-group">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Descripción (opcional)</label>
            <input type="text" name="descripcion" id="edit_descripcion" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="precio" id="edit_precio" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" id="edit_stock" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer" style="border: none; padding: 1rem 2rem 2rem;">
          <button type="button" class="btn-modern secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn-modern success" type="submit">Actualizar</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const modalAgregarEl = document.getElementById('modalAgregar');
    const modalEditarEl  = document.getElementById('modalEditar');
    const modalAgregar = new bootstrap.Modal(modalAgregarEl);
    const modalEditar  = new bootstrap.Modal(modalEditarEl);

    function cargarTabla(){
      $("#tabla").load("productos_listar.php");
    }
    cargarTabla();

    // Agregar con imagen (FormData)
    $("#formAgregar").on("submit", function(e){
      e.preventDefault();
      let formData = new FormData(this);

      $.ajax({
        url: "productos_agregar.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(res){
          if(res.trim()==="ok"){
            modalAgregar.hide();
            $("#formAgregar")[0].reset();
            cargarTabla();
          }else{
            alert("Error al agregar: " + res);
          }
        },
        error: function(xhr){
          alert("Error servidor: " + xhr.responseText);
        }
      });
    });

    // Abrir modal editar con datos
    $(document).on("click",".btn-editar",function(){
      $("#edit_id").val($(this).data("id"));
      $("#edit_nombre").val($(this).data("nombre"));
      $("#edit_descripcion").val($(this).data("descripcion"));
      $("#edit_precio").val($(this).data("precio"));
      $("#edit_stock").val($(this).data("stock"));
      modalEditar.show();
    });

    // Actualizar
    $("#formEditar").on("submit", function(e){
      e.preventDefault();
      $.post("productos_editar.php", $(this).serialize())
        .done(function(res){
          if(res.trim()==="ok"){
            modalEditar.hide();
            cargarTabla();
          }else{
            alert("Error al actualizar: " + res);
          }
        })
        .fail(function(xhr){ alert("Error servidor: " + xhr.responseText); });
    });

    // Eliminar
    $(document).on("click",".btn-eliminar",function(){
      if(!confirm("¿Eliminar este producto?")) return;
      $.post("productos_eliminar.php", { id_producto: $(this).data("id") })
        .done(function(res){
          if(res.trim()==="ok"){
            cargarTabla();
          }else{
            alert("Error al eliminar: " + res);
          }
        })
        .fail(function(xhr){ alert("Error servidor: " + xhr.responseText); });
    });
  </script>

  <!-- Navbar fijo abajo -->
  <?php include "../includes/navbar.php"; ?>
</body>
</html>
