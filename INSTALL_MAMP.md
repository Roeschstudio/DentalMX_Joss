# 🦷 DentalMX - Guía de Instalación para MAMP (macOS)

## ⚡ Instalación Rápida (Recomendado)

```bash
# 1. Navega a la carpeta del proyecto
cd /Applications/MAMP/htdocs/DentalMX_Joss

# 2. Da permisos y ejecuta el instalador
chmod +x install-mamp.sh
./install-mamp.sh
```

El instalador configurará automáticamente:
- ✅ Conexión a MySQL (puerto 8889)
- ✅ URL base correcta
- ✅ Permisos de archivos
- ✅ Base de datos

---

## 📋 Instalación Manual

### Paso 1: Configurar MAMP

1. **Abrir MAMP** y verificar los puertos:
   - Apache: `8888` (típico)
   - MySQL: `8889` (típico)

2. **Iniciar servidores** (botón Start Servers)

### Paso 2: Copiar archivo de configuración

```bash
# Copiar el archivo .env específico para MAMP
cp .env.mamp .env
```

### Paso 3: Editar .env (si es necesario)

Abre `.env` y verifica:

```env
# URL de la aplicación (ajusta el nombre de la carpeta)
app.baseURL = 'http://localhost:8888/DentalMX_Joss/public/'

# Puerto de MySQL (8889 es típico en MAMP)
database.default.port = 8889
```

### Paso 4: Importar base de datos

```bash
# Usar MySQL de MAMP
/Applications/MAMP/Library/bin/mysql -u root -h 127.0.0.1 -P 8889 -e "CREATE DATABASE IF NOT EXISTS engsigne_magic_dental"
/Applications/MAMP/Library/bin/mysql -u root -h 127.0.0.1 -P 8889 engsigne_magic_dental < database/schema.sql
/Applications/MAMP/Library/bin/mysql -u root -h 127.0.0.1 -P 8889 engsigne_magic_dental < database/initial_data.sql
```

### Paso 5: Configurar permisos

```bash
chmod -R 777 writable
chmod -R 777 public/uploads
```

### Paso 6: Acceder a la aplicación

Abre en tu navegador:
```
http://localhost:8888/DentalMX_Joss/public/
```

**Credenciales:**
- Email: `admin@dentalmx.com`
- Password: `admin123`

---

## 🔧 Solución de Problemas

### Error: "Internal Server Error" (500)

**Causa más común:** MySQL no está conectando.

**Solución:**
1. Verifica que MAMP esté ejecutándose
2. Verifica el puerto de MySQL en `.env`:
   ```env
   database.default.port = 8889
   ```
3. Ejecuta el diagnóstico:
   ```bash
   chmod +x diagnose-mysql-macos.sh
   ./diagnose-mysql-macos.sh
   ```

### Error: "Unable to connect to database"

**Causa:** Puerto de MySQL incorrecto.

**Solución:**
1. Abre MAMP > Preferences > Ports
2. Anota el puerto de MySQL (ej: 8889)
3. Actualiza `.env`:
   ```env
   database.default.port = 8889
   ```

### Después del login, página vacía "index.php"

**Causa:** mod_rewrite no está habilitado en MAMP.

**Solución 1 - Habilitar mod_rewrite (Recomendado):**
```bash
chmod +x enable-mod-rewrite-mamp.sh
./enable-mod-rewrite-mamp.sh
```

Luego edita `/Applications/MAMP/conf/apache/httpd.conf`:
1. Busca: `<Directory "/Applications/MAMP/htdocs">`
2. Cambia: `AllowOverride None` → `AllowOverride All`
3. Reinicia MAMP

**Solución 2 - Usar URLs con index.php:**
Si no puedes habilitar mod_rewrite, edita `.env`:
```env
app.indexPage = 'index.php'
```

Con esta configuración, las URLs serán:
- `http://localhost:8888/DentalMX_Joss/public/index.php/login`
- `http://localhost:8888/DentalMX_Joss/public/index.php/dashboard`

### Error: "Class not found" o "File not found"

**Solución:**
```bash
# Limpiar caché
rm -rf writable/cache/*
rm -rf writable/session/*
```

### Las URLs AJAX no funcionan

**Causa:** Misma que el problema de página vacía.

**Solución:** Habilitar mod_rewrite (ver arriba) o usar `app.indexPage = 'index.php'`

---

## 📁 Archivos de Configuración

| Archivo | Propósito |
|---------|-----------|
| `.env` | Configuración activa (copia de .env.example o .env.mamp) |
| `.env.mamp` | Configuración preconfigurada para MAMP |
| `.env.example` | Plantilla general |
| `install-mamp.sh` | Instalador automático para MAMP |
| `diagnose-mysql-macos.sh` | Diagnóstico de conexión MySQL |
| `enable-mod-rewrite-mamp.sh` | Habilitar mod_rewrite en MAMP |

---

## 🔐 Credenciales por Defecto

### Aplicación
| Campo | Valor |
|-------|-------|
| Email | `admin@dentalmx.com` |
| Password | `admin123` |

### Base de Datos (MAMP típico)
| Campo | Valor |
|-------|-------|
| Host | `127.0.0.1` |
| Puerto | `8889` |
| Usuario | `root` |
| Password | (vacío) |
| Base de datos | `engsigne_magic_dental` |

---

## 📞 Soporte

Si después de seguir estos pasos aún tienes problemas:

1. Ejecuta: `./diagnose-mysql-macos.sh`
2. Revisa los logs: `cat writable/logs/log-*.log | tail -50`
3. Abre un issue en GitHub con:
   - Salida del diagnóstico
   - Últimos errores del log
   - Tu versión de MAMP

---

**DentalMX** - Sistema de Gestión Dental © 2024 Roesch Studio
