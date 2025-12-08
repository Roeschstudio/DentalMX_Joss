# 🦷 DentalMX - Guía de Instalación Rápida

## Instalación Automática (Recomendado)

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

### Opciones del Instalador

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

## Instalación Manual

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

### Error: "No se puede conectar a MySQL"
- Verifique que MySQL esté ejecutándose en XAMPP Control Panel
- Haga clic en "Start" en la fila de MySQL

### Error: "XAMPP no encontrado"
- Especifique la ruta de XAMPP: `.\Install-DentalMX.ps1 -XamppPath "D:\xampp"`

### Error: "Execution Policy"
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

## Soporte

Para soporte técnico, contacte a:
- **Email:** soporte@dentalmx.com
- **GitHub Issues:** [Reportar un problema](https://github.com/Roeschstudio/DentalMX_Joss/issues)

---
© 2024 Roesch Studio - DentalMX v1.0.0
