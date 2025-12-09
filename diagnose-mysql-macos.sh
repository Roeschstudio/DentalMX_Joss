#!/bin/bash
# ============================================================================
# DENTALMX - Diagnostico y Solucion de Conexión MySQL en MAMP
# ============================================================================
# Este script diagnostica problemas de conexión a MySQL en macOS con MAMP
# Versión: 1.0.0
# © 2024 Roesch Studio
# ============================================================================

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
GRAY='\033[0;37m'
NC='\033[0m'

print_header() {
    echo ""
    echo -e "${CYAN}╔══════════════════════════════════════════════════════════════════╗${NC}"
    printf "${CYAN}║${WHITE}%*s%s%*s${CYAN}║${NC}\n" $(( (66 - ${#1}) / 2 )) "" "$1" $(( (67 - ${#1}) / 2 )) ""
    echo -e "${CYAN}╚══════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

print_success() {
    echo -e "${GREEN}✓${GRAY} $1${NC}"
}

print_error() {
    echo -e "${RED}✗${GRAY} $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠${GRAY} $1${NC}"
}

print_info() {
    echo -e "${CYAN}→${GRAY} $1${NC}"
}

# Banner
clear
echo ""
echo -e "${CYAN}DentalMX - Diagnóstico de MySQL en MAMP${NC}"
echo ""

print_header "DIAGNÓSTICO DE CONEXIÓN MySQL"

# ============================================================================
# PASO 1: Verificar que MAMP esté instalado
# ============================================================================
echo -e "${YELLOW}[1]${WHITE} Verificando MAMP...${NC}"

if [ -d "/Applications/MAMP" ]; then
    print_success "MAMP encontrado en /Applications/MAMP"
else
    print_error "MAMP no encontrado"
    read -p "  Presione Enter para salir..."
    exit 1
fi

# ============================================================================
# PASO 2: Verificar si MySQL está ejecutándose
# ============================================================================
echo -e "${YELLOW}[2]${WHITE} Verificando si MySQL está ejecutándose...${NC}"

# Buscar procesos de MySQL
MYSQL_PID=$(pgrep -f "mysql" | head -1)

if [ -n "$MYSQL_PID" ]; then
    print_success "MySQL está ejecutándose (PID: $MYSQL_PID)"
else
    print_error "MySQL NO está ejecutándose"
    print_warning "SOLUCIÓN:"
    print_info "1. Abre MAMP"
    print_info "2. Haz clic en 'Start Servers'"
    print_info "3. Espera a que MySQL se inicie"
    echo ""
    read -p "  Presione Enter para salir..."
    exit 1
fi

# ============================================================================
# PASO 3: Verificar puerto de MySQL
# ============================================================================
echo -e "${YELLOW}[3]${WHITE} Verificando puertos en los que MySQL está escuchando...${NC}"

# Verificar puerto 3306
if lsof -Pi :3306 -sTCP:LISTEN -t >/dev/null 2>&1; then
    print_success "MySQL está escuchando en puerto 3306"
    MYSQL_PORT=3306
else
    print_warning "MySQL NO está escuchando en puerto 3306"
    
    # Buscar otros puertos
    MYSQL_PORTS=$(lsof -i -P -n | grep mysql | grep LISTEN | awk '{print $9}')
    
    if [ -n "$MYSQL_PORTS" ]; then
        print_info "MySQL está escuchando en: $MYSQL_PORTS"
        MYSQL_PORT=$(echo $MYSQL_PORTS | grep -oE '[0-9]+' | tail -1)
    else
        print_error "MySQL no está escuchando en ningún puerto"
        print_warning "SOLUCIÓN:"
        print_info "1. Reinicia MAMP: Stop Servers → Start Servers"
        print_info "2. Si el problema persiste, desinstala y reinstala MAMP"
        echo ""
        read -p "  Presione Enter para salir..."
        exit 1
    fi
fi

# ============================================================================
# PASO 4: Probar conexión básica
# ============================================================================
echo -e "${YELLOW}[4]${WHITE} Probando conexión a MySQL...${NC}"

MYSQL_BIN="/Applications/MAMP/Library/bin/mysql"

if [ ! -f "$MYSQL_BIN" ]; then
    print_error "MySQL client no encontrado"
    read -p "  Presione Enter para salir..."
    exit 1
fi

# Intentar conectar sin contraseña
if "$MYSQL_BIN" -u root -h 127.0.0.1 -P $MYSQL_PORT -e "SELECT 1" 2>/dev/null | grep -q "1"; then
    print_success "Conexión a MySQL: OK (sin contraseña)"
    DB_PASS=""
elif "$MYSQL_BIN" -u root -p"root" -h 127.0.0.1 -P $MYSQL_PORT -e "SELECT 1" 2>/dev/null | grep -q "1"; then
    print_success "Conexión a MySQL: OK (contraseña: root)"
    DB_PASS="root"
else
    print_error "No se puede conectar a MySQL"
    print_warning "Verificando socket..."
    
    # Intentar con socket
    MYSQL_SOCKET=$("$MYSQL_BIN" -u root --socket=/Applications/MAMP/tmp/mysql.sock -e "SELECT 1" 2>&1)
    
    if echo "$MYSQL_SOCKET" | grep -q "1"; then
        print_success "MySQL responde por socket"
        MYSQL_SOCKET="/Applications/MAMP/tmp/mysql.sock"
    else
        print_error "No se puede conectar por socket tampoco"
        read -p "  Presione Enter para salir..."
        exit 1
    fi
fi

# ============================================================================
# PASO 5: Verificar base de datos
# ============================================================================
echo -e "${YELLOW}[5]${WHITE} Verificando base de datos engsigne_magic_dental...${NC}"

if "$MYSQL_BIN" -u root -p"$DB_PASS" -h 127.0.0.1 -P $MYSQL_PORT -e "USE engsigne_magic_dental; SHOW TABLES;" 2>/dev/null | grep -q "usuarios"; then
    print_success "Base de datos y tabla usuarios encontradas"
    
    # Contar usuarios
    USER_COUNT=$("$MYSQL_BIN" -u root -p"$DB_PASS" -h 127.0.0.1 -P $MYSQL_PORT engsigne_magic_dental -N -e "SELECT COUNT(*) FROM usuarios" 2>/dev/null)
    print_success "Usuarios en la base de datos: $USER_COUNT"
else
    print_error "No se puede acceder a la base de datos"
    echo ""
    read -p "  Presione Enter para salir..."
    exit 1
fi

# ============================================================================
# PASO 6: Detectar la configuración actual
# ============================================================================
print_header "CONFIGURACIÓN DETECTADA"

echo -e "${CYAN}  Información de MySQL MAMP:${NC}"
echo ""
echo -e "${GRAY}    Host:       127.0.0.1 (localhost)${NC}"
echo -e "${GRAY}    Puerto:     $MYSQL_PORT${NC}"
echo -e "${GRAY}    Usuario:    root${NC}"
echo -e "${GRAY}    Contraseña: ${DB_PASS:-sin contraseña}${NC}"
echo -e "${GRAY}    Base datos: engsigne_magic_dental${NC}"
echo ""

# ============================================================================
# PASO 7: Verificar .env
# ============================================================================
echo -e "${YELLOW}[6]${WHITE} Verificando archivo .env...${NC}"

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="$PROJECT_DIR/.env"

if [ ! -f "$ENV_FILE" ]; then
    print_error "Archivo .env no encontrado"
    echo ""
    read -p "  Presione Enter para salir..."
    exit 1
fi

# Leer configuración del .env
ENV_HOST=$(grep "database.default.hostname" "$ENV_FILE" | cut -d '=' -f2 | xargs)
ENV_PORT=$(grep "database.default.port" "$ENV_FILE" | cut -d '=' -f2 | xargs)
ENV_USER=$(grep "database.default.username" "$ENV_FILE" | cut -d '=' -f2 | xargs)
ENV_PASS=$(grep "database.default.password" "$ENV_FILE" | cut -d '=' -f2 | xargs)
ENV_DB=$(grep "database.default.database" "$ENV_FILE" | cut -d '=' -f2 | xargs)

print_success ".env encontrado"
echo ""
echo -e "${CYAN}  Configuración en .env:${NC}"
echo ""
echo -e "${GRAY}    Host:       $ENV_HOST${NC}"
echo -e "${GRAY}    Puerto:     $ENV_PORT${NC}"
echo -e "${GRAY}    Usuario:    $ENV_USER${NC}"
echo -e "${GRAY}    Contraseña: $ENV_PASS${NC}"
echo -e "${GRAY}    Base datos: $ENV_DB${NC}"
echo ""

# ============================================================================
# PASO 8: Comparar y corregir si es necesario
# ============================================================================
if [ "$ENV_PORT" != "$MYSQL_PORT" ]; then
    print_warning "¡Puerto mismatch! .env tiene $ENV_PORT pero MySQL usa $MYSQL_PORT"
    
    echo ""
    read -p "  ¿Desea actualizar .env con el puerto correcto ($MYSQL_PORT)? (s/n): " FIX_PORT
    
    if [[ "$FIX_PORT" =~ ^[Ss]$ ]]; then
        # Hacer backup
        cp "$ENV_FILE" "$ENV_FILE.backup"
        
        # Actualizar puerto
        if [[ "$OSTYPE" == "darwin"* ]]; then
            sed -i '' "s/database\.default\.port = .*/database.default.port = $MYSQL_PORT/" "$ENV_FILE"
        else
            sed -i "s/database\.default\.port = .*/database.default.port = $MYSQL_PORT/" "$ENV_FILE"
        fi
        
        print_success ".env actualizado con puerto $MYSQL_PORT"
        print_info "Backup guardado como .env.backup"
    fi
fi

# ============================================================================
# RESUMEN
# ============================================================================
print_header "DIAGNÓSTICO COMPLETADO"

echo -e "${GREEN}  RESULTADO: MySQL está funcionando correctamente${NC}"
echo ""
echo -e "${CYAN}  Próximos pasos:${NC}"
echo ""
echo -e "${GRAY}    1. Si cambiaste el puerto en .env, reinicia MAMP${NC}"
echo -e "${GRAY}    2. Limpia el caché de CodeIgniter (opcional):${NC}"
echo -e "${GRAY}       rm -rf writable/cache/*${NC}"
echo -e "${GRAY}    3. Recarga tu navegador${NC}"
echo ""

read -p "  Presione Enter para terminar..."
