<?php
session_start();
require_once "../config/db.php";
date_default_timezone_set('America/Bogota');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Calcular la fecha actual
$fecha_hoy = date('Y-m-d');

// 1. Calcular las ganancias del día (ventas - costos)
$sql_ganancias = "
    SELECT 
        ISNULL(SUM(dv.subtotal), 0) AS total_ventas,
        ISNULL(SUM(
            CASE 
                WHEN mi.costo_unidad IS NOT NULL THEN dv.cantidad * mi.costo_unidad
                ELSE 0
            END
        ), 0) AS total_costos,
        ISNULL(SUM(dv.subtotal), 0) - ISNULL(SUM(
            CASE 
                WHEN mi.costo_unidad IS NOT NULL THEN dv.cantidad * mi.costo_unidad
                ELSE 0
            END
        ), 0) AS ganancias_dia
    FROM detalle_ventas dv
    INNER JOIN ventas v ON dv.venta_id = v.id
    LEFT JOIN (
        SELECT 
            producto_id,
            AVG(costo_unidad) as costo_unidad
        FROM movimientos_inventario 
        WHERE costo_unidad > 0
        GROUP BY producto_id
    ) mi ON dv.producto_id = mi.producto_id
    WHERE CAST(v.fecha AS DATE) = ?
";

$params = array($fecha_hoy);
$stmt_ganancias = sqlsrv_query($conn, $sql_ganancias, $params);

if ($stmt_ganancias === false) {
    die(print_r(sqlsrv_errors(), true)); // muestra el error si falla la consulta
}

$row_ganancias = sqlsrv_fetch_array($stmt_ganancias, SQLSRV_FETCH_ASSOC);
$total_ventas = $row_ganancias['total_ventas'] ?? 0;
$total_costos = $row_ganancias['total_costos'] ?? 0;
$ganancias_dia = $row_ganancias['ganancias_dia'] ?? 0;

// Verificar si ya existe un cierre para hoy
$sql_check = "SELECT COUNT(*) as existe FROM historial_cierres WHERE CAST(fecha_cierre AS DATE) = ?";
$stmt_check = sqlsrv_query($conn, $sql_check, array($fecha_hoy));
$row_check = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
$cierre_existe = $row_check['existe'] > 0;

// Solo insertar cierre si se solicita explícitamente
$cierre_realizado = false;
if (isset($_POST['realizar_cierre']) && !$cierre_existe) {
    $sql_insert = "INSERT INTO historial_cierres (fecha_cierre, total, usuario_id, creado_en) 
                   VALUES (?, ?, ?, GETDATE())";
    
    $usuario_id = $_SESSION['usuario_id'];
    $params_insert = array($fecha_hoy, $ganancias_dia, $usuario_id);
    
    $stmt_insert = sqlsrv_query($conn, $sql_insert, $params_insert);
    
    if ($stmt_insert !== false) {
        $cierre_realizado = true;
        // Recargar la página para mostrar el cierre realizado
        header("Location: cierre.php?cierre=exitoso");
        exit();
    }
}
include "../includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>Cierre de Caja - Litzy</title>
    <link rel="stylesheet" href="../css/app-base.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="app-header">
            <div class="header-content">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="user-details">
                        <h1>💰 Cierre de Caja</h1>
                        <p>Resumen del día - <?php echo $fecha_hoy; ?></p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="historial.php" class="btn-modern secondary">
                        <i class="fas fa-history"></i>
                        Historial
                    </a>
                </div>
            </div>
        </header>

        <!-- Resumen del día -->
        <section class="section">
            <div class="card-modern" style="text-align: center; background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                <h2 style="color: white; margin-bottom: 0.5rem;">💰 Ganancias del Día</h2>
                <p style="font-size: 3rem; font-weight: 700; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    $<?php echo number_format($ganancias_dia, 0, ',', '.'); ?>
                </p>
                <div style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.9;">
                    <div style="display: flex; justify-content: space-around; margin-top: 0.5rem;">
                        <div>
                            <i class="fas fa-arrow-up" style="color: #90EE90; margin-right: 0.3rem;"></i>
                            <span>Ventas: $<?php echo number_format($total_ventas, 0, ',', '.'); ?></span>
                        </div>
                        <div>
                            <i class="fas fa-arrow-down" style="color: #FFB6C1; margin-right: 0.3rem;"></i>
                            <span>Costos: $<?php echo number_format($total_costos, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Estado del cierre -->
            <div class="card-modern" style="margin-top: 1rem; text-align: center;">
                <?php if (isset($_GET['cierre']) && $_GET['cierre'] == 'exitoso'): ?>
                    <div style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 1rem; border-radius: 12px; margin-bottom: 1rem;">
                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                        <h3 style="margin: 0;">¡Cierre de Caja Realizado Exitosamente!</h3>
                    </div>
                <?php elseif ($cierre_existe): ?>
                    <div style="background: linear-gradient(135deg, #ffc107, #fd7e14); color: #333; padding: 1rem; border-radius: 12px; margin-bottom: 1rem;">
                        <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                        <h3 style="margin: 0;">Cierre de Caja Ya Realizado Hoy</h3>
                        <p style="margin: 0.5rem 0 0 0;">El cierre de caja para esta fecha ya fue procesado.</p>
                    </div>
                <?php else: ?>
                    <form method="POST" style="margin: 0;">
                        <button type="submit" name="realizar_cierre" class="btn-modern success" style="font-size: 1.2rem; padding: 1rem 2rem;">
                            <i class="fas fa-lock"></i>
                            Realizar Cierre de Caja
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </section>

        <!-- Detalle de ventas con ganancias -->
        <section class="section">
            <h2 class="section-title">📊 Detalle de Ventas y Ganancias</h2>
            <div style="max-height: 400px; overflow-y: auto; border-radius: 16px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                <div class="table-modern" style="margin: 0;">
                    <table style="margin: 0;">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="padding: 1rem 0.8rem; font-size: 0.9rem;">
                                    <i class="fas fa-list-alt" style="margin-right: 0.5rem;"></i>
                                    Descripción
                                </th>
                                <th style="padding: 1rem 0.8rem; font-size: 0.9rem; text-align: right;">
                                    <i class="fas fa-arrow-up" style="margin-right: 0.5rem; color: #28a745;"></i>
                                    Ventas
                                </th>
                                <th style="padding: 1rem 0.8rem; font-size: 0.9rem; text-align: right;">
                                    <i class="fas fa-arrow-down" style="margin-right: 0.5rem; color: #dc3545;"></i>
                                    Costos
                                </th>
                                <th style="padding: 1rem 0.8rem; font-size: 0.9rem; text-align: right;">
                                    <i class="fas fa-chart-line" style="margin-right: 0.5rem; color: #ff6a00;"></i>
                                    Ganancia
                                </th>
                                <th style="padding: 1rem 0.8rem; font-size: 0.9rem; text-align: right;">
                                    <i class="fas fa-clock" style="margin-right: 0.5rem;"></i>
                                    Hora
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_detalle = "
                                SELECT 
                                    v.id,
                                    v.descripcion,
                                    v.monto,
                                    v.fecha,
                                    ISNULL(SUM(dv.subtotal), 0) AS total_ventas,
                                    ISNULL(SUM(
                                        CASE 
                                            WHEN mi.costo_unidad IS NOT NULL THEN dv.cantidad * mi.costo_unidad
                                            ELSE 0
                                        END
                                    ), 0) AS total_costos,
                                    ISNULL(SUM(dv.subtotal), 0) - ISNULL(SUM(
                                        CASE 
                                            WHEN mi.costo_unidad IS NOT NULL THEN dv.cantidad * mi.costo_unidad
                                            ELSE 0
                                        END
                                    ), 0) AS ganancia_venta
                                FROM ventas v
                                LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
                                LEFT JOIN (
                                    SELECT 
                                        producto_id,
                                        AVG(costo_unidad) as costo_unidad
                                    FROM movimientos_inventario 
                                    WHERE costo_unidad > 0
                                    GROUP BY producto_id
                                ) mi ON dv.producto_id = mi.producto_id
                                WHERE CAST(v.fecha AS DATE) = ?
                                GROUP BY v.id, v.descripcion, v.monto, v.fecha
                                ORDER BY v.fecha DESC
                            ";
                            $stmt_detalle = sqlsrv_query($conn, $sql_detalle, $params);

                            while ($row = sqlsrv_fetch_array($stmt_detalle, SQLSRV_FETCH_ASSOC)) {
                                $ganancia_venta = $row['ganancia_venta'] ?? 0;
                                $color_ganancia = $ganancia_venta >= 0 ? '#28a745' : '#dc3545';
                                $icono_ganancia = $ganancia_venta >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                                
                                echo "<tr style='border-bottom: 1px solid #f8f9fa;'>";
                                echo "<td style='padding: 1rem 0.8rem; color: #333; font-size: 0.9rem; line-height: 1.4;'>
                                        <i class='fas fa-shopping-bag' style='color: #6c757d; margin-right: 0.5rem; font-size: 0.8rem;'></i>
                                        " . htmlspecialchars($row['descripcion']) . "
                                      </td>";
                                echo "<td style='padding: 1rem 0.8rem; font-weight: 700; color: #28a745; text-align: right; font-size: 0.9rem;'>
                                        <span style='background: rgba(40, 167, 69, 0.1); padding: 0.3rem 0.6rem; border-radius: 12px; display: inline-block;'>
                                            $" . number_format($row['total_ventas'], 0, ',', '.') . "
                                        </span>
                                      </td>";
                                echo "<td style='padding: 1rem 0.8rem; font-weight: 700; color: #dc3545; text-align: right; font-size: 0.9rem;'>
                                        <span style='background: rgba(220, 53, 69, 0.1); padding: 0.3rem 0.6rem; border-radius: 12px; display: inline-block;'>
                                            $" . number_format($row['total_costos'], 0, ',', '.') . "
                                        </span>
                                      </td>";
                                echo "<td style='padding: 1rem 0.8rem; font-weight: 700; color: {$color_ganancia}; text-align: right; font-size: 0.9rem;'>
                                        <span style='background: rgba(" . ($ganancia_venta >= 0 ? '40, 167, 69' : '220, 53, 69') . ", 0.1); padding: 0.3rem 0.6rem; border-radius: 12px; display: inline-block;'>
                                            <i class='fas {$icono_ganancia}' style='margin-right: 0.3rem;'></i>
                                            $" . number_format($ganancia_venta, 0, ',', '.') . "
                                        </span>
                                      </td>";
                                echo "<td style='padding: 1rem 0.8rem; color: #666; text-align: right; font-size: 0.8rem;'>
                                        <i class='fas fa-clock' style='margin-right: 0.3rem;'></i>
                                        {$row['fecha']->format('H:i:s')}
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Acciones adicionales -->
        <section class="section">
            <h2 class="section-title">Acciones Rápidas</h2>
            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                <a href="historial.php" class="action-card-modern">
                    <div class="action-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="action-content">
                        <h3>Ver Historial</h3>
                        <p>Consulta cierres anteriores</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
                
                <a href="dashboard.php" class="action-card-modern">
                    <div class="action-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="action-content">
                        <h3>Volver al Inicio</h3>
                        <p>Regresar al dashboard</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </section>

        <!-- Estilos para las tarjetas de acción -->
        <style>
        .action-card-modern {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 20px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: #333;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }

        .action-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff6a00, #ff8c00);
        }

        .action-card-modern:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(255, 106, 0, 0.2);
            border-color: #ff6a00;
        }

        .action-card-modern:active {
            transform: translateY(-2px) scale(0.98);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ff6a00, #ff8c00);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(255, 106, 0, 0.3);
            transition: all 0.3s ease;
        }

        .action-card-modern:hover .action-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 25px rgba(255, 106, 0, 0.4);
        }

        .action-content {
            flex: 1;
        }

        .action-content h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
            color: #333;
            transition: color 0.3s ease;
        }

        .action-card-modern:hover .action-content h3 {
            color: #ff6a00;
        }

        .action-content p {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.4;
            margin: 0;
            transition: color 0.3s ease;
        }

        .action-card-modern:hover .action-content p {
            color: #555;
        }

        .action-arrow {
            color: #ff6a00;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .action-card-modern:hover .action-arrow {
            transform: translateX(5px);
            opacity: 1;
        }

        /* Animación de entrada */
        .action-card-modern {
            animation: slideInUp 0.6s ease-out;
        }

        .action-card-modern:nth-child(1) { animation-delay: 0.1s; }
        .action-card-modern:nth-child(2) { animation-delay: 0.2s; }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .action-card-modern {
                padding: 1.2rem;
                gap: 0.8rem;
            }
            
            .action-icon {
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }
            
            .action-content h3 {
                font-size: 1.1rem;
            }
            
            .action-content p {
                font-size: 0.8rem;
            }
        }
        </style>
    </div>
</body>
</html>
