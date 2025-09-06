<?php
include '../config/db.php';

// Traer todos los productos con su stock actual
$sql = "SELECT id_producto, nombre, descripcion, precio, stock, imagen, fecha_registro
        FROM productos 
        WHERE estado = 1
        ORDER BY fecha_registro DESC";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Almacenar los productos en un array para reutilizar
$productos = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $productos[] = $row;
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Inventario - Litzy</title>
    
    <!-- Estilos para mejorar el scroll -->
    <style>
        /* Mejorar el scroll en la página de inventario */
        body {
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch !important;
        }
        
        .app-container {
            padding-bottom: 120px !important; /* Espacio extra para el navbar */
        }
        
        /* Asegurar que el contenido sea scrolleable */
        .section {
            margin-bottom: 2rem !important;
        }
        
        /* Mejorar el espaciado de la tabla */
        .table-modern {
            margin-bottom: 2rem !important;
        }
        
        /* Mejorar el espaciado de las cards móviles */
        .mobile-cards {
            margin-bottom: 2rem !important;
        }
        
        /* Asegurar que los botones sean visibles */
        .btn-agregar-stock {
            margin-bottom: 1rem !important;
            position: relative !important;
            z-index: 10 !important;
        }
        
        /* Mejorar el scroll en móvil */
        @media (max-width: 768px) {
            .app-container {
                padding-bottom: 100px !important;
            }
            
            .section {
                margin-bottom: 1.5rem !important;
            }
            
            .table-modern, .mobile-cards {
                margin-bottom: 1.5rem !important;
            }
            
            .producto-card-mobile {
                margin-bottom: 1rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="app-header">
            <div class="header-content">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="user-details">
                        <h1>📦 Gestión de Inventario</h1>
                        <p>Administra productos y stock</p>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="btn-modern success" data-bs-toggle="modal" data-bs-target="#modalAgregarStock">
                        <i class="fas fa-plus"></i>
                        Agregar Stock
                    </button>
                </div>
            </div>
        </header>
        
        
        <!-- Tabla de inventario -->
        <section class="section">
            <h2 class="section-title">Productos en Inventario</h2>
            
            <!-- Alertas de stock bajo -->
            <div id="alertas-stock"></div>

            <!-- Vista de escritorio -->
            <div class="table-modern desktop-table">
                <table>
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $row): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['imagen'])) { ?>
                                    <img src="../uploads/<?php echo $row['imagen']; ?>" 
                                         style="width:50px; height:50px; object-fit:cover; border-radius: 8px;">
                                <?php } else { ?>
                                    <div style="width:50px; height:50px; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="color: #6c757d;"></i>
                                    </div>
                                <?php } ?>
                            </td>
                            <td style="font-weight: 700;"><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                            <td style="font-weight: 700; color: #ff6a00;">
                                $<?php echo number_format($row['precio'], 0, ',', '.'); ?>
                            </td>
                            <td>
                                <span class="badge-modern <?php echo $row['stock'] <= 5 ? 'danger' : ($row['stock'] <= 10 ? 'warning' : 'success'); ?>">
                                    <?php echo $row['stock']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['stock'] <= 0): ?>
                                    <span class="badge-modern danger">Agotado</span>
                                <?php elseif ($row['stock'] <= 5): ?>
                                    <span class="badge-modern warning">Stock Bajo</span>
                                <?php else: ?>
                                    <span class="badge-modern success">Disponible</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn-modern btn-agregar-stock" 
                                        data-id="<?php echo $row['id_producto']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>"
                                        data-stock="<?php echo $row['stock']; ?>">
                                    <i class="fas fa-plus"></i>
                                    Agregar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Vista móvil -->
            <div class="mobile-cards">
                <?php foreach ($productos as $row): ?>
                <div class="producto-card-mobile">
                    <div class="producto-header">
                        <div class="producto-imagen">
                            <?php if (!empty($row['imagen'])) { ?>
                                <img src="../uploads/<?php echo $row['imagen']; ?>" 
                                     style="width:60px; height:60px; object-fit:cover; border-radius: 12px;">
                            <?php } else { ?>
                                <div style="width:60px; height:60px; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="color: #6c757d; font-size: 1.2rem;"></i>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="producto-info">
                            <h3 class="producto-nombre"><?php echo htmlspecialchars($row['nombre']); ?></h3>
                            <p class="producto-descripcion"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        </div>
                        <div class="producto-precio">
                            <span class="precio">$<?php echo number_format($row['precio'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <div class="producto-details">
                        <div class="detail-row">
                            <span class="detail-label">Stock:</span>
                            <span class="badge-modern <?php echo $row['stock'] <= 5 ? 'danger' : ($row['stock'] <= 10 ? 'warning' : 'success'); ?>">
                                <?php echo $row['stock']; ?> unidades
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Estado:</span>
                            <?php if ($row['stock'] <= 0): ?>
                                <span class="badge-modern danger">Agotado</span>
                            <?php elseif ($row['stock'] <= 5): ?>
                                <span class="badge-modern warning">Stock Bajo</span>
                            <?php else: ?>
                                <span class="badge-modern success">Disponible</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="producto-actions">
                        <button class="btn-modern success btn-agregar-stock" 
                                data-id="<?php echo $row['id_producto']; ?>"
                                data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>"
                                data-stock="<?php echo $row['stock']; ?>">
                            <i class="fas fa-plus"></i>
                            Agregar Stock
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- Modal Agregar Stock -->
    <div class="modal fade" id="modalAgregarStock" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formAgregarStock" class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #ff6a00, #ff8c00); color: white; border-radius: 20px 20px 0 0; border: none;">
                    <h5 class="modal-title" style="font-weight: 700;">Agregar Stock al Inventario</h5>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div class="form-group">
                        <label class="form-label">Producto</label>
                        <select name="producto_id" id="selectProducto" class="form-select" required>
                            <option value="">Selecciona un producto</option>
                            <?php foreach ($productos as $row): ?>
                                <option value="<?php echo $row['id_producto']; ?>" 
                                        data-stock="<?php echo $row['stock']; ?>">
                                    <?php echo htmlspecialchars($row['nombre']); ?> 
                                    (Stock actual: <?php echo $row['stock']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cantidad a Agregar</label>
                        <input type="number" name="cantidad" id="cantidadAgregar" 
                               class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Costo por Unidad (opcional)</label>
                        <input type="number" name="costo_unidad" id="costoUnidad" 
                               class="form-control" step="0.01" min="0">
                        <small style="color: #666; font-size: 0.8rem;">Para llevar registro de costos de compra</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Proveedor (opcional)</label>
                        <input type="text" name="proveedor" id="proveedor" 
                               class="form-control" placeholder="Nombre del proveedor">
                    </div>
                </div>
                <div class="modal-footer" style="border: none; padding: 1rem 2rem 2rem;">
                    <button type="button" class="btn-modern secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-modern success">Agregar Stock</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Agregar Stock Individual -->
    <div class="modal fade" id="modalAgregarStockIndividual" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formAgregarStockIndividual" class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #ff6a00, #ff8c00); color: white; border-radius: 20px 20px 0 0; border: none;">
                    <h5 class="modal-title" style="font-weight: 700;">Agregar Stock</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <input type="hidden" name="producto_id" id="productoIdIndividual">
                    <div class="form-group">
                        <label class="form-label">Producto</label>
                        <input type="text" id="nombreProductoIndividual" class="form-control" readonly style="background: #f8f9fa;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock Actual</label>
                        <input type="text" id="stockActualIndividual" class="form-control" readonly style="background: #f8f9fa;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cantidad a Agregar</label>
                        <input type="number" name="cantidad" id="cantidadIndividual" 
                               class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Costo por Unidad (opcional)</label>
                        <input type="number" name="costo_unidad" id="costoUnidadIndividual" 
                               class="form-control" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Proveedor (opcional)</label>
                        <input type="text" name="proveedor" id="proveedorIndividual" 
                               class="form-control" placeholder="Nombre del proveedor">
                    </div>
                </div>
                <div class="modal-footer" style="border: none; padding: 1rem 2rem 2rem;">
                    <button type="button" class="btn-modern secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-modern success">Agregar Stock</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Estilos para las alertas -->
    <style>
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #alertas-stock {
            margin-bottom: 1.5rem;
        }
        
        #alertas-stock .card-modern {
            transition: all 0.3s ease;
        }
        
        #alertas-stock .card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        /* === RESPONSIVE DESIGN === */
        
        /* Por defecto, mostrar solo la tabla */
        .desktop-table {
            display: block;
        }
        
        .mobile-cards {
            display: none;
        }
        
        /* En móvil, ocultar tabla y mostrar cards */
        @media (max-width: 768px) {
            .desktop-table {
                display: none !important;
            }
            .mobile-cards {
                display: block !important;
            }
        }
        
        /* === ESTILOS PARA CARDS MÓVILES === */
        
        .mobile-cards {
            gap: 1rem;
        }
        
        .producto-card-mobile {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .producto-card-mobile::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #ff6a00, #ff8c00);
        }
        
        .producto-card-mobile:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .producto-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .producto-imagen {
            flex-shrink: 0;
        }
        
        .producto-info {
            flex: 1;
            min-width: 0;
        }
        
        .producto-nombre {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 0.5rem 0;
            line-height: 1.3;
        }
        
        .producto-descripcion {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .producto-precio {
            flex-shrink: 0;
            text-align: right;
        }
        
        .precio {
            font-size: 1.2rem;
            font-weight: 700;
            color: #ff6a00;
            background: linear-gradient(135deg, #fff5f0, #ffe8d6);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            display: inline-block;
        }
        
        .producto-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .detail-row:last-child {
            margin-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .producto-actions {
            display: flex;
            justify-content: center;
        }
        
        .producto-actions .btn-modern {
            width: 100%;
            justify-content: center;
            font-weight: 600;
        }
        
        /* === MEJORAS PARA TABLA ESCRITORIO === */
        
        .desktop-table {
            overflow-x: auto;
        }
        
        .desktop-table table {
            min-width: 800px;
        }
        
        /* === ANIMACIONES === */
        
        .producto-card-mobile {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        .producto-card-mobile:nth-child(1) { animation-delay: 0.1s; }
        .producto-card-mobile:nth-child(2) { animation-delay: 0.2s; }
        .producto-card-mobile:nth-child(3) { animation-delay: 0.3s; }
        .producto-card-mobile:nth-child(4) { animation-delay: 0.4s; }
        .producto-card-mobile:nth-child(5) { animation-delay: 0.5s; }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* === FIX PARA MODALES === */
        
        .modal {
            z-index: 99999 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(0,0,0,0.5) !important;
            display: none !important;
            overflow-y: auto !important;
            padding: 10px !important;
        }
        
        .modal.show {
            display: block !important;
        }
        
        .modal-backdrop {
            z-index: 99998 !important;
        }
        
        .modal-dialog {
            z-index: 100000 !important;
            position: relative !important;
            margin: 0 auto !important;
            max-width: 95% !important;
            width: 500px !important;
            margin-top: 10px !important;
            margin-bottom: 10px !important;
            min-height: auto !important;
        }
        
        .modal-content {
            position: relative !important;
            z-index: 100001 !important;
            max-height: none !important;
            overflow-y: visible !important;
            background: white !important;
            border: none !important;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3) !important;
            border-radius: 20px !important;
        }
        
        /* Ocultar modales por defecto */
        .modal:not(.show) {
            display: none !important;
        }
        
        /* === RESPONSIVE PARA MODALES === */
        
        @media (max-width: 768px) {
            .modal {
                padding: 5px !important;
            }
            
            .modal-dialog {
                width: 98% !important;
                max-width: 98% !important;
                margin-top: 5px !important;
                margin-bottom: 5px !important;
            }
            
            .modal-content {
                max-height: none !important;
                border-radius: 16px !important;
            }
            
            .modal-body {
                padding: 1rem !important;
                max-height: none !important;
                overflow-y: visible !important;
            }
        }
        
        /* Mejorar el scroll en móvil */
        .modal-body {
            -webkit-overflow-scrolling: touch !important;
            overflow-y: visible !important;
        }
        
        /* Permitir scroll completo en el modal */
        .modal {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        
        /* Asegurar que el header sea sólido */
        .modal-header {
            background: linear-gradient(135deg, #ff6a00, #ff8c00) !important;
            color: white !important;
            border-radius: 20px 20px 0 0 !important;
            border: none !important;
            padding: 1.5rem !important;
        }
        
        /* Asegurar que el body sea sólido */
        .modal-body {
            background: white !important;
            padding: 2rem !important;
        }
        
        /* Asegurar que el footer sea sólido */
        .modal-footer {
            background: white !important;
            border: none !important;
            padding: 1rem 2rem 2rem !important;
        }
        
        /* Estilos para los campos del formulario */
        .modal .form-control, .modal .form-select {
            background: white !important;
            border: 2px solid #e9ecef !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem !important;
            font-size: 1rem !important;
            color: #495057 !important;
        }
        
        .modal .form-control:focus, .modal .form-select:focus {
            border-color: #ff6a00 !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 106, 0, 0.25) !important;
        }
        
        .modal .form-label {
            color: #495057 !important;
            font-weight: 600 !important;
            margin-bottom: 0.5rem !important;
        }
        
        .modal .btn-modern {
            background: linear-gradient(135deg, #ff6a00, #ff8c00) !important;
            border: none !important;
            color: white !important;
            font-weight: 600 !important;
        }
        
        .modal .btn-modern.secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268) !important;
        }
    </style>
    
    <script>
        // Verificar stock bajo al cargar la página
        $(document).ready(function() {
            // Solo ejecutar una vez
            if (!window.stockVerificado) {
                verificarStockBajo();
                window.stockVerificado = true;
            }
            ajustarVista();
            inicializarModales();
            mejorarScroll();
        });
        
        // Mejorar la experiencia de scroll
        function mejorarScroll() {
            // Asegurar que el contenido sea scrolleable
            $('body').css('overflow-x', 'hidden');
            
            // Scroll suave al hacer clic en botones de agregar stock
            $(document).on('click', '.btn-agregar-stock', function() {
                // Scroll al botón para asegurar que esté visible
                $('html, body').animate({
                    scrollTop: $(this).offset().top - 100
                }, 300);
            });
            
            // Asegurar que los botones estén siempre visibles
            $(window).on('scroll', function() {
                const windowHeight = $(window).height();
                const documentHeight = $(document).height();
                const scrollTop = $(window).scrollTop();
                
                // Si estamos cerca del final, asegurar que los botones sean visibles
                if (scrollTop + windowHeight >= documentHeight - 50) {
                    $('.btn-agregar-stock').css('position', 'relative');
                }
            });
        }
        
        // Inicializar modales correctamente
        function inicializarModales() {
            // Ocultar todos los modales al cargar
            $('.modal').removeClass('show').hide();
            
            // Limpiar modales al cerrar
            $('.modal').on('hidden.bs.modal', function() {
                $(this).removeClass('show').hide();
                $(this).find('form')[0].reset();
                $(this).find('.form-control').removeClass('is-invalid');
            });
            
            // Prevenir que los modales se abran múltiples veces
            $('.modal').on('show.bs.modal', function() {
                $('.modal').not(this).removeClass('show').hide();
            });
        }
        
        // Ajustar vista según el tamaño de pantalla
        function ajustarVista() {
            if (window.innerWidth <= 768) {
                $('.desktop-table').hide();
                $('.mobile-cards').show();
            } else {
                $('.desktop-table').show();
                $('.mobile-cards').hide();
            }
        }
        
        // Ajustar vista al redimensionar la ventana
        $(window).resize(function() {
            ajustarVista();
        });

        function verificarStockBajo() {
            // Limpiar alertas anteriores
            $('#alertas-stock').empty();
            
            let hayStockBajo = false;
            let productosConStockBajo = new Set(); // Para evitar duplicados
            
            // Buscar solo en la columna de stock (5ta columna), no en la de estado
            $('tbody tr').each(function() {
                const stockBadge = $(this).find('td:nth-child(5) .badge-modern');
                const stock = parseInt(stockBadge.text());
                
                if (stock <= 5) {
                    const producto = $(this).find('td:nth-child(2)').text().trim();
                    
                    // Evitar duplicados
                    if (!productosConStockBajo.has(producto)) {
                        productosConStockBajo.add(producto);
                        hayStockBajo = true;
                        
                        const esAgotado = stock <= 0;
                        const colorFondo = esAgotado ? 'linear-gradient(135deg, #f8d7da, #f5c6cb)' : 'linear-gradient(135deg, #fff3cd, #ffeaa7)';
                        const colorBorde = esAgotado ? '#dc3545' : '#ffc107';
                        const colorTexto = esAgotado ? '#721c24' : '#856404';
                        const icono = esAgotado ? 'fa-times-circle' : 'fa-exclamation-triangle';
                        const mensaje = esAgotado ? 'Agotado' : 'Stock Bajo';
                        
                        const alerta = `
                            <div class="card-modern" style="background: ${colorFondo}; border-left: 4px solid ${colorBorde}; margin-bottom: 1rem; animation: slideInDown 0.5s ease;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas ${icono}" style="color: ${colorBorde}; font-size: 1.5rem;"></i>
                                    <div>
                                        <h4 style="color: ${colorTexto}; margin: 0; font-size: 1rem; font-weight: 600;">⚠️ ${mensaje}</h4>
                                        <p style="color: ${colorTexto}; margin: 0; font-size: 0.9rem;">${producto} tiene solo ${stock} unidades disponibles.</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#alertas-stock').append(alerta);
                    }
                }
            });
            
            // Si no hay stock bajo, mostrar mensaje de que todo está bien
            if (!hayStockBajo) {
                const mensajeOk = `
                    <div class="card-modern" style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border-left: 4px solid #28a745; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-check-circle" style="color: #28a745; font-size: 1.5rem;"></i>
                            <div>
                                <h4 style="color: #155724; margin: 0; font-size: 1rem; font-weight: 600;">✅ Stock Normal</h4>
                                <p style="color: #155724; margin: 0; font-size: 0.9rem;">Todos los productos tienen stock suficiente.</p>
                            </div>
                        </div>
                    </div>
                `;
                $('#alertas-stock').append(mensajeOk);
            }
        }

        // Abrir modal individual
        $(document).on('click', '.btn-agregar-stock', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');
            const stock = $(this).data('stock');
            
            // Ocultar otros modales
            $('.modal').removeClass('show').hide();
            
            // Usar el modal general en lugar del individual
            $('#selectProducto').val(id);
            $('#cantidadAgregar').focus();
            
            // Mostrar el modal general
            $('#modalAgregarStock').addClass('show').show();
            
            // Permitir scroll en el body
            $('body').css('overflow', 'auto');
        });

        // Función para cerrar modales
        function cerrarModal(modalId) {
            $('#' + modalId).removeClass('show').hide();
        }
        
        // Cerrar modales al hacer clic en cancelar o X
        $(document).on('click', '[data-bs-dismiss="modal"], .btn-close', function() {
            $(this).closest('.modal').removeClass('show').hide();
        });
        
        // Cerrar modal al hacer clic fuera de él
        $(document).on('click', '.modal', function(e) {
            if (e.target === this) {
                $(this).removeClass('show').hide();
            }
        });
        
        // Abrir modal general
        $(document).on('click', '[data-bs-target="#modalAgregarStock"]', function() {
            // Ocultar otros modales
            $('.modal').removeClass('show').hide();
            
            // Mostrar el modal general
            $('#modalAgregarStock').addClass('show').show();
            
            // Permitir scroll en el body
            $('body').css('overflow', 'auto');
        });
        
        // Enviar formulario individual
        $('#formAgregarStockIndividual').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'agregar_stock.php',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.trim() === 'ok') {
                        alert('✅ Stock agregado correctamente');
                        cerrarModal('modalAgregarStockIndividual');
                        location.reload();
                    } else {
                        alert('❌ Error: ' + response);
                    }
                },
                error: function() {
                    alert('❌ Error del servidor');
                }
            });
        });

        // Enviar formulario general
        $('#formAgregarStock').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'agregar_stock.php',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.trim() === 'ok') {
                        alert('✅ Stock agregado correctamente');
                        cerrarModal('modalAgregarStock');
                        location.reload();
                    } else {
                        alert('❌ Error: ' + response);
                    }
                },
                error: function() {
                    alert('❌ Error del servidor');
                }
            });
        });
    </script>

    <!-- Navbar fijo abajo -->
    <?php include "../includes/navbar.php"; ?>
</body>
</html>
