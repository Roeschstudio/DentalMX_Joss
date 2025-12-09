@echo off
REM ============================================================================
REM DENTALMX - Diagnostico y Solucion de Conexion MySQL en XAMPP (Windows)
REM ============================================================================
REM Este script diagnostica problemas de conexion a MySQL en Windows con XAMPP
REM Version: 1.0.0
REM © 2024 Roesch Studio
REM ============================================================================

setlocal enabledelayedexpansion

cls
echo.
echo ============================================================================
echo  DentalMX - Diagnostico de MySQL en XAMPP
echo ============================================================================
echo.

REM ============================================================================
REM PASO 1: Verificar que XAMPP este instalado
REM ============================================================================
echo [1] Verificando XAMPP...
echo.

if exist "C:\xampp" (
    echo [OK] XAMPP encontrado en C:\xampp
    set XAMPP_PATH=C:\xampp
) else if exist "D:\xampp" (
    echo [OK] XAMPP encontrado en D:\xampp
    set XAMPP_PATH=D:\xampp
) else if exist "E:\xampp" (
    echo [OK] XAMPP encontrado en E:\xampp
    set XAMPP_PATH=E:\xampp
) else (
    echo [ERROR] XAMPP no encontrado en C:, D:, E:
    echo.
    echo Instalacion tipicas de XAMPP:
    echo   - C:\xampp
    echo   - D:\xampp
    echo   - Otra ubicacion personalizada
    echo.
    pause
    exit /b 1
)

echo XAMPP Path: !XAMPP_PATH!
echo.

REM ============================================================================
REM PASO 2: Verificar si MySQL esta ejecutandose
REM ============================================================================
echo [2] Verificando si MySQL esta ejecutandose...
echo.

tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL esta ejecutandose
    for /f "tokens=2" %%a in ('tasklist /FI "IMAGENAME eq mysqld.exe" ^| find "mysqld"') do (
        echo PID: %%a
    )
) else (
    echo [ERROR] MySQL NO esta ejecutandose
    echo.
    echo SOLUCION:
    echo   1. Abre el Control Panel de XAMPP (xampp-control.exe)
    echo   2. Haz clic en "Start" para MySQL
    echo   3. Espera a que el indicador este en verde
    echo   4. Ejecuta este script de nuevo
    echo.
    pause
    exit /b 1
)

echo.

REM ============================================================================
REM PASO 3: Verificar puerto de MySQL
REM ============================================================================
echo [3] Verificando puerto MySQL...
echo.

netstat -ano | find "LISTENING" | find ":3306" >NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL esta escuchando en puerto 3306
    set MYSQL_PORT=3306
) else (
    echo [ERROR] MySQL NO esta escuchando en puerto 3306
    echo.
    echo Puertos MySQL detectados:
    netstat -ano | find "mysqld" 2>NUL
    echo.
    pause
    exit /b 1
)

echo.

REM ============================================================================
REM PASO 4: Probar conexion basica
REM ============================================================================
echo [4] Probando conexion a MySQL...
echo.

REM Buscar mysql.exe
if exist "!XAMPP_PATH!\mysql\bin\mysql.exe" (
    set MYSQL_BIN=!XAMPP_PATH!\mysql\bin\mysql.exe
) else (
    echo [ERROR] mysql.exe no encontrado
    pause
    exit /b 1
)

echo Intentando conectar...

REM Intentar conectar sin contrasena
"!MYSQL_BIN!" -u root -h 127.0.0.1 -P !MYSQL_PORT! -e "SELECT 1" >NUL 2>&1
if "%ERRORLEVEL%"=="0" (
    echo [OK] Conexion a MySQL: EXITOSA (sin contrasena)
    set DB_PASS=
) else (
    echo [ERROR] No se puede conectar a MySQL
    echo.
    pause
    exit /b 1
)

echo.

REM ============================================================================
REM PASO 5: Verificar base de datos
REM ============================================================================
echo [5] Verificando base de datos engsigne_magic_dental...
echo.

"!MYSQL_BIN!" -u root -p"!DB_PASS!" -h 127.0.0.1 -P !MYSQL_PORT! -e "USE engsigne_magic_dental; SHOW TABLES;" 2>NUL | find "usuarios" >NUL

if "%ERRORLEVEL%"=="0" (
    echo [OK] Base de datos y tabla usuarios encontradas
    
    for /f %%a in ('"!MYSQL_BIN!" -u root -p"!DB_PASS!" -h 127.0.0.1 -P !MYSQL_PORT! engsigne_magic_dental -N -e "SELECT COUNT(*) FROM usuarios" 2>NUL"') do (
        echo Usuarios en la base de datos: %%a
    )
) else (
    echo [ERROR] No se puede acceder a la base de datos
    echo.
    echo La base de datos podria no estar importada correctamente.
    echo.
    pause
    exit /b 1
)

echo.

REM ============================================================================
REM PASO 6: Detectar la configuracion actual
REM ============================================================================
echo ============================================================================
echo CONFIGURACION DETECTADA
echo ============================================================================
echo.
echo   Host:       127.0.0.1 (localhost)
echo   Puerto:     !MYSQL_PORT!
echo   Usuario:    root
echo   Contrasena: (sin contrasena)
echo   Base datos: engsigne_magic_dental
echo.

REM ============================================================================
REM PASO 7: Verificar .env
REM ============================================================================
echo [6] Verificando archivo .env...
echo.

REM Obtener el directorio del script
set PROJECT_DIR=%~dp0

set ENV_FILE=%PROJECT_DIR%.env

if not exist "!ENV_FILE!" (
    echo [ERROR] Archivo .env no encontrado en:
    echo   !ENV_FILE!
    echo.
    pause
    exit /b 1
)

echo [OK] .env encontrado
echo.

REM Leer configuracion del .env
for /f "tokens=3" %%a in ('type "!ENV_FILE!" ^| find "database.default.hostname"') do set ENV_HOST=%%a
for /f "tokens=3" %%a in ('type "!ENV_FILE!" ^| find "database.default.port"') do set ENV_PORT=%%a
for /f "tokens=3" %%a in ('type "!ENV_FILE!" ^| find "database.default.username"') do set ENV_USER=%%a
for /f "tokens=3" %%a in ('type "!ENV_FILE!" ^| find "database.default.database"') do set ENV_DB=%%a

echo   Configuracion en .env:
echo.
echo   Host:       !ENV_HOST!
echo   Puerto:     !ENV_PORT!
echo   Usuario:    !ENV_USER!
echo   Base datos: !ENV_DB!
echo.

REM ============================================================================
REM PASO 8: Comparar y mostrar resultado
REM ============================================================================
if "!ENV_PORT!" == "!MYSQL_PORT!" (
    echo [OK] Puerto en .env coincide con MySQL
) else (
    echo [ADVERTENCIA] Puerto mismatch!
    echo   .env tiene:    !ENV_PORT!
    echo   MySQL usa:     !MYSQL_PORT!
    echo.
    echo   SOLUCION:
    echo   Abre el archivo .env en tu editor y cambia:
    echo     database.default.port = !MYSQL_PORT!
    echo.
)

echo.

REM ============================================================================
REM RESUMEN
REM ============================================================================
echo ============================================================================
echo DIAGNOSTICO COMPLETADO
echo ============================================================================
echo.
echo [OK] MySQL esta funcionando correctamente
echo.
echo   Proximos pasos:
echo   1. Si hay mismatch de puertos, actualiza .env
echo   2. Limpiar cache de CodeIgniter (opcional):
echo        rmdir /S /Q writable\cache
echo   3. Recarga tu navegador
echo.
pause
