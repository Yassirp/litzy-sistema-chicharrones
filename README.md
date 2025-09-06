# 🥓 Litzy - Sistema de Gestión para Microempresa de Chicharrones

Un sistema completo de punto de venta e inventario diseñado específicamente para microempresas de chicharrones en crecimiento. Incluye cálculo automático de ganancias restando costos de productos.

## ✨ Características Principales

### 🛒 Sistema de Ventas
- **Interfaz moderna y responsive** con diseño atractivo
- **Gestión de productos** con imágenes y precios
- **Cálculo automático** de totales y subtotales
- **Validación de stock** en tiempo real
- **Descuento automático** de inventario al realizar ventas

### 📦 Sistema de Inventario
- **Control de stock** en tiempo real
- **Alertas de stock bajo** (≤5 unidades)
- **Registro de movimientos** de inventario
- **Gestión de proveedores** y costos
- **Historial completo** de entradas y salidas

### 💰 Sistema de Ganancias
- **Cálculo automático de ganancias** restando costos de ventas
- **Registro de costos** por producto y proveedor
- **Reportes de rentabilidad** por transacción
- **Cierre de caja con ganancias** en lugar de totales
- **Análisis de productos más rentables**

### 🎨 Diseño Moderno
- **Interfaz intuitiva** con gradientes y animaciones
- **Responsive design** para móviles y tablets
- **Navbar inferior** para navegación fácil
- **Alertas visuales** para stock bajo
- **Cards interactivas** para productos
- **Compatibilidad mejorada** con Android e iOS

## 🚀 Instalación

### Requisitos
- **XAMPP** (Apache + PHP + SQL Server)
- **SQL Server** configurado
- **PHP 7.4+** con extensión `sqlsrv`

### Pasos de Instalación

1. **Clonar el proyecto**
   ```bash
   git clone [url-del-repositorio]
   cd Litzy
   ```

2. **Configurar la base de datos**
   - Abrir SQL Server Management Studio
   - Ejecutar el archivo `database.sql`
   - Verificar que se crearon las tablas correctamente

3. **Configurar la conexión**
   - Copiar `config/db.example.php` como `config/db.php`
   - Editar `config/db.php` con tus credenciales:
   ```php
   $serverName = "localhost";
   $database = "caja_chicharron";
   $username = "tu_usuario";
   $password = "tu_contraseña";
   ```

4. **Iniciar XAMPP**
   - Iniciar Apache
   - Iniciar SQL Server
   - Acceder a `http://localhost/Litzy/public/`

## 📱 Uso del Sistema

### Gestión de Productos
1. Ir a **Productos** desde el navbar
2. **Agregar productos** con nombre, precio, stock e imagen
3. **Editar o eliminar** productos existentes
4. **Subir imágenes** para mejor presentación

### Realizar Ventas
1. Ir a **Ventas** desde el navbar
2. **Seleccionar productos** haciendo clic en las cards
3. **Ajustar cantidades** según el stock disponible
4. **Finalizar venta** - el stock se descuenta automáticamente

### Gestionar Inventario
1. Ir a **Inventario** desde el navbar
2. **Ver stock actual** de todos los productos
3. **Agregar stock** cuando compres productos nuevos
4. **Registrar proveedores** y costos (opcional)

## 🗄️ Estructura de Base de Datos

### Tablas Principales
- **`usuarios`** - Gestión de usuarios del sistema
- **`productos`** - Catálogo de productos con stock
- **`ventas`** - Registro de ventas realizadas
- **`detalle_ventas`** - Detalle de productos vendidos
- **`movimientos_inventario`** - Historial de entradas/salidas

## 🎯 Funcionalidades Clave

### Control de Stock Automático
- ✅ **Descuento automático** al vender
- ✅ **Validación de stock** antes de vender
- ✅ **Alertas de stock bajo**
- ✅ **Prevención de ventas** sin stock

### Gestión de Inventario
- ✅ **Agregar stock** fácilmente
- ✅ **Registro de proveedores**
- ✅ **Control de costos**
- ✅ **Historial de movimientos**

### Interfaz de Usuario
- ✅ **Diseño moderno** y atractivo
- ✅ **Responsive** para móviles
- ✅ **Navegación intuitiva**
- ✅ **Feedback visual** inmediato

## 🔧 Personalización

### Colores y Estilos
- Editar archivos CSS en la carpeta `css/`
- Modificar gradientes y colores según tu marca
- Ajustar animaciones y transiciones

### Funcionalidades Adicionales
- Agregar códigos de barras
- Implementar descuentos por volumen
- Crear reportes de ventas
- Agregar impresión de tickets

## 📞 Soporte

Para dudas o problemas:
1. Revisar la documentación
2. Verificar configuración de base de datos
3. Comprobar logs de error de PHP
4. Contactar al desarrollador: Yassir Paez 

## 🚀 Próximas Mejoras -- God willing

- [ ] Sistema de códigos de barras
- [ ] Reportes y estadísticas avanzadas
- [ ] Integración con impresoras térmicas
- [ ] App móvil nativa
- [ ] Backup automático de datos

---

**¡Disfruta gestionando tu microempresa de chicharrones! 🥓✨**
