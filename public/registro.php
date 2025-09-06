<?php
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
    $params = [$nombre, $email, $password];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        $mensaje = "Usuario registrado con éxito. <a href='index.php'>Iniciar sesión</a>";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "Error: " . print_r(sqlsrv_errors(), true);
        $tipo_mensaje = "danger";
    }
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
    <title>Registro - Litzy</title>
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="app-header">
            <div class="header-content">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="user-details">
                        <h1>👤 Registro de Usuario</h1>
                        <p>Crea tu cuenta en Litzy</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="index.php" class="btn-modern secondary">
                        <i class="fas fa-sign-in-alt"></i>
                        Iniciar Sesión
                    </a>
                </div>
            </div>
        </header>

        <!-- Formulario de Registro -->
        <section class="section">
            <div class="card-modern" style="max-width: 500px; margin: 0 auto;">
                <form method="post" style="padding: 0;">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user" style="color: #ff6a00; margin-right: 0.5rem;"></i>
                            Nombre Completo
                        </label>
                        <input type="text" name="nombre" class="form-control" placeholder="Tu nombre completo" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope" style="color: #ff6a00; margin-right: 0.5rem;"></i>
                            Correo Electrónico
                        </label>
                        <input type="email" name="email" class="form-control" placeholder="tu@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock" style="color: #ff6a00; margin-right: 0.5rem;"></i>
                            Contraseña
                        </label>
                        <input type="password" name="password" class="form-control" placeholder="Tu contraseña" required>
                    </div>

                    <?php if(isset($mensaje)): ?>
                        <div class="alert-modern <?php echo $tipo_mensaje; ?>" style="margin-bottom: 1rem;">
                            <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn-modern success" style="width: 100%; font-size: 1.1rem; padding: 1rem;">
                        <i class="fas fa-user-plus"></i>
                        Registrarse
                    </button>
                </form>
            </div>
        </section>

        <!-- Enlace de login -->
        <section class="section">
            <div class="card-modern" style="text-align: center; background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                <h3 style="color: #333; margin-bottom: 1rem;">
                    <i class="fas fa-sign-in-alt" style="color: #ff6a00; margin-right: 0.5rem;"></i>
                    ¿Ya tienes cuenta?
                </h3>
                <p style="color: #666; margin-bottom: 1rem;">Inicia sesión con tu cuenta existente</p>
                <a href="index.php" class="btn-modern" style="text-decoration: none;">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </a>
            </div>
        </section>
    </div>
</body>
</html>
