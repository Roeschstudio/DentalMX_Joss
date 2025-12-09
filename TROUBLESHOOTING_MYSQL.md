# 🔧 Solución de Problemas - MySQL en MAMP

## Error: "No se puede establecer una conexión"

Si ves este error en la pantalla de login o en cualquier página de DentalMX:

```
ERROR - ... Error DB en login: Unable to connect to the database.
Main connection [MySQLi]: No se puede establecer una conexión ya que el equipo de destino denegó expresamente dicha conexión
```

### 🔍 Diagnóstico Automático

Ejecuta el script de diagnóstico:

```bash
cd /ruta/a/DentalMX_Joss
chmod +x diagnose-mysql-macos.sh
./diagnose-mysql-macos.sh
```

Este script:
- ✓ Verifica que MAMP esté instalado
- ✓ Comprueba si MySQL está ejecutándose
- ✓ Detecta el puerto en el que está escuchando
- ✓ Verifica la conexión a la base de datos
- ✓ Compara la configuración de .env con la de MAMP
- ✓ Sugiere correcciones automáticas si es necesario

---

## 🚀 Solución Manual

### Opción 1: Asegurar que MAMP esté ejecutándose

1. **Abre MAMP**
2. **Haz clic en "Start Servers"** (o "Iniciar Servidores")
3. **Espera a que ambos indicadores estén en verde:**
   - Apache: Verde
   - MySQL: Verde
4. **Espera 5-10 segundos** para que todo esté completamente iniciado

### Opción 2: Verificar el puerto de MySQL

MAMP puede ejecutar MySQL en diferentes puertos. El más común es:

**MAMP estándar:**
- Puerto: `3306` ← Más común
- Socket: `/Applications/MAMP/tmp/mysql.sock`

**MAMP con múltiples versiones:**
- Puerto: Puede variar (3307, 3308, etc.)

Para verificar qué puerto usa tu MAMP:

```bash
# Ver todos los puertos que MAMP está usando
lsof -i | grep mysql

# O buscar directamente el puerto de MySQL
netstat -an | grep LISTEN | grep mysql
```

### Opción 3: Actualizar .env con el puerto correcto

1. **Abre el archivo `.env`** en tu editor de código
2. **Busca la línea:**
   ```
   database.default.port = 3306
   ```
3. **Si ves un puerto diferente en el diagnóstico, cámbialo:**
   ```
   database.default.port = 3307
   ```
4. **Guarda el archivo**
5. **Recarga tu navegador** (Cmd + R)

### Opción 4: Reiniciar MAMP completamente

Si nada funciona, reinicia MAMP:

1. **Haz clic en "Stop Servers"**
2. **Espera 5 segundos**
3. **Haz clic en "Start Servers"**
4. **Espera a que los indicadores estén verdes**
5. **Recarga tu navegador**

---

## 🔐 Verificar Credenciales de MySQL

Las credenciales por defecto en MAMP son:

| Parámetro | Valor |
|-----------|-------|
| **Host** | `localhost` o `127.0.0.1` |
| **Puerto** | `3306` (típico) |
| **Usuario** | `root` |
| **Contraseña** | (vacía - sin contraseña) |

Si estas no coinciden con tu configuración, actualiza el archivo `.env`:

```env
# .env
database.default.hostname = localhost
database.default.port = 3306
database.default.username = root
database.default.password = 
database.default.database = engsigne_magic_dental
```

---

## 🗄️ Verificar que la Base de Datos Exista

Para verificar que la base de datos fue creada correctamente:

1. **Abre phpMyAdmin** (generalmente en `http://localhost:8888/phpmyadmin`)
2. **Inicia sesión** con usuario `root` (sin contraseña)
3. **Busca `engsigne_magic_dental`** en la lista de bases de datos
4. **Si existe, haz clic en ella y verifica que tenga las tablas:**
   - usuarios
   - pacientes
   - citas
   - etc.

---

## 🔧 Limpiar Caché (si ya corregiste el puerto)

Si actualicaste el puerto y aún ves el error:

1. **Detén el servidor Apache en MAMP**
2. **Ejecuta en Terminal:**
   ```bash
   cd /ruta/a/DentalMX_Joss
   rm -rf writable/cache/*
   rm -rf writable/logs/log-*.log
   ```
3. **Inicia el servidor Apache nuevamente**
4. **Recarga tu navegador** (sin caché: Cmd + Shift + R)

---

## 📊 Ver Logs de Error Detallados

Si el error persiste, revisa los logs:

```bash
# Ver los últimos 50 errores
tail -50 /ruta/a/DentalMX_Joss/writable/logs/log-*.log

# Ver todo el archivo de log (útil para buscar patrones)
cat /ruta/a/DentalMX_Joss/writable/logs/log-*.log | grep "ERROR"
```

---

## ✅ Checklist de Solución

- [ ] MAMP está instalado
- [ ] MAMP Servers están ejecutándose (indicadores verdes)
- [ ] MySQL está escuchando en el puerto correcto
- [ ] El puerto en `.env` coincide con el puerto de MAMP
- [ ] La base de datos `engsigne_magic_dental` existe
- [ ] phpMyAdmin muestra la base de datos correctamente
- [ ] El archivo `.env` tiene el puerto correcto
- [ ] El caché se limpió
- [ ] El navegador se recargó sin caché (Cmd + Shift + R)

---

## 💬 Contacto

Si después de seguir todos estos pasos aún tienes problemas:

1. **Ejecuta el diagnóstico nuevamente** y guarda la salida
2. **Verifica los logs**: `writable/logs/log-*.log`
3. **Contacta al equipo de soporte** con:
   - Salida del diagnóstico
   - Último error del log
   - Versión de MAMP
   - Versión de macOS

---

**Última actualización:** 2025-12-09  
**Versión:** 1.0.0
