<?php
include '../config/db.php'; 

// Traer todos los productos con stock
$sql = "SELECT id_producto, nombre, precio, stock, imagen FROM productos WHERE estado = 1 AND stock > 0";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
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
    
    <!-- Preload de imágenes críticas -->
    <link rel="preload" as="image" href="../uploads/1757139442_coca_cola.webp">
    <link rel="preload" as="image" href="../uploads/1757139668_picada-chorizo.webp">
    <link rel="preload" as="image" href="../uploads/1757139795_chicharron.webp">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Ventas - Litzy</title>
    
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="app-header">
            <div class="header-content">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="user-details">
                        <h1>🛒 Registrar Venta</h1>
                        <p>Selecciona productos para la venta</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="dashboard.php" class="btn-modern secondary">
                        <i class="fas fa-home"></i>
                        Inicio
                    </a>
                </div>
            </div>
        </header>
        
        <!-- Sección de productos -->
        <section class="section">
            <h2 class="section-title">Productos Disponibles</h2>
            <div class="productos-grid" id="productos-lista">
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { ?>
                    <div class="card-modern producto-card"
                    data-id="<?php echo $row['id_producto']; ?>" 
                    data-nombre="<?php echo $row['nombre']; ?>" 
                    data-precio="<?php echo $row['precio']; ?>"
                    data-stock="<?php echo $row['stock']; ?>">
                    
                    <?php if (!empty($row['imagen'])) { ?>
                        <?php 
                        $finalPath = "../uploads/" . $row['imagen'];
                        ?>
                        <?php
                        // Para móviles, usar JPG directamente (más compatible)
                        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                        $isMobile = preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/', $userAgent);
                        
                        if ($isMobile) {
                            // Móviles: usar JPG directamente
                            $imagePath = str_replace('.webp', '.jpg', $finalPath);
                        } else {
                            // Escritorio: usar WebP con fallback
                            $imagePath = $finalPath;
                        }
                        ?>
                        
                        <?php if ($isMobile): ?>
                            <img src="<?php echo $imagePath; ?>" 
                            style="width: 100%; height: 150px; object-fit: cover; border-radius: 12px; margin-bottom: 1rem;"
                            loading="eager" decoding="async" alt="<?php echo $row['nombre']; ?>">
                        <?php else: ?>
                            <picture>
                                <source srcset="<?php echo $finalPath; ?>" type="image/webp">
                                <img src="<?php echo str_replace('.webp', '.jpg', $finalPath); ?>" 
                                style="width: 100%; height: 150px; object-fit: cover; border-radius: 12px; margin-bottom: 1rem;"
                                loading="eager" decoding="async" alt="<?php echo $row['nombre']; ?>">
                            </picture>
                        <?php endif; ?>
                        <?php } else { ?>
                        <div style="width: 100%; height: 150px; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 12px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="font-size: 2rem; color: #6c757d;"></i>
                        </div>
                        <?php } ?>
                        
                    <div style="text-align: center;">
                        <h5 style="font-weight: 700; color: #333; margin-bottom: 0.5rem; font-size: 1rem;"><?php echo $row['nombre']; ?></h5>
                        <p style="color: #ff6a00; font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">
                            $<?php echo number_format($row['precio'], 0, ',', '.'); ?>
                        </p>
                        <div style="margin-bottom: 1rem;">
                            <span class="badge-modern <?php echo $row['stock'] <= 5 ? 'danger' : ($row['stock'] <= 10 ? 'warning' : 'success'); ?>">
                                Stock: <?php echo $row['stock']; ?>
                            </span>
                        </div>
                        <button class="btn-modern btn-agregar" style="width: 100%;">
                            <i class="fas fa-plus"></i>
                            Agregar
                        </button>
                    </div>
                </div>
                <?php } ?>
            </div>
        </section>
        
        <!-- Tabla de venta -->
        <section class="section">
            <h2 class="section-title">📝 Productos en la Venta</h2>
            <div class="table-modern">
                <table id="tabla-venta">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <!-- Total -->
            <div class="card-modern total-section" style="margin-top: 1rem; background: linear-gradient(135deg, #28a745, #20c997); color: white; text-align: center;">
                <h3 style="color: white; margin-bottom: 0.5rem;">
                    <i class="fas fa-calculator" style="margin-right: 0.5rem;"></i>
                    Total de la Venta
                </h3>
                <p style="font-size: 2.5rem; font-weight: 700; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    $<span id="total">0</span>
                </p>
                <button id="finalizar" class="btn-modern" style="margin-top: 1rem; background: rgba(255,255,255,0.2); border: 2px solid white; color: white; font-size: 1.1rem; padding: 1rem 2rem;">
                    <i class="fas fa-check-circle"></i>
                    Finalizar Compra
                </button>
            </div>
        </section>
    </div>
    
   
    
    <!-- Script de lógica -->
  <script>
    const formatoPesos = new Intl.NumberFormat('es-CO');
    let total = 0;

    $(document).on("click", ".btn-agregar", function() {
        const card = $(this).closest(".producto-card");
        const id = card.data("id");
        const nombre = card.data("nombre");
        const precio = parseFloat(card.data("precio"));
        const stockDisponible = parseInt(card.data("stock"));
        
        let row = $("#fila-" + id);
        if (row.length > 0) {
            let cantidadInput = row.find(".cantidad");
            let cantidad = parseInt(cantidadInput.val()) + 1;
            
            if (cantidad > stockDisponible) {
                alert(`No hay suficiente stock. Disponible: ${stockDisponible}`);
                return;
            }
            
            cantidadInput.val(cantidad);
            cantidadInput.attr("max", stockDisponible);
            row.find(".subtotal").text(formatoPesos.format(precio * cantidad));
        } else {
            let fila = `
            <tr id="fila-${id}">
                <td>${nombre}</td>
                <td>${formatoPesos.format(precio)}</td>
                <td><input type="number" class="form-control cantidad" value="1" min="1" max="${stockDisponible}" style="width:80px"></td>
                <td class="subtotal">${formatoPesos.format(precio)}</td>
                <td><button class="btn-eliminar">Eliminar</button></td>
            </tr>
            `;
            $("#tabla-venta tbody").append(fila);
        }
        calcularTotal();
    });

    $(document).on("input", ".cantidad", function() {
        let row = $(this).closest("tr");
        let precio = parseFloat(row.find("td:nth-child(2)").text().replace(/\./g, "").replace(",", "."));
        let cantidad = parseInt($(this).val());
        let maxStock = parseInt($(this).attr("max"));
        
        if (cantidad > maxStock) {
            alert(`No hay suficiente stock. Disponible: ${maxStock}`);
            $(this).val(maxStock);
            cantidad = maxStock;
        }
        
        if (cantidad < 1) {
            $(this).val(1);
            cantidad = 1;
        }
        
        row.find(".subtotal").text(formatoPesos.format(precio * cantidad));
        calcularTotal();
    });

    $(document).on("click", ".btn-eliminar", function() {
        $(this).closest("tr").remove();
        calcularTotal();
    });

    function calcularTotal() {
        total = 0;
        $("#tabla-venta tbody tr").each(function() {
            let subtotal = $(this).find(".subtotal").text().replace(/\./g, "").replace(",", ".");
            total += parseFloat(subtotal);
        });
        $("#total").text(formatoPesos.format(total));
    }

    $("#finalizar").click(function() {
        let productos = [];
        $("#tabla-venta tbody tr").each(function() {
            let id = $(this).attr("id").replace("fila-", "");
            let nombre = $(this).find("td:first").text();
            let precio = parseFloat($(this).find("td:nth-child(2)").text().replace(/\./g, "").replace(",", "."));
            let cantidad = parseInt($(this).find(".cantidad").val());
            let subtotal = parseFloat($(this).find(".subtotal").text().replace(/\./g, "").replace(",", "."));

            productos.push({ id, nombre, precio, cantidad, subtotal });
        });

        if (productos.length === 0) {
            alert("Agrega productos antes de finalizar la compra.");
            return;
        }

        $.ajax({
            url: "guardar_venta.php",
            method: "POST",
            data: { productos: JSON.stringify(productos), total: total },
            success: function(respuesta) {
                alert(respuesta);
                location.reload();
            }
        });
    });
    
    // Mejorar la experiencia de scroll
    $(document).ready(function() {
        // Asegurar que el contenido sea scrolleable
        $('body').css('overflow-x', 'hidden');
        
        // Scroll suave al hacer clic en el botón finalizar
        $('#finalizar').on('click', function() {
            // Scroll al botón para asegurar que esté visible
            $('html, body').animate({
                scrollTop: $(this).offset().top - 100
            }, 300);
        });
        
        // Asegurar que el botón esté siempre visible al final
        $(window).on('scroll', function() {
            const finalizarBtn = $('#finalizar');
            const windowHeight = $(window).height();
            const documentHeight = $(document).height();
            const scrollTop = $(window).scrollTop();
            
            // Si estamos cerca del final, asegurar que el botón sea visible
            if (scrollTop + windowHeight >= documentHeight - 50) {
                finalizarBtn.css('position', 'relative');
            }
        });
    });
</script>


    <!-- Navbar fijo abajo -->
    <?php include "../includes/navbar.php"; ?>
</body>
</html>
