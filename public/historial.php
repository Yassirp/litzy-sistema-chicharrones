<?php
session_start();
require_once "../config/db.php";
date_default_timezone_set('America/Bogota');

// Consulta para traer el historial de cierres
$sql = "SELECT id, fecha_cierre, usuario_id, total, creado_en 
        FROM historial_cierres
        ORDER BY fecha_cierre DESC";

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
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#ff6a00">
    <meta name="msapplication-navbutton-color" content="#ff6a00">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="stylesheet" href="../css/app-base.css">
    <link rel="stylesheet" href="../css/ios-fix.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Historial de Cierres - Litzy</title>
    
    <!-- CSS específico para Android e iOS -->
    <style>
        /* === FIXES PARA ANDROID E iOS === */
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Forzar hardware acceleration */
        .app-container, .app-header, .section, .stat-card, .action-card {
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            will-change: transform;
        }
        
        /* Mejorar renderizado */
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
        
        /* Estilos para las cards de acciones rápidas */
        .action-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ff6a00, #ff8c00);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .action-icon i {
            color: white;
            font-size: 1.2rem;
        }
        
        .action-content {
            flex: 1;
        }
        
        .action-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin: 0 0 0.25rem 0;
        }
        
        .action-description {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }
        
        .action-arrow {
            color: #ff6a00;
            font-size: 1.2rem;
            margin-left: 1rem;
        }
        
        /* Responsive para móvil */
        @media (max-width: 768px) {
            .action-card {
                padding: 1rem;
                margin-bottom: 0.75rem;
            }
            
            .action-icon {
                width: 45px;
                height: 45px;
                margin-right: 0.75rem;
            }
            
            .action-title {
                font-size: 1rem;
            }
            
            .action-description {
                font-size: 0.85rem;
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
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="user-details">
                        <h1>📋 Historial de Cierres</h1>
                        <p>Consulta todos los cierres de caja</p>
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

        <!-- Historial de cierres -->
        <section class="section">
            <h2 class="section-title">Cierres de Caja Anteriores</h2>
            <?php
            // Verificar si hay cierres
            $cierres_count = 0;
            $cierres_data = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $cierres_data[] = $row;
                $cierres_count++;
            }
            
            if ($cierres_count == 0) {
                echo '<div class="card-modern" style="text-align: center; background: linear-gradient(135deg, #ffc107, #fd7e14); color: #333;">
                        <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 1rem; color: #856404;"></i>
                        <h2>No hay cierres de caja registrados</h2>
                        <p>Los cierres de caja aparecerán aquí una vez que se realicen ventas y se procesen cierres.</p>
                        <a href="cierre.php" class="btn-modern" style="margin-top: 1rem;">Realizar Primer Cierre</a>
                      </div>';
            } else {
                echo '<div class="table-modern">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha de Cierre</th>
                                    <th>Total Caja</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>';
                
                foreach ($cierres_data as $row) {
                    // Manejar la fecha correctamente
                    $fecha_cierre = $row['fecha_cierre'];
                    if (is_object($fecha_cierre)) {
                        $fecha_formateada = $fecha_cierre->format('d/m/Y');
                    } else {
                        $fecha_formateada = date('d/m/Y', strtotime($fecha_cierre));
                    }
                    
                    // Manejar el total correctamente
                    $total = $row['total'] ?? 0;
                    
                    echo '<tr>
                            <td style="font-weight: 700;">
                                <i class="fas fa-calendar-alt" style="color: #ff6a00; margin-right: 0.5rem;"></i>
                                ' . $fecha_formateada . '
                            </td>
                            <td style="font-weight: 700; color: #28a745; font-size: 1.1rem;">
                                $' . number_format($total, 0, ',', '.') . '
                            </td>
                            <td>
                                <span class="badge-modern success">Completado</span>
                            </td>
                          </tr>';
                }
                
                echo '</tbody></table></div>';
            }
            ?>
        </section>

        <!-- Acciones Rápidas -->
        <section class="section">
            <h2 class="section-title">Acciones Rápidas</h2>
            
            <a href="cierre.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="action-content">
                    <h3 class="action-title">Ver Historial</h3>
                    <p class="action-description">Consulta cierres anteriores</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
            
            <a href="dashboard.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="action-content">
                    <h3 class="action-title">Volver al Inicio</h3>
                    <p class="action-description">Regresar al dashboard</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </section>
    </div>

    <?php include_once "../includes/navbar.php"; ?> 
    
    <!-- Script para forzar carga de estilos en móviles -->
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
            var cards = document.querySelectorAll('.action-card');
            cards.forEach(function(card) {
                card.style.transform = 'translateZ(0)';
                card.style.webkitTransform = 'translateZ(0)';
            });
            
            // Timeout adicional para Android
            if (isAndroid()) {
                setTimeout(function() {
                    container.style.opacity = '1';
                    container.classList.add('android-loaded');
                }, 100);
            }
        });
    }
    
    // Ejecutar para iOS y Android
    if (isMobile()) {
        forceStyleLoad();
    }
    </script>
</body>
</html>
