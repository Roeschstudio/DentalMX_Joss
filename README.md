# DentalMX - Sistema de Gestión Dental Integral

Sistema completo de gestión para clínicas dentales desarrollado con CodeIgniter 4. Incluye gestión de pacientes, citas, presupuestos, inventario, facturación y más.

## 🚀 Características Principales

- **Gestión de Pacientes**: Expedientes completos, historial clínico, documentos
- **Agenda de Citas**: Calendario interactivo, recordatorios automáticos
- **Presupuestos y Cotizaciones**: Generación automática, seguimiento de estados
- **Inventario**: Control de materiales y equipos
- **Facturación**: Generación de facturas, control de pagos
- **Reportes**: Dashboards interactivos, reportes personalizables
- **Multi-usuario**: Roles y permisos configurables
- **Modo Oscuro**: Interfaz moderna adaptable

## 📋 Requisitos del Sistema

- **PHP**: 8.1 o superior
- **MySQL**: 8.0 o superior
- **Composer**: 2.0 o superior
- **Node.js**: 16.0 o superior (para assets)
- **Extensiones PHP requeridas**:
  - intl
  - mbstring
  - mysqli
  - gd
  - json
  - xml

## 🔧 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Roeschstudio/DentalMX_Joss.git
cd DentalMX_Joss
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar base de datos

Copie el archivo de configuración de ejemplo:

```bash
cp .env.example .env
```

Edite `.env` con los datos de su base de datos:

```env
database.default.hostname = localhost
database.default.database = su_base_datos
database.default.username = su_usuario
database.default.password = su_contraseña
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 4. Importar la base de datos

```bash
mysql -u su_usuario -p su_base_datos < database/schema.sql
mysql -u su_usuario -p su_base_datos < database/initial_data.sql
```

### 5. Configurar permisos

```bash
chmod -R 755 writable/
chmod -R 755 public/uploads/
```

### 6. Iniciar el servidor de desarrollo

```bash
php spark serve
```

La aplicación estará disponible en: `http://localhost:8080`

## 🔐 Acceso Inicial

**Usuario administrador por defecto:**
- Usuario: `admin`
- Contraseña: `admin123`

⚠️ **IMPORTANTE**: Cambie las credenciales después del primer inicio de sesión.

## 📁 Estructura del Proyecto

```
DentalMX_Joss/
├── app/
│   ├── Controllers/      # Controladores de la aplicación
│   ├── Models/          # Modelos de datos
│   ├── Views/           # Vistas (frontend)
│   ├── Config/          # Configuración
│   └── Database/        # Migraciones y seeds
├── public/
│   ├── css/            # Hojas de estilo
│   ├── js/             # JavaScript
│   └── uploads/        # Archivos subidos
├── writable/           # Logs y caché
├── database/           # Scripts SQL de instalación
└── docs/              # Documentación adicional
```

## 📖 Documentación Adicional

- [Guía de Instalación Completa](INSTALL.md)
- [Manual de Usuario](docs/USER_MANUAL.md)
- [Documentación de la Base de Datos](docs/DATABASE.md)
- [Guía de Desarrollo](docs/DEVELOPMENT.md)

## 🔄 Actualización

Para actualizar a la última versión:

```bash
git pull origin main
composer update
php spark migrate
```

## 🐛 Solución de Problemas

### Error de permisos en writable/

```bash
chmod -R 755 writable/
chown -R www-data:www-data writable/
```

### Error de conexión a base de datos

Verifique en `.env`:
- Credenciales correctas
- MySQL está corriendo
- Permisos del usuario de base de datos

### Página en blanco

Active el modo de desarrollo en `.env`:

```env
CI_ENVIRONMENT = development
```

Revise los logs en `writable/logs/`

## 🛠️ Stack Tecnológico

- **Backend**: CodeIgniter 4.6.3
- **Base de datos**: MySQL 8.0
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **UI Framework**: Bootstrap 5 + Custom Design System
- **Generación PDF**: Dompdf
- **Testing**: PHPUnit

## 📞 Soporte

Para reportar problemas o solicitar ayuda:
- Crear un issue en GitHub
- Email: soporte@dentalmx.com

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo [LICENSE](LICENSE) para más detalles.

## 👥 Créditos

Desarrollado por Roesch Studio
- GitHub: [@Roeschstudio](https://github.com/Roeschstudio)

---

**DentalMX** - Sistema de Gestión Dental © 2024
