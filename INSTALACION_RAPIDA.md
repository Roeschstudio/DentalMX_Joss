# 🦷 DentalMX - Guía de Instalación Rápida

## Instalación en Windows (XAMPP)

### Prerrequisitos
1. **XAMPP** instalado (incluye PHP 8.0+ y MySQL)
2. **Apache y MySQL** ejecutándose desde XAMPP Control Panel
3. **PowerShell 5.1+** (incluido en Windows 10/11)

### Pasos de Instalación

1. **Descargue el proyecto** desde GitHub:
   - Clone el repositorio o descargue el ZIP
   - Extraiga en `C:\xampp\htdocs\DentalMX_Joss`

2. **Ejecute el instalador**:
   - Abra PowerShell como Administrador
   - Navegue a la carpeta del proyecto:
   ```powershell
   cd C:\xampp\htdocs\DentalMX_Joss
   ```
   - Ejecute el instalador:
   ```powershell
   .\Install-DentalMX.ps1
   ```

3. **Acceda a la aplicación**:
   - Abra su navegador en: `http://localhost/DentalMX_Joss/public`
   - **Email:** `admin@dentalmx.com`
   - **Contraseña:** `admin123`

### Opciones del Instalador Windows

```powershell
# Instalación con configuración personalizada
.\Install-DentalMX.ps1 -XamppPath "D:\xampp" -DatabasePassword "mipassword"

# Todos los parámetros disponibles:
# -XamppPath          Ruta de XAMPP (default: C:\xampp)
# -DatabaseName       Nombre de la BD (default: engsigne_magic_dental)
# -DatabaseUser       Usuario MySQL (default: root)
# -DatabasePassword   Contraseña MySQL (default: vacío)
# -BaseUrl            URL base de la aplicación
```

---

## Instalación en macOS (MAMP)

### Prerrequisitos
1. **MAMP** instalado (incluye PHP 8.0+ y MySQL)
2. **Servidores MAMP** ejecutándose (Start Servers)
3. **Terminal** de macOS

### Pasos de Instalación

1. **Descargue el proyecto** desde GitHub:
   - Clone el repositorio o descargue el ZIP
   - Extraiga en `/Applications/MAMP/htdocs/DentalMX_Joss`

2. **Ejecute el instalador**:
   - Abra Terminal
   - Navegue a la carpeta del proyecto:
   ```bash
   cd /Applications/MAMP/htdocs/DentalMX_Joss
   ```
   - Dé permisos de ejecución al instalador:
   ```bash
   chmod +x install-macos.sh
   chmod +x setup-htaccess-macos.sh
   ```
   - Ejecute el instalador:
   ```bash
   ./install-macos.sh
   ```

3. **Acceda a la aplicación**:
   - El instalador configurará automáticamente las redirecciones
   - Abra su navegador en: `http://localhost:8888/DentalMX_Joss/public`
   - **Nota:** Ya NO necesita agregar `/index.php/` en la URL
   - **Email:** `admin@dentalmx.com`
   - **Contraseña:** `admin123`

**¿Problemas con las redirecciones?**

Si aún necesita usar `/index.php/` en la URL, ejecute manualmente:
```bash
./setup-htaccess-macos.sh
```

Este script:
1. Crea automáticamente el archivo `.htaccess` correcto
2. Configura las redirecciones para funcionar sin `/index.php/`
3. Verifica si `mod_rewrite` está habilitado
4. Proporciona soluciones si hay problemas

**Después de ejecutar el script:**
- Reinicia los servidores MAMP (Stop → Start)
- Recarga tu navegador
- Ahora debería funcionar sin `/index.php/`

### Opciones del Instalador macOS

```bash
# Instalación con configuración personalizada
./install-macos.sh --password "mipassword"

# Todos los parámetros disponibles:
# -m, --mamp-path     Ruta de MAMP (default: /Applications/MAMP)
# -d, --database      Nombre de la BD (default: engsigne_magic_dental)
# -u, --user          Usuario MySQL (default: root)
# -p, --password      Contraseña MySQL (default: root)
# -b, --base-url      URL base de la aplicación

# Ver ayuda
./install-macos.sh --help
```

### Nota sobre MAMP
- MAMP usa el puerto **8888** para Apache y **8889** para MySQL
- La URL por defecto será: `http://localhost:8888/DentalMX_Joss/public`
- Las credenciales por defecto de MySQL en MAMP son: `root` / `root`

---

Si prefiere instalar manualmente:

1. Cree la base de datos:
   ```sql
   CREATE DATABASE engsigne_magic_dental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Importe el schema:
   ```bash
   mysql -u root engsigne_magic_dental < database/schema.sql
   ```

3. Importe los datos iniciales:
   ```bash
   mysql -u root engsigne_magic_dental < database/initial_data.sql
   ```

4. Copie `.env.example` a `.env` y configure las credenciales

5. Instale dependencias (si es necesario):
   ```bash
   composer install
   ```

## Solución de Problemas

### Windows (XAMPP)

#### Error: "No se puede conectar a MySQL"
- Verifique que MySQL esté ejecutándose en XAMPP Control Panel
- Haga clic en "Start" en la fila de MySQL

#### Error: "XAMPP no encontrado"
- Especifique la ruta de XAMPP: `.\Install-DentalMX.ps1 -XamppPath "D:\xampp"`

#### Error: "Execution Policy"
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

---

### macOS (MAMP)

#### Error: "Aún necesito agregar /index.php/ en la URL"

Este es el problema más común en macOS. Solución:

1. **Ejecutar el script de .htaccess:**
   ```bash
   cd /Applications/MAMP/htdocs/DentalMX_Joss
   chmod +x setup-htaccess-macos.sh
   ./setup-htaccess-macos.sh
   ```

2. **Reiniciar MAMP:**
   - Abre MAMP
   - Haz clic en "Stop Servers"
   - Espera 3 segundos
   - Haz clic en "Start Servers"
   - Recarga tu navegador

3. **Verificar que mod_rewrite está habilitado:**
   ```bash
   /Applications/MAMP/Library/bin/httpd -M | grep rewrite
   ```
   
   Si ves `rewrite_module (shared)`, está habilitado. Si no:
   ```bash
   sudo nano /Applications/MAMP/conf/apache/httpd.conf
   ```
   - Busca: `#LoadModule rewrite_module`
   - Quita el `#` al principio (descomenta)
   - Guarda: `Ctrl+O`, `Enter`, `Ctrl+X`
   - Reinicia MAMP

#### Error: "No se puede conectar a MySQL en MAMP"
- Asegúrate de que MAMP esté ejecutándose
- Verifica que el puerto sea **8889** para MySQL
- Las credenciales por defecto son: `root` / `root`
- En Terminal, prueba:
  ```bash
  /Applications/MAMP/Library/bin/mysql -u root -p"root"
  ```

#### Error: "Archivo test-redirect.php no funciona"

El archivo de prueba se crea en `public/test-redirect.php`. Si al abrirlo no ves el mensaje de confirmación:

1. Verifica que esté en la ruta correcta:
   ```bash
   ls -la /Applications/MAMP/htdocs/DentalMX_Joss/public/test-redirect.php
   ```

2. Comprueba los permisos:
   ```bash
   chmod 644 /Applications/MAMP/htdocs/DentalMX_Joss/public/test-redirect.php
   ```

3. Revisa el log de errores de Apache:
   ```bash
   tail -f /Applications/MAMP/logs/apache_error.log
   ```

#### Error: "Permisos denegados al ejecutar script"

Asegúrate de dar permisos de ejecución:
```bash
chmod +x install-macos.sh
chmod +x setup-htaccess-macos.sh
```

---

## Soporte

Para soporte técnico, contacte a:
- **Email:** soporte@dentalmx.com
- **GitHub Issues:** [Reportar un problema](https://github.com/Roeschstudio/DentalMX_Joss/issues)

---
© 2024 Roesch Studio - DentalMX v1.0.0
