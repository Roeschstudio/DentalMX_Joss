# ============================================================================
# DentalMX - Script de Instalación Automática para Windows (PowerShell)
# Versión: 3.0.0
# Fecha: 2024-12-07
# 
# USO: 
#   .\install.ps1
#   .\install.ps1 -DbName "mi_base_datos" -DbUser "usuario" -DbPass "contraseña"
# ============================================================================

param(
    [string]$DbHost = "localhost",
    [string]$DbName = "dentalmx",
    [string]$DbUser = "root",
    [string]$DbPass = "",
    [string]$DbPort = "3306",
    [string]$BaseUrl = "http://localhost:8080/"
)

# Colores para mensajes
function Write-Success { param($msg) Write-Host "[OK] $msg" -ForegroundColor Green }
function Write-Fail { param($msg) Write-Host "[ERROR] $msg" -ForegroundColor Red }
function Write-Info { param($msg) Write-Host "[INFO] $msg" -ForegroundColor Cyan }
function Write-Warn { param($msg) Write-Host "[WARN] $msg" -ForegroundColor Yellow }

# Banner
Clear-Host
Write-Host ""
Write-Host "============================================================" -ForegroundColor Magenta
Write-Host "       DENTALMX - INSTALADOR AUTOMATICO v3.0" -ForegroundColor Magenta
Write-Host "       Sistema de Gestion para Clinicas Dentales" -ForegroundColor Magenta
Write-Host "============================================================" -ForegroundColor Magenta
Write-Host ""

# Obtener directorio del script
$scriptDir = $PSScriptRoot
if ([string]::IsNullOrEmpty($scriptDir)) {
    $scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
}
if ([string]::IsNullOrEmpty($scriptDir)) {
    $scriptDir = Get-Location
}
Set-Location $scriptDir
Write-Info "Directorio de instalacion: $scriptDir"
Write-Host ""

# ============================================================================
# PASO 1: Verificar PHP
# ============================================================================
Write-Host "--- PASO 1/6: Verificando PHP ---" -ForegroundColor Yellow

$phpOk = $false
try {
    $phpOutput = php -v 2>&1 | Select-Object -First 1
    if ($phpOutput -match "PHP (\d+)\.(\d+)") {
        $major = [int]$Matches[1]
        $minor = [int]$Matches[2]
        if ($major -gt 8 -or ($major -eq 8 -and $minor -ge 1)) {
            Write-Success "PHP $major.$minor detectado"
            $phpOk = $true
        } else {
            Write-Fail "PHP 8.1+ requerido. Version actual: $major.$minor"
        }
    }
} catch {
    Write-Fail "PHP no encontrado en el sistema"
}

if (-not $phpOk) {
    Write-Host ""
    Write-Fail "Instale PHP 8.1 o superior y agregelo al PATH"
    Write-Host "Descarga: https://windows.php.net/download/" -ForegroundColor White
    exit 1
}

# Verificar extensiones críticas
$extensions = php -m 2>&1
$requiredExt = @("intl", "mbstring", "mysqli")
foreach ($ext in $requiredExt) {
    if ($extensions -contains $ext) {
        Write-Success "Extension $ext disponible"
    } else {
        Write-Warn "Extension $ext no encontrada (puede causar problemas)"
    }
}

# ============================================================================
# PASO 2: Verificar Composer
# ============================================================================
Write-Host ""
Write-Host "--- PASO 2/6: Verificando Composer ---" -ForegroundColor Yellow

$composerOk = $false
$composerCmd = "composer"

# Intentar encontrar composer
try {
    $null = composer --version 2>&1
    $composerOk = $true
    Write-Success "Composer encontrado en PATH"
} catch {
    # Buscar composer.phar local
    if (Test-Path "composer.phar") {
        $composerCmd = "php composer.phar"
        $composerOk = $true
        Write-Success "Usando composer.phar local"
    }
}

if (-not $composerOk) {
    Write-Info "Descargando Composer..."
    try {
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php composer-setup.php --quiet
        Remove-Item "composer-setup.php" -ErrorAction SilentlyContinue
        if (Test-Path "composer.phar") {
            $composerCmd = "php composer.phar"
            $composerOk = $true
            Write-Success "Composer descargado correctamente"
        }
    } catch {
        Write-Fail "No se pudo descargar Composer"
    }
}

if (-not $composerOk) {
    Write-Host ""
    Write-Fail "Instale Composer: https://getcomposer.org/download/"
    exit 1
}

# ============================================================================
# PASO 3: Instalar dependencias
# ============================================================================
Write-Host ""
Write-Host "--- PASO 3/6: Instalando dependencias ---" -ForegroundColor Yellow

# Limpiar instalación anterior
if (Test-Path "vendor") {
    Write-Info "Limpiando instalacion anterior..."
    Remove-Item -Recurse -Force "vendor" -ErrorAction SilentlyContinue
}
if (Test-Path "composer.lock") {
    Remove-Item -Force "composer.lock" -ErrorAction SilentlyContinue
}

Write-Info "Ejecutando Composer (esto puede tardar unos minutos)..."
$env:COMPOSER_ALLOW_SUPERUSER = 1

# Ejecutar composer
$composerResult = Invoke-Expression "$composerCmd install --no-dev --optimize-autoloader --no-interaction 2>&1"
$composerResult | ForEach-Object { 
    if ($_ -match "error|fail|exception" -and $_ -notmatch "funding") {
        Write-Host $_ -ForegroundColor Red
    } elseif ($_ -match "Installing|Downloading") {
        Write-Host $_ -ForegroundColor Gray
    }
}

# Verificar que vendor existe
if (-not (Test-Path "vendor/autoload.php")) {
    Write-Fail "Error al instalar dependencias"
    Write-Info "Intentando con composer update..."
    $composerResult = Invoke-Expression "$composerCmd update --no-dev --optimize-autoloader --no-interaction 2>&1"
    
    if (-not (Test-Path "vendor/autoload.php")) {
        Write-Fail "No se pudieron instalar las dependencias"
        exit 1
    }
}

# Optimizar autoloader
Invoke-Expression "$composerCmd dump-autoload --optimize 2>&1" | Out-Null
Write-Success "Dependencias instaladas correctamente"

# ============================================================================
# PASO 4: Configurar archivo .env
# ============================================================================
Write-Host ""
Write-Host "--- PASO 4/6: Configurando entorno ---" -ForegroundColor Yellow

$envContent = @"
#--------------------------------------------------------------------
# DENTALMX - Configuracion generada automaticamente
# Fecha: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
#--------------------------------------------------------------------

CI_ENVIRONMENT = production

app.baseURL = '$BaseUrl'
app.indexPage = ''
app.defaultTimezone = 'America/Mexico_City'
app.defaultLocale = 'es'

database.default.hostname = $DbHost
database.default.database = $DbName
database.default.username = $DbUser
database.default.password = $DbPass
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = $DbPort
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci

app.sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler'
app.sessionCookieName = 'dentalmx_session'
app.sessionExpiration = 7200

logger.threshold = 4
"@

$envContent | Out-File -FilePath ".env" -Encoding UTF8 -Force -NoNewline
Write-Success "Archivo .env creado"

# ============================================================================
# PASO 5: Crear carpetas necesarias
# ============================================================================
Write-Host ""
Write-Host "--- PASO 5/6: Creando estructura de carpetas ---" -ForegroundColor Yellow

$dirs = @(
    "writable",
    "writable/cache",
    "writable/logs",
    "writable/session",
    "writable/uploads",
    "writable/debugbar",
    "public/uploads"
)

foreach ($dir in $dirs) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
    # Crear index.html de seguridad
    "<html><body><h1>403 Forbidden</h1></body></html>" | Out-File -FilePath "$dir/index.html" -Encoding UTF8 -Force -ErrorAction SilentlyContinue
}
Write-Success "Carpetas creadas correctamente"

# ============================================================================
# PASO 6: Verificación final
# ============================================================================
Write-Host ""
Write-Host "--- PASO 6/6: Verificacion final ---" -ForegroundColor Yellow

$allOk = $true
$checks = @(
    @{File="vendor/autoload.php"; Desc="Autoloader de Composer"},
    @{File="vendor/codeigniter4/framework/system/Boot.php"; Desc="Framework CodeIgniter"},
    @{File="app/Config/App.php"; Desc="Configuracion de App"},
    @{File="public/index.php"; Desc="Punto de entrada"},
    @{File=".env"; Desc="Archivo de entorno"}
)

foreach ($check in $checks) {
    if (Test-Path $check.File) {
        Write-Success $check.Desc
    } else {
        Write-Fail "$($check.Desc) - FALTA: $($check.File)"
        $allOk = $false
    }
}

# Test de carga de PHP
Write-Info "Probando carga de la aplicacion..."
$testResult = php -r "require 'vendor/autoload.php'; echo 'OK';" 2>&1
if ($testResult -eq "OK") {
    Write-Success "Autoloader funciona correctamente"
} else {
    Write-Fail "Error al cargar autoloader"
    $allOk = $false
}

# ============================================================================
# RESUMEN FINAL
# ============================================================================
Write-Host ""
Write-Host "============================================================" -ForegroundColor Magenta
if ($allOk) {
    Write-Host "         INSTALACION COMPLETADA EXITOSAMENTE" -ForegroundColor Green
} else {
    Write-Host "         INSTALACION COMPLETADA CON ADVERTENCIAS" -ForegroundColor Yellow
}
Write-Host "============================================================" -ForegroundColor Magenta
Write-Host ""

Write-Host "CONFIGURACION DE BASE DE DATOS:" -ForegroundColor Yellow
Write-Host "  1. Crear base de datos '$DbName' en MySQL/phpMyAdmin" -ForegroundColor White
Write-Host "  2. Importar: database/schema.sql" -ForegroundColor White
Write-Host "  3. Importar: database/initial_data.sql" -ForegroundColor White
Write-Host ""

Write-Host "CREDENCIALES DE ACCESO:" -ForegroundColor Yellow
Write-Host "  Email:    admin@dentalmx.com" -ForegroundColor White
Write-Host "  Password: Admin123!" -ForegroundColor White
Write-Host ""

Write-Host "INICIAR SERVIDOR DE PRUEBAS:" -ForegroundColor Yellow
Write-Host "  cd public" -ForegroundColor Cyan
Write-Host "  php -S localhost:8080" -ForegroundColor Cyan
Write-Host ""

Write-Host "Luego abra en su navegador: http://localhost:8080" -ForegroundColor Green
Write-Host ""
