#Requires -Version 5.1
<#
.SYNOPSIS
    Instalador automático para DentalMX - Sistema de Gestión Dental
    
.DESCRIPTION
    Este script automatiza la instalación completa de DentalMX:
    - Verifica requisitos (XAMPP, PHP, MySQL, Composer)
    - Crea la base de datos
    - Importa el schema y datos iniciales
    - Configura el archivo .env
    - Instala dependencias de Composer
    - Configura el servidor web

.PARAMETER XamppPath
    Ruta de instalación de XAMPP. Por defecto: C:\xampp

.PARAMETER DatabaseName
    Nombre de la base de datos. Por defecto: engsigne_magic_dental

.PARAMETER DatabaseUser
    Usuario de MySQL. Por defecto: root

.PARAMETER DatabasePassword
    Contraseña de MySQL. Por defecto: (vacío)

.PARAMETER BaseUrl
    URL base de la aplicación. Por defecto: http://localhost/DentalMX_Joss/public

.EXAMPLE
    .\Install-DentalMX.ps1
    
.EXAMPLE
    .\Install-DentalMX.ps1 -XamppPath "D:\xampp" -DatabasePassword "mipassword"

.NOTES
    Autor: Roesch Studio
    Versión: 1.0.0
    Fecha: Diciembre 2024
#>

[CmdletBinding()]
param(
    [Parameter()]
    [string]$XamppPath = "C:\xampp",
    
    [Parameter()]
    [string]$DatabaseName = "engsigne_magic_dental",
    
    [Parameter()]
    [string]$DatabaseUser = "root",
    
    [Parameter()]
    [string]$DatabasePassword = "",
    
    [Parameter()]
    [string]$BaseUrl = ""
)

# Configuración de colores y estilos
$Host.UI.RawUI.WindowTitle = "DentalMX - Instalador"

function Write-Header {
    param([string]$Text)
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║" -NoNewline -ForegroundColor Cyan
    $padding = [math]::Floor((66 - $Text.Length) / 2)
    $paddedText = (" " * $padding) + $Text + (" " * (66 - $padding - $Text.Length))
    Write-Host $paddedText -NoNewline -ForegroundColor White
    Write-Host "║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
}

function Write-Step {
    param([int]$Step, [string]$Text)
    Write-Host "  [$Step]" -NoNewline -ForegroundColor Yellow
    Write-Host " $Text" -ForegroundColor White
}

function Write-Success {
    param([string]$Text)
    Write-Host "      ✓ " -NoNewline -ForegroundColor Green
    Write-Host $Text -ForegroundColor Gray
}

function Write-Error2 {
    param([string]$Text)
    Write-Host "      ✗ " -NoNewline -ForegroundColor Red
    Write-Host $Text -ForegroundColor Gray
}

function Write-Warning2 {
    param([string]$Text)
    Write-Host "      ⚠ " -NoNewline -ForegroundColor Yellow
    Write-Host $Text -ForegroundColor Gray
}

function Write-Info {
    param([string]$Text)
    Write-Host "      → " -NoNewline -ForegroundColor Cyan
    Write-Host $Text -ForegroundColor Gray
}

# Banner inicial
Clear-Host
Write-Host ""
Write-Host "    ____             __        __  __  ____  __" -ForegroundColor Cyan
Write-Host "   / __ \___  ____  / /_____ _/ / /  |/  / |/ /" -ForegroundColor Cyan
Write-Host "  / / / / _ \/ __ \/ __/ __ '/ / / /|_/ /|   / " -ForegroundColor Cyan
Write-Host " / /_/ /  __/ / / / /_/ /_/ / / / /  / //   |  " -ForegroundColor Cyan
Write-Host "/_____/\___/_/ /_/\__/\__,_/_/ /_/  /_//_/|_|  " -ForegroundColor Cyan
Write-Host ""
Write-Host "    Sistema de Gestión para Clínicas Dentales" -ForegroundColor White
Write-Host "    Versión 1.0.0 - © 2024 Roesch Studio" -ForegroundColor Gray
Write-Host ""
Write-Host "════════════════════════════════════════════════════════════════════════" -ForegroundColor DarkGray
Write-Host ""

# Obtener la ruta del script (donde está instalado DentalMX)
$ScriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$ProjectPath = $ScriptPath

Write-Header "VERIFICACIÓN DE REQUISITOS"

# ============================================================================
# PASO 1: Verificar XAMPP
# ============================================================================
Write-Step 1 "Verificando XAMPP..."

$MysqlPath = Join-Path $XamppPath "mysql\bin\mysql.exe"
$PhpPath = Join-Path $XamppPath "php\php.exe"
$HtdocsPath = Join-Path $XamppPath "htdocs"

$xamppOk = $true

if (Test-Path $XamppPath) {
    Write-Success "XAMPP encontrado en: $XamppPath"
} else {
    Write-Error2 "XAMPP no encontrado en: $XamppPath"
    $xamppOk = $false
}

if (Test-Path $MysqlPath) {
    Write-Success "MySQL encontrado"
} else {
    Write-Error2 "MySQL no encontrado en: $MysqlPath"
    $xamppOk = $false
}

if (Test-Path $PhpPath) {
    $phpVersion = & $PhpPath -v 2>&1 | Select-Object -First 1
    if ($phpVersion -match "PHP (\d+\.\d+)") {
        $version = [decimal]$Matches[1]
        if ($version -ge 8.0) {
            Write-Success "PHP $($Matches[1]) encontrado (requerido: 8.0+)"
        } else {
            Write-Error2 "PHP versión $($Matches[1]) es muy antigua (requerido: 8.0+)"
            $xamppOk = $false
        }
    }
} else {
    Write-Error2 "PHP no encontrado en: $PhpPath"
    $xamppOk = $false
}

if (-not $xamppOk) {
    Write-Host ""
    Write-Host "  ERROR: XAMPP no está correctamente instalado." -ForegroundColor Red
    Write-Host "  Por favor, instale XAMPP desde: https://www.apachefriends.org/" -ForegroundColor Yellow
    Write-Host ""
    Read-Host "  Presione Enter para salir"
    exit 1
}

# ============================================================================
# PASO 2: Verificar que MySQL esté ejecutándose
# ============================================================================
Write-Step 2 "Verificando servicio MySQL..."

$testConnection = $null
try {
    if ($DatabasePassword -eq "") {
        $testConnection = & $MysqlPath -u $DatabaseUser -e "SELECT 1" 2>&1
    } else {
        $testConnection = & $MysqlPath -u $DatabaseUser -p"$DatabasePassword" -e "SELECT 1" 2>&1
    }
    
    if ($LASTEXITCODE -eq 0) {
        Write-Success "MySQL está ejecutándose correctamente"
    } else {
        throw "No se pudo conectar"
    }
} catch {
    Write-Error2 "No se puede conectar a MySQL"
    Write-Host ""
    Write-Host "  SOLUCIÓN:" -ForegroundColor Yellow
    Write-Host "  1. Abra XAMPP Control Panel" -ForegroundColor Gray
    Write-Host "  2. Inicie el servicio MySQL haciendo clic en 'Start'" -ForegroundColor Gray
    Write-Host "  3. Ejecute este instalador nuevamente" -ForegroundColor Gray
    Write-Host ""
    Read-Host "  Presione Enter para salir"
    exit 1
}

# ============================================================================
# PASO 3: Verificar Composer
# ============================================================================
Write-Step 3 "Verificando Composer..."

$composerInstalled = $false
try {
    $composerVersion = & composer --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Composer encontrado: $($composerVersion -replace 'Composer version ', '')"
        $composerInstalled = $true
    }
} catch {
    # Composer no está en PATH
}

if (-not $composerInstalled) {
    Write-Warning2 "Composer no encontrado en PATH"
    Write-Info "Verificando vendor existente..."
    
    $vendorPath = Join-Path $ProjectPath "vendor"
    if (Test-Path $vendorPath) {
        Write-Success "Directorio vendor ya existe, no se requiere Composer"
    } else {
        Write-Error2 "Se requiere Composer para instalar dependencias"
        Write-Host ""
        Write-Host "  SOLUCIÓN:" -ForegroundColor Yellow
        Write-Host "  Instale Composer desde: https://getcomposer.org/download/" -ForegroundColor Gray
        Write-Host ""
        Read-Host "  Presione Enter para salir"
        exit 1
    }
}

# ============================================================================
# PASO 4: Verificar archivos del proyecto
# ============================================================================
Write-Step 4 "Verificando archivos del proyecto..."

$requiredFiles = @(
    "database\schema.sql",
    "database\initial_data.sql",
    ".env.example",
    "app\Config\Database.php",
    "public\index.php"
)

$filesOk = $true
foreach ($file in $requiredFiles) {
    $filePath = Join-Path $ProjectPath $file
    if (Test-Path $filePath) {
        Write-Success "Archivo encontrado: $file"
    } else {
        Write-Error2 "Archivo faltante: $file"
        $filesOk = $false
    }
}

if (-not $filesOk) {
    Write-Host ""
    Write-Host "  ERROR: Faltan archivos críticos del proyecto." -ForegroundColor Red
    Write-Host ""
    Read-Host "  Presione Enter para salir"
    exit 1
}

Write-Header "CONFIGURACIÓN DE BASE DE DATOS"

# ============================================================================
# PASO 5: Crear base de datos
# ============================================================================
Write-Step 5 "Creando base de datos..."

$createDbQuery = "CREATE DATABASE IF NOT EXISTS ``$DatabaseName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

try {
    if ($DatabasePassword -eq "") {
        $result = & $MysqlPath -u $DatabaseUser -e $createDbQuery 2>&1
    } else {
        $result = & $MysqlPath -u $DatabaseUser -p"$DatabasePassword" -e $createDbQuery 2>&1
    }
    
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Base de datos '$DatabaseName' creada/verificada"
    } else {
        throw $result
    }
} catch {
    Write-Error2 "Error al crear la base de datos: $_"
    Read-Host "  Presione Enter para salir"
    exit 1
}

# ============================================================================
# PASO 6: Importar schema.sql
# ============================================================================
Write-Step 6 "Importando estructura de base de datos..."

$schemaFile = Join-Path $ProjectPath "database\schema.sql"

try {
    if ($DatabasePassword -eq "") {
        $result = & $MysqlPath -u $DatabaseUser $DatabaseName -e "source $schemaFile" 2>&1
    } else {
        $result = & $MysqlPath -u $DatabaseUser -p"$DatabasePassword" $DatabaseName -e "source $schemaFile" 2>&1
    }
    
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Schema importado correctamente"
    } else {
        # Verificar si es solo advertencias
        if ($result -match "ERROR") {
            throw $result
        } else {
            Write-Warning2 "Schema importado con advertencias"
        }
    }
} catch {
    Write-Error2 "Error al importar schema: $_"
    Write-Info "Intentando método alternativo..."
    
    try {
        $schemaContent = Get-Content $schemaFile -Raw
        $tempFile = Join-Path $env:TEMP "dentalmx_schema_temp.sql"
        $schemaContent | Out-File $tempFile -Encoding UTF8
        
        if ($DatabasePassword -eq "") {
            Get-Content $tempFile | & $MysqlPath -u $DatabaseUser $DatabaseName 2>&1
        } else {
            Get-Content $tempFile | & $MysqlPath -u $DatabaseUser -p"$DatabasePassword" $DatabaseName 2>&1
        }
        
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Schema importado correctamente (método alternativo)"
        }
        
        Remove-Item $tempFile -Force -ErrorAction SilentlyContinue
    } catch {
        Write-Error2 "No se pudo importar el schema"
        Read-Host "  Presione Enter para salir"
        exit 1
    }
}

# ============================================================================
# PASO 7: Importar datos iniciales
# ============================================================================
Write-Step 7 "Importando datos iniciales..."

$dataFile = Join-Path $ProjectPath "database\initial_data.sql"

try {
    if ($DatabasePassword -eq "") {
        $result = & $MysqlPath -u $DatabaseUser $DatabaseName -e "source $dataFile" 2>&1
    } else {
        $result = & $MysqlPath -u $DatabaseUser -p"$DatabasePassword" $DatabaseName -e "source $dataFile" 2>&1
    }
    
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Datos iniciales importados"
    } else {
        if ($result -match "Duplicate entry") {
            Write-Warning2 "Algunos datos ya existían (esto es normal)"
        } else {
            throw $result
        }
    }
} catch {
    if ($_ -match "Duplicate entry") {
        Write-Warning2 "Algunos datos ya existían (esto es normal)"
    } else {
        Write-Error2 "Error al importar datos: $_"
    }
}

# Verificar tablas creadas
Write-Info "Verificando tablas creadas..."
try {
    if ($DatabasePassword -eq "") {
        $tables = & $MysqlPath -u $DatabaseUser $DatabaseName -N -e "SHOW TABLES" 2>&1
    } else {
        $tables = & $MysqlPath -u $DatabaseUser -p"$DatabasePassword" $DatabaseName -N -e "SHOW TABLES" 2>&1
    }
    
    $tableCount = ($tables | Measure-Object -Line).Lines
    Write-Success "$tableCount tablas creadas en la base de datos"
} catch {
    Write-Warning2 "No se pudo verificar el conteo de tablas"
}

Write-Header "CONFIGURACIÓN DE LA APLICACIÓN"

# ============================================================================
# PASO 8: Configurar archivo .env
# ============================================================================
Write-Step 8 "Configurando archivo .env..."

$envExample = Join-Path $ProjectPath ".env.example"
$envFile = Join-Path $ProjectPath ".env"

if (Test-Path $envFile) {
    Write-Warning2 "Archivo .env ya existe"
    Write-Info "Creando respaldo como .env.backup"
    Copy-Item $envFile "$envFile.backup" -Force
}

try {
    $envContent = Get-Content $envExample -Raw
    
    # Configurar base de datos
    $envContent = $envContent -replace 'database\.default\.hostname\s*=\s*.*', "database.default.hostname = localhost"
    $envContent = $envContent -replace 'database\.default\.database\s*=\s*.*', "database.default.database = $DatabaseName"
    $envContent = $envContent -replace 'database\.default\.username\s*=\s*.*', "database.default.username = $DatabaseUser"
    $envContent = $envContent -replace 'database\.default\.password\s*=\s*.*', "database.default.password = $DatabasePassword"
    $envContent = $envContent -replace 'database\.default\.DBDriver\s*=\s*.*', "database.default.DBDriver = MySQLi"
    
    # Configurar entorno
    $envContent = $envContent -replace 'CI_ENVIRONMENT\s*=\s*.*', "CI_ENVIRONMENT = production"
    
    # Configurar URL base si se proporcionó
    if ($BaseUrl -ne "") {
        $envContent = $envContent -replace 'app\.baseURL\s*=\s*.*', "app.baseURL = $BaseUrl"
    } else {
        # Detectar URL base automáticamente
        $projectName = Split-Path $ProjectPath -Leaf
        $autoBaseUrl = "http://localhost/$projectName/public"
        $envContent = $envContent -replace 'app\.baseURL\s*=\s*.*', "app.baseURL = $autoBaseUrl"
        Write-Info "URL base configurada: $autoBaseUrl"
    }
    
    $envContent | Out-File $envFile -Encoding UTF8 -NoNewline
    Write-Success "Archivo .env configurado correctamente"
    
} catch {
    Write-Error2 "Error al configurar .env: $_"
}

# ============================================================================
# PASO 9: Instalar dependencias de Composer (si es necesario)
# ============================================================================
Write-Step 9 "Verificando dependencias..."

$vendorPath = Join-Path $ProjectPath "vendor"
$autoloadPath = Join-Path $vendorPath "autoload.php"

if (Test-Path $autoloadPath) {
    Write-Success "Dependencias ya instaladas"
} else {
    if ($composerInstalled) {
        Write-Info "Instalando dependencias con Composer..."
        Push-Location $ProjectPath
        try {
            $result = & composer install --no-dev --optimize-autoloader 2>&1
            if ($LASTEXITCODE -eq 0) {
                Write-Success "Dependencias instaladas correctamente"
            } else {
                Write-Warning2 "Composer tuvo algunas advertencias"
            }
        } catch {
            Write-Error2 "Error al instalar dependencias: $_"
        }
        Pop-Location
    } else {
        Write-Error2 "No se pueden instalar dependencias sin Composer"
        Write-Host ""
        Write-Host "  Por favor, instale las dependencias manualmente:" -ForegroundColor Yellow
        Write-Host "  cd $ProjectPath" -ForegroundColor Gray
        Write-Host "  composer install" -ForegroundColor Gray
        Write-Host ""
    }
}

# ============================================================================
# PASO 10: Configurar permisos de writable
# ============================================================================
Write-Step 10 "Configurando permisos..."

$writableDirs = @(
    "writable",
    "writable\cache",
    "writable\logs",
    "writable\session",
    "writable\uploads"
)

foreach ($dir in $writableDirs) {
    $dirPath = Join-Path $ProjectPath $dir
    if (-not (Test-Path $dirPath)) {
        New-Item -ItemType Directory -Path $dirPath -Force | Out-Null
    }
}
Write-Success "Directorios de escritura configurados"

# ============================================================================
# PASO 11: Verificar instalación
# ============================================================================
Write-Header "VERIFICACIÓN FINAL"

Write-Step 11 "Verificando instalación..."

# Verificar conexión a base de datos desde PHP
$testPhp = @"
<?php
`$db = new mysqli('localhost', '$DatabaseUser', '$DatabasePassword', '$DatabaseName');
if (`$db->connect_error) {
    echo 'ERROR:' . `$db->connect_error;
    exit(1);
}
`$result = `$db->query('SELECT COUNT(*) as count FROM usuarios');
if (`$result) {
    `$row = `$result->fetch_assoc();
    echo 'OK:' . `$row['count'];
} else {
    echo 'ERROR:Tabla usuarios no encontrada';
}
`$db->close();
"@

$testFile = Join-Path $ProjectPath "test_install.php"
$testPhp | Out-File $testFile -Encoding UTF8

try {
    $result = & $PhpPath $testFile 2>&1
    if ($result -match "^OK:(\d+)") {
        Write-Success "Conexión a base de datos: OK"
        Write-Success "Usuarios en sistema: $($Matches[1])"
    } else {
        Write-Error2 "Error en prueba de base de datos: $result"
    }
} catch {
    Write-Error2 "Error al ejecutar prueba: $_"
} finally {
    Remove-Item $testFile -Force -ErrorAction SilentlyContinue
}

# Verificar servicios
try {
    if ($DatabasePassword -eq "") {
        $services = & $MysqlPath -u $DatabaseUser $DatabaseName -N -e "SELECT COUNT(*) FROM servicios" 2>&1
    } else {
        $services = & $MysqlPath -u $DatabaseUser -p"$DatabasePassword" $DatabaseName -N -e "SELECT COUNT(*) FROM servicios" 2>&1
    }
    Write-Success "Servicios configurados: $services"
} catch {
    Write-Warning2 "No se pudo verificar servicios"
}

# ============================================================================
# RESUMEN FINAL
# ============================================================================
Write-Host ""
Write-Host "════════════════════════════════════════════════════════════════════════" -ForegroundColor Green
Write-Host ""
Write-Host "  ¡INSTALACIÓN COMPLETADA EXITOSAMENTE!" -ForegroundColor Green
Write-Host ""
Write-Host "════════════════════════════════════════════════════════════════════════" -ForegroundColor Green
Write-Host ""

$projectName = Split-Path $ProjectPath -Leaf
$finalUrl = if ($BaseUrl -ne "") { $BaseUrl } else { "http://localhost/$projectName/public" }

Write-Host "  INFORMACIÓN DE ACCESO:" -ForegroundColor Cyan
Write-Host ""
Write-Host "    URL de la aplicación:" -ForegroundColor Gray
Write-Host "    $finalUrl" -ForegroundColor Yellow
Write-Host ""
Write-Host "    Credenciales de administrador:" -ForegroundColor Gray
Write-Host "    Email:    admin@dentalmx.com" -ForegroundColor White
Write-Host "    Password: password" -ForegroundColor White
Write-Host ""
Write-Host "    Base de datos: $DatabaseName" -ForegroundColor Gray
Write-Host ""
Write-Host "  IMPORTANTE:" -ForegroundColor Red
Write-Host "    • Cambie la contraseña del administrador después del primer acceso" -ForegroundColor Gray
Write-Host "    • Asegúrese de que Apache esté ejecutándose en XAMPP" -ForegroundColor Gray
Write-Host "    • Configure HTTPS para producción" -ForegroundColor Gray
Write-Host ""
Write-Host "════════════════════════════════════════════════════════════════════════" -ForegroundColor DarkGray
Write-Host ""

# Preguntar si desea abrir el navegador
$openBrowser = Read-Host "  ¿Desea abrir la aplicación en el navegador? (S/N)"
if ($openBrowser -match "^[Ss]") {
    Start-Process $finalUrl
}

Write-Host ""
Write-Host "  Gracias por usar DentalMX" -ForegroundColor Cyan
Write-Host ""
