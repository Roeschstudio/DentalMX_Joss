# Guía de Instalación Completa - DentalMX

Esta guía proporciona instrucciones detalladas paso a paso para instalar DentalMX en su servidor o entorno local.

## Tabla de Contenidos

1. [Requisitos Previos](#requisitos-previos)
2. [Instalación en Windows](#instalación-en-windows)
3. [Instalación en Linux](#instalación-en-linux)
4. [Instalación en macOS](#instalación-en-macos)
5. [Configuración de Base de Datos](#configuración-de-base-de-datos)
6. [Configuración de la Aplicación](#configuración-de-la-aplicación)
7. [Verificación de Instalación](#verificación-de-instalación)
8. [Resolución de Problemas](#resolución-de-problemas)

## Requisitos Previos

### Software Necesario

- **PHP 8.1 o superior**
- **MySQL 8.0 o superior** (o MariaDB 10.3+)
- **Composer 2.0+**
- **Git** (opcional, para clonar repositorio)
- **Servidor Web**: Apache 2.4+ o Nginx 1.18+

### Extensiones PHP Requeridas

Verifique que tenga instaladas las siguientes extensiones:

```bash
php -m | grep -E 'intl|mbstring|mysqli|gd|json|xml'
```

Si falta alguna extensión, instálela:

**Ubuntu/Debian:**
```bash
sudo apt-get install php8.1-intl php8.1-mbstring php8.1-mysqli php8.1-gd php8.1-xml
```

**Windows:**
Edite `php.ini` y descomente las líneas:
```ini
extension=intl
extension=mbstring
extension=mysqli
extension=gd
```

## Instalación en Windows

### 1. Instalar XAMPP (Recomendado)

1. Descargue XAMPP desde: https://www.apachefriends.org/
2. Instale con PHP 8.1 o superior
3. Inicie Apache y MySQL desde el panel de control

### 2. Instalar Composer

1. Descargue desde: https://getcomposer.org/Composer-Setup.exe
2. Ejecute el instalador
3. Verifique: `composer --version`

### 3. Clonar el Proyecto

```cmd
cd C:\xampp\htdocs
git clone https://github.com/Roeschstudio/DentalMX_Joss.git
cd DentalMX_Joss
```

O descargue el ZIP y extraiga en `C:\xampp\htdocs\DentalMX_Joss`

### 4. Instalar Dependencias

```cmd
composer install
```

### 5. Configurar Base de Datos

1. Abra phpMyAdmin: http://localhost/phpmyadmin
2. Cree una nueva base de datos: `dentalmx`
3. Importe los archivos SQL:
   - Vaya a la pestaña "Importar"
   - Seleccione `database/schema.sql`
   - Click en "Continuar"
   - Repita con `database/initial_data.sql`

### 6. Configurar la Aplicación

1. Copie el archivo de configuración:
```cmd
copy .env.example .env
```

2. Edite `.env` con Notepad:
```env
CI_ENVIRONMENT = production

app.baseURL = 'http://localhost/DentalMX_Joss/public/'

database.default.hostname = localhost
database.default.database = dentalmx
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 7. Configurar Permisos

Asegúrese de que la carpeta `writable/` tenga permisos de escritura.

### 8. Acceder a la Aplicación

Abra su navegador: http://localhost/DentalMX_Joss/public/

## Instalación en Linux

### 1. Instalar Requisitos

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install php8.1 php8.1-intl php8.1-mbstring php8.1-mysqli php8.1-gd php8.1-xml php8.1-curl
sudo apt install mysql-server apache2
sudo apt install composer git
```

**CentOS/RHEL:**
```bash
sudo dnf install php php-intl php-mbstring php-mysqli php-gd php-xml php-json
sudo dnf install mysql-server httpd
sudo dnf install composer git
```

### 2. Configurar MySQL

```bash
sudo systemctl start mysql
sudo systemctl enable mysql
sudo mysql_secure_installation
```

### 3. Clonar el Proyecto

```bash
cd /var/www/html
sudo git clone https://github.com/Roeschstudio/DentalMX_Joss.git
cd DentalMX_Joss
sudo chown -R $USER:www-data .
```

### 4. Instalar Dependencias

```bash
composer install
```

### 5. Configurar Base de Datos

```bash
mysql -u root -p
```

Dentro de MySQL:
```sql
CREATE DATABASE dentalmx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dentalmx_user'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT ALL PRIVILEGES ON dentalmx.* TO 'dentalmx_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Importar datos:
```bash
mysql -u dentalmx_user -p dentalmx < database/schema.sql
mysql -u dentalmx_user -p dentalmx < database/initial_data.sql
```

### 6. Configurar la Aplicación

```bash
cp .env.example .env
nano .env
```

Edite:
```env
CI_ENVIRONMENT = production

app.baseURL = 'http://tu-dominio.com/'

database.default.hostname = localhost
database.default.database = dentalmx
database.default.username = dentalmx_user
database.default.password = password_seguro
database.default.DBDriver = MySQLi
```

### 7. Configurar Permisos

```bash
sudo chmod -R 755 writable/
sudo chmod -R 755 public/uploads/
sudo chown -R www-data:www-data writable/
sudo chown -R www-data:www-data public/uploads/
```

### 8. Configurar Apache

Cree un archivo de configuración:
```bash
sudo nano /etc/apache2/sites-available/dentalmx.conf
```

Contenido:
```apache
<VirtualHost *:80>
    ServerName tu-dominio.com
    DocumentRoot /var/www/html/DentalMX_Joss/public

    <Directory /var/www/html/DentalMX_Joss/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dentalmx-error.log
    CustomLog ${APACHE_LOG_DIR}/dentalmx-access.log combined
</VirtualHost>
```

Habilite el sitio:
```bash
sudo a2ensite dentalmx.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## Instalación en macOS

### 1. Instalar Homebrew (si no está instalado)

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### 2. Instalar Requisitos

```bash
brew install php@8.1
brew install mysql
brew install composer
```

### 3. Iniciar MySQL

```bash
brew services start mysql
```

### 4. Seguir Pasos Similares a Linux

Los pasos de clonación, configuración y permisos son similares a la instalación en Linux.

## Configuración de Base de Datos

### Estructura de Tablas Principales

El sistema crea las siguientes tablas:

- `usuarios` - Usuarios del sistema
- `pacientes` - Datos de pacientes
- `citas` - Agenda de citas
- `presupuestos` - Presupuestos y cotizaciones
- `tratamientos` - Tratamientos dentales
- `servicios` - Catálogo de servicios
- `inventario` - Control de inventario
- `facturas` - Facturación
- `pagos` - Registro de pagos

### Datos Iniciales

El archivo `initial_data.sql` incluye:

- Usuario administrador (admin/admin123)
- Catálogo de servicios básicos
- Estados y tipos predefinidos
- Configuración inicial del sistema

## Configuración de la Aplicación

### Variables de Entorno Importantes

```env
# Entorno
CI_ENVIRONMENT = production  # development para desarrollo

# Base URL
app.baseURL = 'https://tudominio.com/'

# Base de datos
database.default.hostname = localhost
database.default.database = dentalmx
database.default.username = usuario
database.default.password = contraseña

# Sesiones
app.sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler'
app.sessionCookieName = 'dentalmx_session'
app.sessionExpiration = 7200

# Correo (Opcional)
email.fromEmail = noreply@tudominio.com
email.fromName = DentalMX
email.SMTPHost = smtp.tudominio.com
email.SMTPUser = usuario@tudominio.com
email.SMTPPass = contraseña
email.SMTPPort = 587
```

## Verificación de Instalación

### 1. Verificar PHP

```bash
php -v
# Debe mostrar PHP 8.1 o superior
```

### 2. Verificar Extensiones

```bash
php -m
# Debe incluir: intl, mbstring, mysqli, gd, json, xml
```

### 3. Verificar Base de Datos

```bash
mysql -u usuario -p -e "SHOW DATABASES LIKE 'dentalmx';"
# Debe mostrar la base de datos
```

### 4. Verificar Permisos

```bash
ls -la writable/
# Debe tener permisos de escritura (755 o 775)
```

### 5. Probar Acceso

Abra en navegador: http://tu-dominio.com

Debe ver la página de inicio de sesión.

### 6. Iniciar Sesión

- Email: `admin@dentalmx.com`
- Contraseña: `admin123`

Si puede acceder al dashboard, la instalación fue exitosa.

## Resolución de Problemas

### Error: "Undefined class constant 'FILTER_SANITIZE_STRING'"

**Causa**: PHP 8.1+ deprecó esta constante

**Solución**: Ya está corregido en el código

### Error: "Database connection failed"

**Causas comunes:**
1. MySQL no está corriendo: `sudo systemctl start mysql`
2. Credenciales incorrectas en `.env`
3. Base de datos no existe: crearla manualmente
4. Usuario sin permisos: otorgar permisos con `GRANT ALL`

### Error: "Undefined array key"

**Causa**: Datos incompletos en base de datos

**Solución**: Asegúrese de importar ambos archivos SQL:
```bash
mysql -u usuario -p dentalmx < database/schema.sql
mysql -u usuario -p dentalmx < database/initial_data.sql
```

### Página en blanco

**Solución**:
1. Active modo desarrollo en `.env`: `CI_ENVIRONMENT = development`
2. Revise logs: `writable/logs/log-YYYY-MM-DD.log`
3. Verifique permisos en `writable/`

### Error 404 en todas las páginas

**Causa**: mod_rewrite no está habilitado

**Solución Ubuntu/Debian**:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Error de permisos al subir archivos

**Solución**:
```bash
sudo chmod -R 755 public/uploads/
sudo chown -R www-data:www-data public/uploads/
```

### Performance lenta

**Optimizaciones**:

1. Habilite caché en `.env`:
```env
cache.handler = file
cache.ttl = 3600
```

2. Configure OPcache en `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
```

3. Optimice MySQL:
```sql
OPTIMIZE TABLE pacientes, citas, presupuestos;
```

## Mantenimiento

### Respaldos de Base de Datos

```bash
# Crear respaldo
mysqldump -u usuario -p dentalmx > backup_$(date +%Y%m%d).sql

# Restaurar respaldo
mysql -u usuario -p dentalmx < backup_20241207.sql
```

### Limpiar Caché

```bash
php spark cache:clear
```

### Ver Logs

```bash
tail -f writable/logs/log-$(date +%Y-%m-%d).log
```

### Actualización del Sistema

```bash
git pull origin main
composer update
php spark migrate
```

## Soporte

Si continúa teniendo problemas:

1. Revise los logs en `writable/logs/`
2. Consulte la documentación: `docs/`
3. Cree un issue en GitHub con:
   - Versión de PHP: `php -v`
   - Versión de MySQL: `mysql --version`
   - Sistema operativo
   - Mensaje de error completo
   - Logs relevantes

---

**¿Instalación exitosa?** Continúe con el [Manual de Usuario](docs/USER_MANUAL.md)
