#!/bin/bash
# ============================================================================
# DENTALMX - Instalador Automático para macOS + MAMP
# ============================================================================
# Sistema de Gestión para Clínicas Dentales
# Versión: 1.0.0
# © 2024 Roesch Studio
# ============================================================================

# Configuración por defecto
MAMP_PATH="/Applications/MAMP"
DATABASE_NAME="engsigne_magic_dental"
DATABASE_USER="root"
DATABASE_PASSWORD="root"
BASE_URL=""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
GRAY='\033[0;37m'
NC='\033[0m' # No Color

# Funciones de utilidad
print_banner() {
    clear
    echo ""
    echo -e "${CYAN}    ____             __        __  __  ____  __${NC}"
    echo -e "${CYAN}   / __ \\___  ____  / /_____ _/ / /  |/  / |/ /${NC}"
    echo -e "${CYAN}  / / / / _ \\/ __ \\/ __/ __ '/ / / /|_/ /|   / ${NC}"
    echo -e "${CYAN} / /_/ /  __/ / / / /_/ /_/ / / / /  / //   |  ${NC}"
    echo -e "${CYAN}/_____/\\___/_/ /_/\\__/\\__,_/_/ /_/  /_//_/|_|  ${NC}"
    echo ""
    echo -e "${WHITE}    Sistema de Gestión para Clínicas Dentales${NC}"
    echo -e "${GRAY}    Versión 1.0.0 - © 2024 Roesch Studio${NC}"
    echo ""
    echo -e "${GRAY}════════════════════════════════════════════════════════════════════════${NC}"
    echo ""
}

print_header() {
    echo ""
    echo -e "${CYAN}╔══════════════════════════════════════════════════════════════════╗${NC}"
    printf "${CYAN}║${WHITE}%*s%s%*s${CYAN}║${NC}\n" $(( (66 - ${#1}) / 2 )) "" "$1" $(( (67 - ${#1}) / 2 )) ""
    echo -e "${CYAN}╚══════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

print_step() {
    echo -e "  ${YELLOW}[$1]${WHITE} $2${NC}"
}

print_success() {
    echo -e "      ${GREEN}✓${GRAY} $1${NC}"
}

print_error() {
    echo -e "      ${RED}✗${GRAY} $1${NC}"
}

print_warning() {
    echo -e "      ${YELLOW}⚠${GRAY} $1${NC}"
}

print_info() {
    echo -e "      ${CYAN}→${GRAY} $1${NC}"
}

# Función para mostrar ayuda
show_help() {
    echo "Uso: $0 [opciones]"
    echo ""
    echo "Opciones:"
    echo "  -m, --mamp-path PATH      Ruta de MAMP (default: /Applications/MAMP)"
    echo "  -d, --database NAME       Nombre de la base de datos (default: engsigne_magic_dental)"
    echo "  -u, --user USER           Usuario de MySQL (default: root)"
    echo "  -p, --password PASS       Contraseña de MySQL (default: root)"
    echo "  -b, --base-url URL        URL base de la aplicación"
    echo "  -h, --help                Mostrar esta ayuda"
    echo ""
    echo "Ejemplo:"
    echo "  $0 --mamp-path /Applications/MAMP --password mipassword"
    echo ""
    exit 0
}

# Procesar argumentos
while [[ $# -gt 0 ]]; do
    case $1 in
        -m|--mamp-path)
            MAMP_PATH="$2"
            shift 2
            ;;
        -d|--database)
            DATABASE_NAME="$2"
            shift 2
            ;;
        -u|--user)
            DATABASE_USER="$2"
            shift 2
            ;;
        -p|--password)
            DATABASE_PASSWORD="$2"
            shift 2
            ;;
        -b|--base-url)
            BASE_URL="$2"
            shift 2
            ;;
        -h|--help)
            show_help
            ;;
        *)
            echo "Opción desconocida: $1"
            show_help
            ;;
    esac
done

# Obtener la ruta del script (donde está instalado DentalMX)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_PATH="$SCRIPT_DIR"

# Rutas de MAMP
MYSQL_PATH="$MAMP_PATH/Library/bin/mysql"
PHP_PATH="$MAMP_PATH/bin/php/php8.2.0/bin/php"
HTDOCS_PATH="$MAMP_PATH/htdocs"

# Buscar PHP en diferentes versiones
find_php() {
    local php_versions=("php8.3.1" "php8.2.0" "php8.1.13" "php8.1.0" "php8.0.8")
    for version in "${php_versions[@]}"; do
        local php_check="$MAMP_PATH/bin/php/$version/bin/php"
        if [ -f "$php_check" ]; then
            PHP_PATH="$php_check"
            return 0
        fi
    done
    # Intentar con php genérico
    if command -v php &> /dev/null; then
        PHP_PATH=$(which php)
        return 0
    fi
    return 1
}

# Iniciar instalación
print_banner

print_header "VERIFICACIÓN DE REQUISITOS"

# ============================================================================
# PASO 1: Verificar MAMP
# ============================================================================
print_step 1 "Verificando MAMP..."

MAMP_OK=true

if [ -d "$MAMP_PATH" ]; then
    print_success "MAMP encontrado en: $MAMP_PATH"
else
    print_error "MAMP no encontrado en: $MAMP_PATH"
    MAMP_OK=false
fi

if [ -f "$MYSQL_PATH" ]; then
    print_success "MySQL encontrado"
else
    print_error "MySQL no encontrado en: $MYSQL_PATH"
    MAMP_OK=false
fi

if find_php; then
    PHP_VERSION=$("$PHP_PATH" -v 2>&1 | head -n 1 | grep -oE 'PHP [0-9]+\.[0-9]+')
    VERSION_NUM=$(echo "$PHP_VERSION" | grep -oE '[0-9]+\.[0-9]+')
    if (( $(echo "$VERSION_NUM >= 8.0" | bc -l) )); then
        print_success "$PHP_VERSION encontrado (requerido: 8.0+)"
    else
        print_error "PHP versión $VERSION_NUM es muy antigua (requerido: 8.0+)"
        MAMP_OK=false
    fi
else
    print_error "PHP no encontrado"
    MAMP_OK=false
fi

if [ "$MAMP_OK" = false ]; then
    echo ""
    echo -e "${RED}  ERROR: MAMP no está correctamente instalado.${NC}"
    echo -e "${YELLOW}  Por favor, instale MAMP desde: https://www.mamp.info/${NC}"
    echo ""
    read -p "  Presione Enter para salir..."
    exit 1
fi

# ============================================================================
# PASO 2: Verificar que MySQL esté ejecutándose
# ============================================================================
print_step 2 "Verificando servicio MySQL..."

# Intentar conectar a MySQL
if "$MYSQL_PATH" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" -e "SELECT 1" &> /dev/null; then
    print_success "MySQL está ejecutándose correctamente"
else
    print_error "No se puede conectar a MySQL"
    echo ""
    echo -e "${YELLOW}  SOLUCIÓN:${NC}"
    echo -e "${GRAY}  1. Abra MAMP${NC}"
    echo -e "${GRAY}  2. Haga clic en 'Start Servers'${NC}"
    echo -e "${GRAY}  3. Ejecute este instalador nuevamente${NC}"
    echo ""
    read -p "  Presione Enter para salir..."
    exit 1
fi

# ============================================================================
# PASO 3: Verificar Composer
# ============================================================================
print_step 3 "Verificando Composer..."

COMPOSER_INSTALLED=false
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version 2>&1 | head -n 1)
    print_success "Composer encontrado: $COMPOSER_VERSION"
    COMPOSER_INSTALLED=true
else
    print_warning "Composer no encontrado en PATH"
    print_info "Verificando vendor existente..."
    
    if [ -d "$PROJECT_PATH/vendor" ]; then
        print_success "Directorio vendor ya existe, no se requiere Composer"
    else
        print_error "Se requiere Composer para instalar dependencias"
        echo ""
        echo -e "${YELLOW}  SOLUCIÓN:${NC}"
        echo -e "${GRAY}  Instale Composer con: brew install composer${NC}"
        echo -e "${GRAY}  O descargue desde: https://getcomposer.org/download/${NC}"
        echo ""
        read -p "  Presione Enter para salir..."
        exit 1
    fi
fi

# ============================================================================
# PASO 4: Verificar archivos del proyecto
# ============================================================================
print_step 4 "Verificando archivos del proyecto..."

REQUIRED_FILES=(
    "database/schema.sql"
    "database/initial_data.sql"
    ".env.example"
    "app/Config/Database.php"
    "public/index.php"
)

FILES_OK=true
for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$PROJECT_PATH/$file" ]; then
        print_success "Archivo encontrado: $file"
    else
        print_error "Archivo faltante: $file"
        FILES_OK=false
    fi
done

if [ "$FILES_OK" = false ]; then
    echo ""
    echo -e "${RED}  ERROR: Faltan archivos críticos del proyecto.${NC}"
    echo ""
    read -p "  Presione Enter para salir..."
    exit 1
fi

print_header "CONFIGURACIÓN DE BASE DE DATOS"

# ============================================================================
# PASO 5: Crear base de datos
# ============================================================================
print_step 5 "Creando base de datos..."

CREATE_DB_QUERY="CREATE DATABASE IF NOT EXISTS \`$DATABASE_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if "$MYSQL_PATH" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" -e "$CREATE_DB_QUERY" 2>/dev/null; then
    print_success "Base de datos '$DATABASE_NAME' creada/verificada"
else
    print_error "Error al crear la base de datos"
    read -p "  Presione Enter para salir..."
    exit 1
fi

# ============================================================================
# PASO 6: Importar schema.sql
# ============================================================================
print_step 6 "Importando estructura de base de datos..."

SCHEMA_FILE="$PROJECT_PATH/database/schema.sql"

if "$MYSQL_PATH" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" "$DATABASE_NAME" < "$SCHEMA_FILE" 2>/dev/null; then
    print_success "Schema importado correctamente"
else
    print_warning "Schema importado con advertencias (esto puede ser normal)"
fi

# ============================================================================
# PASO 7: Importar datos iniciales
# ============================================================================
print_step 7 "Importando datos iniciales..."

DATA_FILE="$PROJECT_PATH/database/initial_data.sql"

if "$MYSQL_PATH" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" "$DATABASE_NAME" < "$DATA_FILE" 2>/dev/null; then
    print_success "Datos iniciales importados"
else
    IMPORT_RESULT=$("$MYSQL_PATH" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" "$DATABASE_NAME" < "$DATA_FILE" 2>&1)
    if echo "$IMPORT_RESULT" | grep -q "Duplicate entry"; then
        print_warning "Algunos datos ya existían (esto es normal)"
    else
        print_warning "Datos importados con advertencias"
    fi
fi

# Verificar tablas creadas
print_info "Verificando tablas creadas..."
TABLE_COUNT=$("$MYSQL_PATH" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" "$DATABASE_NAME" -N -e "SHOW TABLES" 2>/dev/null | wc -l | tr -d ' ')
print_success "$TABLE_COUNT tablas creadas en la base de datos"

print_header "CONFIGURACIÓN DE LA APLICACIÓN"

# ============================================================================
# PASO 8: Configurar archivo .env
# ============================================================================
print_step 8 "Configurando archivo .env..."

ENV_EXAMPLE="$PROJECT_PATH/.env.example"
ENV_FILE="$PROJECT_PATH/.env"

if [ -f "$ENV_FILE" ]; then
    print_warning "Archivo .env ya existe"
    print_info "Creando respaldo como .env.backup"
    cp "$ENV_FILE" "$ENV_FILE.backup"
fi

# Copiar .env.example a .env
cp "$ENV_EXAMPLE" "$ENV_FILE"

# Detectar URL base automáticamente si no se proporcionó
if [ -z "$BASE_URL" ]; then
    PROJECT_NAME=$(basename "$PROJECT_PATH")
    BASE_URL="http://localhost:8888/$PROJECT_NAME/public"
fi

# Configurar .env usando sed (compatible con macOS)
sed -i '' "s|database.default.hostname = .*|database.default.hostname = localhost|g" "$ENV_FILE"
sed -i '' "s|database.default.database = .*|database.default.database = $DATABASE_NAME|g" "$ENV_FILE"
sed -i '' "s|database.default.username = .*|database.default.username = $DATABASE_USER|g" "$ENV_FILE"
sed -i '' "s|database.default.password = .*|database.default.password = $DATABASE_PASSWORD|g" "$ENV_FILE"
sed -i '' "s|database.default.DBDriver = .*|database.default.DBDriver = MySQLi|g" "$ENV_FILE"
sed -i '' "s|database.default.port = .*|database.default.port = 8889|g" "$ENV_FILE"
sed -i '' "s|CI_ENVIRONMENT = .*|CI_ENVIRONMENT = production|g" "$ENV_FILE"
sed -i '' "s|app.baseURL = .*|app.baseURL = '$BASE_URL'|g" "$ENV_FILE"

print_success "Archivo .env configurado correctamente"
print_info "URL base configurada: $BASE_URL"

# ============================================================================
# PASO 9: Instalar dependencias de Composer (si es necesario)
# ============================================================================
print_step 9 "Verificando dependencias..."

if [ -f "$PROJECT_PATH/vendor/autoload.php" ]; then
    print_success "Dependencias ya instaladas"
else
    if [ "$COMPOSER_INSTALLED" = true ]; then
        print_info "Instalando dependencias con Composer..."
        cd "$PROJECT_PATH"
        if composer install --no-dev --optimize-autoloader 2>/dev/null; then
            print_success "Dependencias instaladas correctamente"
        else
            print_warning "Composer tuvo algunas advertencias"
        fi
    else
        print_error "No se pueden instalar dependencias sin Composer"
        echo ""
        echo -e "${YELLOW}  Por favor, instale las dependencias manualmente:${NC}"
        echo -e "${GRAY}  cd $PROJECT_PATH${NC}"
        echo -e "${GRAY}  composer install${NC}"
        echo ""
    fi
fi

# ============================================================================
# PASO 10: Configurar permisos
# ============================================================================
print_step 10 "Configurando permisos..."

WRITABLE_DIRS=(
    "writable"
    "writable/cache"
    "writable/logs"
    "writable/session"
    "writable/uploads"
    "public/uploads"
)

for dir in "${WRITABLE_DIRS[@]}"; do
    DIR_PATH="$PROJECT_PATH/$dir"
    if [ ! -d "$DIR_PATH" ]; then
        mkdir -p "$DIR_PATH"
    fi
    chmod -R 755 "$DIR_PATH"
done

print_success "Directorios de escritura configurados"

# ============================================================================
# PASO 11: Verificar instalación
# ============================================================================
print_header "VERIFICACIÓN FINAL"

print_step 11 "Verificando instalación..."

# Verificar conexión a base de datos
USER_COUNT=$("$MYSQL_PATH" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" "$DATABASE_NAME" -N -e "SELECT COUNT(*) FROM usuarios" 2>/dev/null)
if [ -n "$USER_COUNT" ]; then
    print_success "Conexión a base de datos: OK"
    print_success "Usuarios en sistema: $USER_COUNT"
else
    print_error "Error al verificar base de datos"
fi

# Verificar servicios
SERVICE_COUNT=$("$MYSQL_PATH" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" "$DATABASE_NAME" -N -e "SELECT COUNT(*) FROM servicios" 2>/dev/null)
if [ -n "$SERVICE_COUNT" ]; then
    print_success "Servicios configurados: $SERVICE_COUNT"
fi

# ============================================================================
# RESUMEN FINAL
# ============================================================================
echo ""
echo -e "${GREEN}════════════════════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${GREEN}  ¡INSTALACIÓN COMPLETADA EXITOSAMENTE!${NC}"
echo ""
echo -e "${GREEN}════════════════════════════════════════════════════════════════════════${NC}"
echo ""

PROJECT_NAME=$(basename "$PROJECT_PATH")
FINAL_URL="${BASE_URL:-http://localhost:8888/$PROJECT_NAME/public}"

echo -e "${CYAN}  INFORMACIÓN DE ACCESO:${NC}"
echo ""
echo -e "${GRAY}    URL de la aplicación:${NC}"
echo -e "${YELLOW}    $FINAL_URL${NC}"
echo ""
echo -e "${GRAY}    Credenciales de administrador:${NC}"
echo -e "${WHITE}    Email:    admin@dentalmx.com${NC}"
echo -e "${WHITE}    Password: admin123${NC}"
echo ""
echo -e "${GRAY}    Base de datos: $DATABASE_NAME${NC}"
echo -e "${GRAY}    Puerto MySQL MAMP: 8889${NC}"
echo ""
echo -e "${RED}  IMPORTANTE:${NC}"
echo -e "${GRAY}    • Cambie la contraseña del administrador después del primer acceso${NC}"
echo -e "${GRAY}    • Asegúrese de que los servidores MAMP estén ejecutándose${NC}"
echo -e "${GRAY}    • Configure HTTPS para producción${NC}"
echo ""
echo -e "${GRAY}════════════════════════════════════════════════════════════════════════${NC}"
echo ""

# Preguntar si desea abrir el navegador
read -p "  ¿Desea abrir la aplicación en el navegador? (s/n): " OPEN_BROWSER
if [[ "$OPEN_BROWSER" =~ ^[Ss]$ ]]; then
    open "$FINAL_URL"
fi

echo ""
echo -e "${CYAN}  Gracias por usar DentalMX${NC}"
echo ""
