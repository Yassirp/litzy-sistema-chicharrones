<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
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
    if ($stmt_ventas && $row = sqlsrv_fetch_array($stmt_ventas, SQLSRV_FETCH_ASSOC)) {
        $ventas_hoy = $row['total_ventas'];
    }
    
    // Número de transacciones de hoy
    $sql_transacciones = "SELECT COUNT(*) as total_transacciones 
                          FROM ventas 
                          WHERE CAST(fecha AS DATE) = CAST(GETDATE() AS DATE)";
    $stmt_transacciones = sqlsrv_query($conn, $sql_transacciones);
    if ($stmt_transacciones && $row = sqlsrv_fetch_array($stmt_transacciones, SQLSRV_FETCH_ASSOC)) {
        $transacciones_hoy = $row['total_transacciones'];
    }
    
    // Total de productos activos
    $sql_productos = "SELECT COUNT(*) as total_productos 
                      FROM productos 
                      WHERE estado = 1";
    $stmt_productos = sqlsrv_query($conn, $sql_productos);
    if ($stmt_productos && $row = sqlsrv_fetch_array($stmt_productos, SQLSRV_FETCH_ASSOC)) {
        $total_productos = $row['total_productos'];
    }
    
} catch (Exception $e) {
    // En caso de error, mantener valores en 0
    $ventas_hoy = 0;
    $transacciones_hoy = 0;
    $total_productos = 0;
}

include "../includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">    
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#ff6a00">
    <meta name="msapplication-navbutton-color" content="#ff6a00">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="stylesheet" href="../css/app-base.css">
    <link rel="stylesheet" href="../css/ios-fix.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS específico para Android -->
    <style>
        /* === FIXES PARA ANDROID === */
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Forzar hardware acceleration en Android */
        .app-container, .app-header, .section, .stat-card, .action-card {
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            will-change: transform;
        }
        
        /* Mejorar renderizado en Android */
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        
        /* Asegurar que los estilos se carguen */
        .app-container {
            opacity: 0;
            animation: fadeIn 0.5s ease forwards;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        
        /* Fix para Android Chrome */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .stat-card, .action-card {
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
            }
            
            .action-card.primary {
                background: #ff6a00 !important;
                background: -webkit-linear-gradient(135deg, #ff6a00, #ff8c00) !important;
                background: linear-gradient(135deg, #ff6a00, #ff8c00) !important;
            }
        }
        
        /* Detectar Android y forzar estilos */
        .android-loaded .stat-card {
            background: rgba(255, 255, 255, 0.95) !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }
        
        .android-loaded .action-card {
            background: white !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }
        
        .android-loaded .action-card.primary {
            background: linear-gradient(135deg, #ff6a00, #ff8c00) !important;
            color: white !important;
        }
        
        .android-loaded .action-card.primary .card-icon {
            background: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }
        
        .android-loaded .action-card.primary .card-arrow {
            color: white !important;
        }
        
        .android-loaded .action-card.primary .card-content h3 {
            color: white !important;
        }
        
        .android-loaded .action-card.primary .card-content p {
            color: rgba(255, 255, 255, 0.8) !important;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Dashboard - Litzy</title>
</head>
<body>
    <div class="app-container">
        <!-- Header con información del usuario -->
        <header class="app-header">
            <div class="header-content">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <h1>¡Hola, <?= $_SESSION["usuario_nombre"] ?>!</h1>
                        <p>Bienvenido a tu panel de control</p>
                    </div>
                </div>
                
            </div>
        </header>

        <!-- Estadísticas rápidas -->
        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>$<?= number_format($ventas_hoy, 0, ',', '.') ?></h3>
                        <p>Ventas Hoy</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $transacciones_hoy ?></h3>
                        <p>Transacciones</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $total_productos ?></h3>
                        <p>Productos</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Acciones principales -->
        <section class="actions-section">
            <h2 class="section-title" style="color: white;">Acciones Rápidas</h2>
            <div class="actions-grid">
                <a href="ventas.php" class="action-card primary">
                    <div class="card-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="card-content">
                        <h3>Nueva Venta</h3>
                        <p>Registra una venta rápidamente</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="inventario.php" class="action-card">
                    <div class="card-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="card-content">
                        <h3>Inventario</h3>
                        <p>Gestiona productos y stock</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="cierre.php" class="action-card">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-content">
                        <h3>Resumen</h3>
                        <p>Ventas y estadísticas del día</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="historial.php" class="action-card">
                    <div class="card-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="card-content">
                        <h3>Historial</h3>
                        <p>Consulta ventas anteriores</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </section>
    </div>

    <!-- Navbar inferior -->
    <?php include "../includes/navbar.php"; ?>

    <!-- Estilos específicos para iPhone -->
    <style>
    /* Forzar carga de estilos en iOS */
    @media screen and (-webkit-min-device-pixel-ratio: 2) {
        .app-container {
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
        }
        
        .app-header {
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }
    }
    
    /* Asegurar que los estilos se carguen */
    .app-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #ff6a00 0%, #ff8c00 50%, #ffa500 100%);
    }
    
    /* Forzar renderizado en iOS */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Asegurar que el navbar esté visible */
    body {
        padding-bottom: 85px !important;
    }
    
    @media (max-width: 480px) {
        body {
            padding-bottom: 80px !important;
        }
    }
    </style>

    <!-- Estilos específicos para el dashboard -->
    <style>
    /* === ESTADÍSTICAS === */
    .stats-section {
        padding: 1.5rem 1rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 1.2rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 120px;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff6a00, #ff8c00);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(255, 106, 0, 0.2);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #ff6a00, #ff8c00);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.8rem;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .stat-content h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ff6a00;
        margin-bottom: 0.3rem;
    }

    .stat-content p {
        font-size: 0.8rem;
        color: #666;
        font-weight: 500;
    }

    /* === ACCIONES PRINCIPALES === */
    .actions-section {
        padding: 0 1rem 1.5rem;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .action-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: #333;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
    }

    .action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff6a00, #ff8c00);
    }

    .action-card.primary {
        background: linear-gradient(135deg, #ff6a00, #ff8c00);
        color: white;
    }

    .action-card.primary .card-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .action-card.primary .card-arrow {
        color: white;
    }

    .action-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 35px rgba(255, 106, 0, 0.25);
        border-color: #ff6a00;
    }

    .action-card:active {
        transform: translateY(-2px) scale(0.98);
    }

    .card-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #ff6a00, #ff8c00);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .card-content {
        flex: 1;
    }

    .card-content h3 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
        color: inherit;
    }

    .card-content p {
        font-size: 0.9rem;
        color: #666;
        line-height: 1.4;
    }

    .action-card.primary .card-content p {
        color: rgba(255, 255, 255, 0.8);
    }

    .card-arrow {
        color: #ff6a00;
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .action-card:hover .card-arrow {
        transform: translateX(5px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid {
            gap: 0.8rem;
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .actions-grid {
            gap: 0.8rem;
        }
        
        .action-card {
            padding: 1.2rem;
        }
    }

    @media (max-width: 480px) {
        .stats-section {
            padding: 1rem 0.8rem;
        }
        
        .actions-section {
            padding: 0 0.8rem 1rem;
        }
        
        .stat-card {
            padding: 0.8rem;
        }
        
        .action-card {
            padding: 1rem;
            gap: 0.8rem;
        }
        
        .card-icon {
            width: 45px;
            height: 45px;
            font-size: 1.2rem;
        }
    }

    /* Asegurar que los estilos se carguen en iOS */
    @media screen and (-webkit-min-device-pixel-ratio: 2) {
        .stat-card, .action-card {
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }
    }
    </style>

    <!-- Script para forzar recarga de estilos en iOS -->
    <script>
    // Detectar iOS y Android
    function isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) || 
               (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    }
    
    function isAndroid() {
        return /Android/.test(navigator.userAgent);
    }
    
    function isMobile() {
        return isIOS() || isAndroid() || /Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }
    
    // Función para forzar carga de estilos
    function forceStyleLoad() {
        document.addEventListener('DOMContentLoaded', function() {
            var container = document.querySelector('.app-container');
            var navbar = document.querySelector('.bottom-navbar');
            
            if (container) {
                // Forzar repaint
                container.style.opacity = '0';
                container.offsetHeight; // trigger reflow
                container.style.opacity = '1';
                
                // Agregar clase específica según el dispositivo
                if (isIOS()) {
                    container.classList.add('ios-loaded');
                } else if (isAndroid()) {
                    container.classList.add('android-loaded');
                }
            }
            
            if (navbar) {
                // Forzar recarga del navbar
                navbar.style.display = 'none';
                navbar.offsetHeight; // trigger reflow
                navbar.style.display = 'block';
            }
            
            // Forzar repaint de todos los cards
            var cards = document.querySelectorAll('.stat-card, .action-card');
            cards.forEach(function(card) {
                card.style.transform = 'translateZ(0)';
                card.style.webkitTransform = 'translateZ(0)';
            });
            
            // Timeout adicional para Android
            if (isAndroid()) {
                setTimeout(function() {
                    container.style.opacity = '1';
                    container.classList.add('android-loaded');
                    
                    // Forzar aplicación de estilos específicos para Android
                    var primaryCards = document.querySelectorAll('.action-card.primary');
                    primaryCards.forEach(function(card) {
                        card.style.background = 'linear-gradient(135deg, #ff6a00, #ff8c00)';
                        card.style.color = 'white';
                        
                        var icon = card.querySelector('.card-icon');
                        if (icon) {
                            icon.style.background = 'rgba(255, 255, 255, 0.2)';
                            icon.style.color = 'white';
                        }
                        
                        var arrow = card.querySelector('.card-arrow');
                        if (arrow) {
                            arrow.style.color = 'white';
                        }
                        
                        var title = card.querySelector('.card-content h3');
                        if (title) {
                            title.style.color = 'white';
                        }
                        
                        var description = card.querySelector('.card-content p');
                        if (description) {
                            description.style.color = 'rgba(255, 255, 255, 0.8)';
                        }
                    });
                }, 100);
            }
        });
    }
    
    // Ejecutar para iOS y Android
    if (isMobile()) {
        forceStyleLoad();
    }
    
    // Actualizar estadísticas automáticamente cada 30 segundos
    function actualizarEstadisticas() {
        $.ajax({
            url: 'obtener_estadisticas.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Datos recibidos:', data);
                if (data.success) {
                    // Actualizar ventas de hoy (monto total)
                    $('.stat-card:first-child h3').text('$' + data.ventas_hoy.toLocaleString());
                    
                    // Actualizar transacciones
                    $('.stat-card:nth-child(2) h3').text(data.transacciones_hoy);
                    
                    // Actualizar productos
                    $('.stat-card:last-child h3').text(data.total_productos);
                } else {
                    console.log('Error en datos:', data.error);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error AJAX:', error);
                console.log('Respuesta:', xhr.responseText);
            }
        });
    }
    
    // Actualizar estadísticas al cargar la página
    $(document).ready(function() {
        // Actualizar cada 30 segundos
        setInterval(actualizarEstadisticas, 30000);
    });
    </script>
</body>
</html>
