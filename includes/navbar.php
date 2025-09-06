<!-- Navbar inferior moderno para app móvil -->
<nav class="bottom-navbar">
    <div class="nav-container">
        <a href="dashboard.php" class="nav-item" data-page="dashboard">
            <div class="nav-icon">
                <i class="fas fa-home"></i>
            </div>
            <span class="nav-label">Inicio</span>
            <div class="nav-indicator"></div>
        </a>
        
        <a href="ventas.php" class="nav-item" data-page="ventas">
            <div class="nav-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <span class="nav-label">Ventas</span>
            <div class="nav-indicator"></div>
        </a>
        
        <a href="inventario.php" class="nav-item" data-page="inventario">
            <div class="nav-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <span class="nav-label">Inventario</span>
            <div class="nav-indicator"></div>
        </a>
        
        <a href="cierre.php" class="nav-item" data-page="cierre">
            <div class="nav-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <span class="nav-label">Resumen</span>
            <div class="nav-indicator"></div>
        </a>
        
        <a href="logout.php" class="nav-item logout" data-page="logout">
            <div class="nav-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <span class="nav-label">Salir</span>
            <div class="nav-indicator"></div>
        </a>
    </div>
    
    <!-- Indicador de página activa -->
    <div class="active-indicator"></div>
</nav>

<!-- Script del navbar -->
<script src="../js/navbar.js"></script>
