<?php
session_start();
require_once "../config/db.php";
require_once "../includes/security.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize_input($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    // Validar entrada
    if (empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } elseif (!validate_email($email)) {
        $error = "Email inválido";
    } else {
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $params = [$email];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (password_verify($password, $row["password"])) {
            $_SESSION["usuario_id"] = $row["id"];
            $_SESSION["usuario_nombre"] = $row["nombre"];
                $_SESSION["last_activity"] = time();
                security_log("Usuario {$row['email']} inició sesión exitosamente");
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Contraseña incorrecta";
                security_log("Intento de login fallido para email: $email - Contraseña incorrecta", 'WARNING');
        }
    } else {
        $error = "Usuario no encontrado";
            security_log("Intento de login fallido para email: $email - Usuario no encontrado", 'WARNING');
        }
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
    <title>Login - Litzy</title>
    <link rel="stylesheet" href="../css/app-base.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === ESTILOS ESPECÍFICOS PARA LOGIN === */
        .login-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #d2691e 0%, #cd853f 50%, #daa520 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }

        .login-page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 140, 0, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 165, 0, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 215, 0, 0.2) 0%, transparent 50%);
            animation: backgroundShift 20s ease-in-out infinite;
        }

        @keyframes backgroundShift {
            0%, 100% { transform: translateX(0) translateY(0); }
            25% { transform: translateX(-20px) translateY(-10px); }
            50% { transform: translateX(20px) translateY(10px); }
            75% { transform: translateX(-10px) translateY(20px); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 10;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #d2691e, #cd853f);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 20px rgba(210, 105, 30, 0.3);
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .login-logo i {
            font-size: 2rem;
            color: white;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 0.5rem;
            background: linear-gradient(135deg, #d2691e, #cd853f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            color: #718096;
            font-size: 1rem;
            margin: 0;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            outline: none;
            border-color: #d2691e;
            box-shadow: 0 0 0 3px rgba(210, 105, 30, 0.1);
            transform: translateY(-2px);
        }

        .form-input::placeholder {
            color: #a0aec0;
        }

        .form-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .form-input:focus + .form-icon {
            color: #d2691e;
        }

        .login-button {
            background: linear-gradient(135deg, #d2691e, #cd853f);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(210, 105, 30, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .login-link {
            color: #d2691e;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .login-link:hover {
            color: #cd853f;
            text-decoration: underline;
        }

        .error-message {
            background: linear-gradient(135deg, #fed7d7, #feb2b2);
            color: #c53030;
            padding: 1rem;
            border-radius: 12px;
            border-left: 4px solid #e53e3e;
            margin-bottom: 1rem;
            font-weight: 500;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .login-title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <!-- Formas flotantes de fondo -->
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
    <div class="login-container">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-utensils"></i>
                </div>
                <h1 class="login-title">🍖 ¡Bienvenido!</h1>
                <p class="login-subtitle">Accede al sistema de gestión</p>
            </div>
            
            <form method="post" class="login-form">
                <?php if(isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Correo electrónico" required>
                    <i class="fas fa-envelope form-icon"></i>
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Contraseña" required>
                    <i class="fas fa-lock form-icon"></i>
                </div>
                
                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
        </form>
            
            <div class="login-footer">
                <p style="color: #718096; font-size: 0.9rem;">
                    <i class="fas fa-shield-alt"></i>
                    Acceso privado - Solo personal autorizado
                </p>
            </div>
        </div>
    </div>
</body>
</html>

