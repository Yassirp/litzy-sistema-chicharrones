# 🚀 Despliegue en Netlify - Litzy

Guía paso a paso para desplegar el sistema Litzy en Netlify sin problemas con las imágenes.

## 📋 Requisitos Previos

1. **Cuenta de Netlify** (gratis)
2. **Repositorio en GitHub** (ya configurado)
3. **Base de datos externa** (SQL Server en la nube)

## 🔧 Configuración en Netlify

### Paso 1: Conectar GitHub
1. Ve a [netlify.com](https://netlify.com)
2. Inicia sesión con tu cuenta
3. Haz clic en **"New site from Git"**
4. Selecciona **"GitHub"**
5. Busca y selecciona `litzy-sistema-chicharrones`

### Paso 2: Configurar Build Settings
```
Build command: (dejar vacío)
Publish directory: public
```

### Paso 3: Configurar Variables de Entorno
En la sección **"Site settings" > "Environment variables"**, agrega:

```
DB_SERVER = tu_servidor_db
DB_NAME = caja_chicharron
DB_USER = tu_usuario
DB_PASS = tu_contraseña
APP_URL = https://tu-sitio.netlify.app
APP_ENV = production
```

### Paso 4: Configurar Redirecciones
Netlify usará automáticamente el archivo `netlify.toml` que ya está configurado.

## 🖼️ Configuración de Imágenes

### ✅ Imágenes Incluidas
Las siguientes imágenes ya están en el repositorio:
- `picada-chorizo.jpg`
- `mazorca.jpg`
- `picada-mixta.jpg`
- `Chicharron-personal.jpg`
- `Chicharron-para2.jpg`
- `Picada-mixta-2.jpg`

### 🔧 Rutas de Imágenes
El sistema está configurado para:
- **Imágenes de productos**: `/uploads/nombre-imagen.jpg`
- **Imagen por defecto**: `/img/todoalbarril.jpg`
- **Caché**: 1 año para mejor rendimiento

## 🗄️ Base de Datos

### Opciones Recomendadas:
1. **Azure SQL Database** (Microsoft)
2. **AWS RDS** (Amazon)
3. **Google Cloud SQL**
4. **PlanetScale** (MySQL compatible)

### Configuración Mínima:
- **CPU**: 1 vCore
- **RAM**: 2 GB
- **Almacenamiento**: 20 GB
- **Conexiones**: 10-20 simultáneas

## 🚀 Pasos de Despliegue

1. **Conectar repositorio** a Netlify
2. **Configurar variables** de entorno
3. **Configurar base de datos** externa
4. **Desplegar** el sitio
5. **Verificar** que las imágenes cargan correctamente

## 🔍 Verificación Post-Despliegue

### Checklist:
- [ ] El sitio carga correctamente
- [ ] Las imágenes de productos aparecen
- [ ] La base de datos se conecta
- [ ] Los formularios funcionan
- [ ] El sistema de ventas opera
- [ ] El cálculo de ganancias funciona

## 🛠️ Solución de Problemas

### Imágenes no aparecen:
1. Verificar que las imágenes están en `/uploads/`
2. Revisar las redirecciones en `netlify.toml`
3. Verificar los headers de caché

### Base de datos no conecta:
1. Verificar variables de entorno
2. Revisar configuración de firewall
3. Verificar credenciales

### Errores de PHP:
1. Revisar logs en Netlify
2. Verificar configuración de PHP
3. Revisar archivos de configuración

## 📞 Soporte

Si tienes problemas:
1. Revisar logs de Netlify
2. Verificar configuración de variables
3. Contactar al desarrollador: Yassir Paez

---

**¡Tu sistema estará funcionando perfectamente en Netlify! 🎉**
