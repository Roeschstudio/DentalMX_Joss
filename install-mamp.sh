#!/bin/bash
# ============================================================================
# DENTALMX - Instalador Completo para macOS + MAMP
# ============================================================================
# Este script configura COMPLETAMENTE DentalMX para MAMP en macOS
# Resuelve problemas de:
#   - Puerto MySQL (8889 en MAMP)
#   - URLs con index.php (cuando mod_rewrite no está disponible)
#   - Configuración de .htaccess
#   - Permisos de archivos
# 
# Versión: 2.0.0
# © 2024 Roesch Studio
# ============================================================================

set -e

# Colores para terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
GRAY='\033[0;37m'
NC='\033[0m'

# Banner
clear
echo ""
echo -e "${CYAN}╔══════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║${NC}                                                                  ${CYAN}║${NC}"
echo -e "${CYAN}║${WHITE}           🦷 DentalMX - Instalador para MAMP v2.0              ${CYAN}║${NC}"
echo -e "${CYAN}║${NC}                                                                  ${CYAN}║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Detectar directorio del proyecto
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$SCRIPT_DIR"

echo -e "${CYAN}→${NC} Directorio del proyecto: ${WHITE}$PROJECT_DIR${NC}"
echo ""

# ============================================================================
# PASO 1: Detectar configuración de MAMP
# ============================================================================
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${WHITE}[1/6] Detectando configuración de MAMP...${NC}"
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Verificar MAMP
if [ ! -d "/Applications/MAMP" ]; then
    echo -e "${RED}✗${NC} MAMP no encontrado en /Applications/MAMP"
    echo -e "${YELLOW}  Por favor instale MAMP desde: https://www.mamp.info/en/downloads/${NC}"
    exit 1
fi

echo -e "${GREEN}✓${NC} MAMP encontrado"

# Detectar puertos de MAMP
MAMP_PREFS="/Library/Preferences/de.appsolute.MAMP.plist"
APACHE_PORT=8888
MYSQL_PORT=8889

# Intentar leer de preferencias de MAMP
if [ -f "$MAMP_PREFS" ]; then
    # Leer puerto Apache
    DETECTED_APACHE=$(defaults read /Library/Preferences/de.appsolute.MAMP ApachePort 2>/dev/null || echo "8888")
    DETECTED_MYSQL=$(defaults read /Library/Preferences/de.appsolute.MAMP MysqlPort 2>/dev/null || echo "8889")
    
    if [ -n "$DETECTED_APACHE" ]; then
        APACHE_PORT=$DETECTED_APACHE
    fi
    if [ -n "$DETECTED_MYSQL" ]; then
        MYSQL_PORT=$DETECTED_MYSQL
    fi
fi

# Preguntar al usuario para confirmar
echo ""
echo -e "${CYAN}Configuración detectada:${NC}"
echo -e "  Puerto Apache: ${WHITE}$APACHE_PORT${NC}"
echo -e "  Puerto MySQL:  ${WHITE}$MYSQL_PORT${NC}"
echo ""

read -p "¿Son correctos estos puertos? (s/n): " CONFIRM_PORTS
if [[ ! "$CONFIRM_PORTS" =~ ^[Ss]$ ]]; then
    read -p "Ingrese puerto Apache (ej: 8888): " APACHE_PORT
    read -p "Ingrese puerto MySQL (ej: 8889 o 3306): " MYSQL_PORT
fi

echo ""
echo -e "${GREEN}✓${NC} Puertos configurados: Apache=$APACHE_PORT, MySQL=$MYSQL_PORT"
echo ""

# ============================================================================
# PASO 2: Verificar MySQL está corriendo
# ============================================================================
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${WHITE}[2/6] Verificando servicios MAMP...${NC}"
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Verificar MySQL
MYSQL_BIN="/Applications/MAMP/Library/bin/mysql"

if [ ! -f "$MYSQL_BIN" ]; then
    echo -e "${RED}✗${NC} MySQL client no encontrado"
    exit 1
fi

# Probar conexión MySQL (intentar varios métodos)
MYSQL_CONNECTED=false
DB_HOST="127.0.0.1"
DB_USER="root"
DB_PASS=""

# Intentar sin contraseña
if "$MYSQL_BIN" -u root -h 127.0.0.1 -P $MYSQL_PORT -e "SELECT 1" 2>/dev/null | grep -q "1"; then
    MYSQL_CONNECTED=true
    DB_PASS=""
    echo -e "${GREEN}✓${NC} MySQL conectado (sin contraseña)"
# Intentar con contraseña 'root'
elif "$MYSQL_BIN" -u root -p"root" -h 127.0.0.1 -P $MYSQL_PORT -e "SELECT 1" 2>/dev/null | grep -q "1"; then
    MYSQL_CONNECTED=true
    DB_PASS="root"
    echo -e "${GREEN}✓${NC} MySQL conectado (contraseña: root)"
else
    echo -e "${RED}✗${NC} No se puede conectar a MySQL en puerto $MYSQL_PORT"
    echo ""
    echo -e "${YELLOW}  SOLUCIÓN:${NC}"
    echo -e "  1. Abre MAMP"
    echo -e "  2. Haz clic en 'Start Servers'"
    echo -e "  3. Espera a que MySQL esté en verde"
    echo -e "  4. Ejecuta este script de nuevo"
    echo ""
    
    read -p "¿Desea continuar de todos modos? (s/n): " CONTINUE_ANYWAY
    if [[ ! "$CONTINUE_ANYWAY" =~ ^[Ss]$ ]]; then
        exit 1
    fi
    MYSQL_CONNECTED=false
fi

# ============================================================================
# PASO 3: Crear/Importar base de datos
# ============================================================================
echo ""
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${WHITE}[3/6] Configurando base de datos...${NC}"
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

DB_NAME="engsigne_magic_dental"

if [ "$MYSQL_CONNECTED" = true ]; then
    # Verificar si la base de datos existe
    DB_EXISTS=$("$MYSQL_BIN" -u root -p"$DB_PASS" -h 127.0.0.1 -P $MYSQL_PORT -N -e "SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$DB_NAME'" 2>/dev/null || echo "0")
    
    if [ "$DB_EXISTS" -gt 0 ]; then
        echo -e "${GREEN}✓${NC} Base de datos '$DB_NAME' ya existe"
        
        # Verificar tablas
        TABLE_COUNT=$("$MYSQL_BIN" -u root -p"$DB_PASS" -h 127.0.0.1 -P $MYSQL_PORT $DB_NAME -N -e "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$DB_NAME'" 2>/dev/null || echo "0")
        echo -e "${CYAN}→${NC} Tablas encontradas: $TABLE_COUNT"
        
        if [ "$TABLE_COUNT" -lt 10 ]; then
            echo -e "${YELLOW}⚠${NC} Pocas tablas detectadas. ¿Desea reimportar la base de datos?"
            read -p "  (Esto borrará los datos actuales) (s/n): " REIMPORT
            
            if [[ "$REIMPORT" =~ ^[Ss]$ ]]; then
                echo -e "${CYAN}→${NC} Importando esquema..."
                "$MYSQL_BIN" -u root -p"$DB_PASS" -h 127.0.0.1 -P $MYSQL_PORT $DB_NAME < "$PROJECT_DIR/database/schema.sql" 2>/dev/null
                echo -e "${GREEN}✓${NC} Esquema importado"
                
                echo -e "${CYAN}→${NC} Importando datos iniciales..."
                "$MYSQL_BIN" -u root -p"$DB_PASS" -h 127.0.0.1 -P $MYSQL_PORT $DB_NAME < "$PROJECT_DIR/database/initial_data.sql" 2>/dev/null
                echo -e "${GREEN}✓${NC} Datos iniciales importados"
            fi
        fi
    else
        echo -e "${CYAN}→${NC} Creando base de datos '$DB_NAME'..."
        "$MYSQL_BIN" -u root -p"$DB_PASS" -h 127.0.0.1 -P $MYSQL_PORT -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" 2>/dev/null
        echo -e "${GREEN}✓${NC} Base de datos creada"
        
        if [ -f "$PROJECT_DIR/database/schema.sql" ]; then
            echo -e "${CYAN}→${NC} Importando esquema..."
            "$MYSQL_BIN" -u root -p"$DB_PASS" -h 127.0.0.1 -P $MYSQL_PORT $DB_NAME < "$PROJECT_DIR/database/schema.sql" 2>/dev/null
            echo -e "${GREEN}✓${NC} Esquema importado"
        fi
        
        if [ -f "$PROJECT_DIR/database/initial_data.sql" ]; then
            echo -e "${CYAN}→${NC} Importando datos iniciales..."
            "$MYSQL_BIN" -u root -p"$DB_PASS" -h 127.0.0.1 -P $MYSQL_PORT $DB_NAME < "$PROJECT_DIR/database/initial_data.sql" 2>/dev/null
            echo -e "${GREEN}✓${NC} Datos iniciales importados"
        fi
    fi
else
    echo -e "${YELLOW}⚠${NC} Saltando configuración de base de datos (MySQL no conectado)"
fi

# ============================================================================
# PASO 4: Crear archivo .env para MAMP
# ============================================================================
echo ""
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${WHITE}[4/6] Configurando archivo .env...${NC}"
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Detectar nombre de la carpeta del proyecto
PROJECT_FOLDER=$(basename "$PROJECT_DIR")
BASE_URL="http://localhost:$APACHE_PORT/$PROJECT_FOLDER/public/"

# Backup si existe
if [ -f "$PROJECT_DIR/.env" ]; then
    cp "$PROJECT_DIR/.env" "$PROJECT_DIR/.env.backup.$(date +%Y%m%d_%H%M%S)"
    echo -e "${CYAN}→${NC} Backup de .env creado"
fi

# Crear nuevo .env optimizado para MAMP
cat > "$PROJECT_DIR/.env" << ENVFILE
#--------------------------------------------------------------------
# DENTALMX - Configuración para MAMP en macOS
# Generado: $(date '+%Y-%m-%d %H:%M:%S')
#--------------------------------------------------------------------
# IMPORTANTE: Este archivo está configurado para MAMP
# Apache Port: $APACHE_PORT
# MySQL Port: $MYSQL_PORT
#--------------------------------------------------------------------

CI_ENVIRONMENT = development

#--------------------------------------------------------------------
# APP - Configuración de la aplicación
#--------------------------------------------------------------------
# NOTA: indexPage = 'index.php' es NECESARIO en MAMP
# porque mod_rewrite generalmente no está habilitado por defecto
app.baseURL = '$BASE_URL'
app.indexPage = 'index.php'
app.defaultTimezone = 'America/Mexico_City'
app.defaultLocale = 'es'

#--------------------------------------------------------------------
# DATABASE - Conexión MySQL (MAMP)
#--------------------------------------------------------------------
# MAMP usa puerto $MYSQL_PORT por defecto (no 3306)
database.default.hostname = 127.0.0.1
database.default.database = $DB_NAME
database.default.username = root
database.default.password = $DB_PASS
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = $MYSQL_PORT
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------
app.sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler'
app.sessionCookieName = 'dentalmx_session'
app.sessionExpiration = 7200

#--------------------------------------------------------------------
# LOGGING
#--------------------------------------------------------------------
logger.threshold = 4
ENVFILE

echo -e "${GREEN}✓${NC} Archivo .env creado con configuración MAMP"
echo -e "  ${CYAN}→${NC} Base URL: $BASE_URL"
echo -e "  ${CYAN}→${NC} Index Page: index.php (necesario para MAMP)"
echo -e "  ${CYAN}→${NC} MySQL Port: $MYSQL_PORT"

# ============================================================================
# PASO 5: Configurar .htaccess
# ============================================================================
echo ""
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${WHITE}[5/6] Configurando .htaccess...${NC}"
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Crear .htaccess en public/
cat > "$PROJECT_DIR/public/.htaccess" << 'HTACCESS'
# Disable directory browsing
Options All -Indexes

# Turn on mod_rewrite
RewriteEngine On

# If your public folder is in a subfolder, set the RewriteBase accordingly
# RewriteBase /DentalMX_Joss/public

# Redirect trailing slashes
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} (.+)/$
RewriteRule ^ %1 [L,R=301]

# Rewrite everything else to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L,QSA]

# Handle Authorization Header
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
HTACCESS

echo -e "${GREEN}✓${NC} .htaccess configurado en public/"

# Crear .htaccess en la raíz para redirigir a public
cat > "$PROJECT_DIR/.htaccess" << 'HTACCESS_ROOT'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
HTACCESS_ROOT

echo -e "${GREEN}✓${NC} .htaccess raíz configurado"

# ============================================================================
# PASO 6: Configurar permisos
# ============================================================================
echo ""
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${WHITE}[6/6] Configurando permisos...${NC}"
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Permisos de escritura
chmod -R 777 "$PROJECT_DIR/writable" 2>/dev/null || true
chmod -R 777 "$PROJECT_DIR/public/uploads" 2>/dev/null || true

# Limpiar caché
rm -rf "$PROJECT_DIR/writable/cache/"* 2>/dev/null || true
rm -rf "$PROJECT_DIR/writable/session/"* 2>/dev/null || true

echo -e "${GREEN}✓${NC} Permisos configurados"
echo -e "${GREEN}✓${NC} Caché limpiado"

# ============================================================================
# RESUMEN FINAL
# ============================================================================
echo ""
echo -e "${CYAN}╔══════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║${NC}                                                                  ${CYAN}║${NC}"
echo -e "${CYAN}║${GREEN}              ✓ INSTALACIÓN COMPLETADA CON ÉXITO                ${CYAN}║${NC}"
echo -e "${CYAN}║${NC}                                                                  ${CYAN}║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${WHITE}Configuración aplicada:${NC}"
echo ""
echo -e "  ${CYAN}URL de la aplicación:${NC}"
echo -e "  ${WHITE}$BASE_URL${NC}"
echo ""
echo -e "  ${CYAN}URL de login:${NC}"
echo -e "  ${WHITE}${BASE_URL}index.php/login${NC}"
echo ""
echo -e "  ${CYAN}Credenciales de acceso:${NC}"
echo -e "  ${WHITE}Email:    admin@dentalmx.com${NC}"
echo -e "  ${WHITE}Password: admin123${NC}"
echo ""
echo -e "  ${CYAN}Base de datos:${NC}"
echo -e "  ${WHITE}Nombre:   $DB_NAME${NC}"
echo -e "  ${WHITE}Puerto:   $MYSQL_PORT${NC}"
echo -e "  ${WHITE}Usuario:  root${NC}"
echo ""
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${WHITE}IMPORTANTE:${NC}"
echo ""
echo -e "  Con la configuración ${WHITE}app.indexPage = 'index.php'${NC}, las URLs"
echo -e "  funcionarán así:"
echo ""
echo -e "    ${GREEN}✓${NC} ${BASE_URL}index.php/login"
echo -e "    ${GREEN}✓${NC} ${BASE_URL}index.php/dashboard"
echo -e "    ${GREEN}✓${NC} ${BASE_URL}index.php/pacientes"
echo ""
echo -e "  CodeIgniter generará automáticamente los links correctos"
echo -e "  usando ${WHITE}site_url()${NC} y ${WHITE}base_url()${NC}."
echo ""
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Abrir en navegador
read -p "¿Desea abrir la aplicación en el navegador? (s/n): " OPEN_BROWSER
if [[ "$OPEN_BROWSER" =~ ^[Ss]$ ]]; then
    open "$BASE_URL"
fi

echo ""
echo -e "${GREEN}¡Instalación completada!${NC}"
echo ""
