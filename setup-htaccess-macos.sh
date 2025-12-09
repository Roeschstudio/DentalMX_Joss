#!/bin/bash
# ============================================================================
# DENTALMX - Script de Configuración de .htaccess para macOS + MAMP
# ============================================================================
# Este script configura automáticamente las redirecciones para que no sea
# necesario agregar /index.php/ en las URLs
# Versión: 1.0.0
# © 2024 Roesch Studio
# ============================================================================

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
GRAY='\033[0;37m'
NC='\033[0m' # No Color

# Obtener la ruta del script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_PATH="$SCRIPT_DIR"
PUBLIC_PATH="$PROJECT_PATH/public"

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

# Mostrar banner
clear
echo ""
echo -e "${CYAN}DentalMX - Configurador de .htaccess para macOS${NC}"
echo -e "${GRAY}Versión 1.0.0${NC}"
echo ""

print_header "CONFIGURACIÓN DE REDIRECCIONES"

# ============================================================================
# PASO 1: Verificar que estamos en macOS
# ============================================================================
echo -e "${YELLOW}[1]${WHITE} Verificando sistema operativo...${NC}"

if [[ "$OSTYPE" == "darwin"* ]]; then
    print_success "Sistema operativo: macOS"
else
    print_error "Este script solo funciona en macOS"
    echo ""
    read -p "  Presione Enter para salir..."
    exit 1
fi

# ============================================================================
# PASO 2: Verificar que exista el directorio public
# ============================================================================
echo -e "${YELLOW}[2]${WHITE} Verificando estructura del proyecto...${NC}"

if [ -d "$PUBLIC_PATH" ]; then
    print_success "Directorio /public encontrado"
else
    print_error "Directorio /public no encontrado en: $PUBLIC_PATH"
    read -p "  Presione Enter para salir..."
    exit 1
fi

if [ -f "$PUBLIC_PATH/index.php" ]; then
    print_success "Archivo index.php encontrado"
else
    print_error "Archivo index.php no encontrado"
    read -p "  Presione Enter para salir..."
    exit 1
fi

# ============================================================================
# PASO 3: Crear/Actualizar .htaccess en public/
# ============================================================================
echo -e "${YELLOW}[3]${WHITE} Configurando .htaccess...${NC}"

HTACCESS_FILE="$PUBLIC_PATH/.htaccess"

# Crear el archivo .htaccess con reglas de redirección
cat > "$HTACCESS_FILE" << 'EOF'
# ============================================================================
# DentalMX - Configuración de Apache para CodeIgniter 4 en macOS + MAMP
# ============================================================================
# Este archivo permite que las URLs funcionen sin necesidad de /index.php/

# Habilitar mod_rewrite
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Asegurarse de que la base URL sea correcta (IMPORTANTE PARA MAMP)
    # Descomentar y actualizar si usas MAMP en un puerto diferente
    # RewriteBase /DentalMX_Joss/public/
    
    # Redirigir HTTP a HTTPS en producción (descomentar si usas HTTPS)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Si la solicitud es para un archivo o directorio existente, no hacer reescritura
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Reescribir todas las demás solicitudes a index.php
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>

# ============================================================================
# Configuración de Headers (opcional pero recomendado)
# ============================================================================

# Evitar que se cacheen archivos dinámicos
<FilesMatch "\.php$">
    Header set Cache-Control "no-cache, no-store, must-revalidate"
    Header set Pragma "no-cache"
    Header set Expires "0"
</FilesMatch>

# Permitir que los navegadores cacheen archivos estáticos
<FilesMatch "\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
    Header set Expires "Sun, 17 Jan 2038 19:14:07 GMT"
</FilesMatch>

# ============================================================================
# Seguridad - Prevenir acceso a archivos sensibles
# ============================================================================

# Denegar acceso a archivos .env
<Files ".env*">
    Order Deny,Allow
    Deny from all
</Files>

# Denegar acceso a archivos .htaccess
<Files ".htaccess">
    Order Deny,Allow
    Deny from all
</Files>

# Denegar acceso a composer.json y composer.lock
<Files "composer.*">
    Order Deny,Allow
    Deny from all
</Files>

# Denegar acceso a archivos README y LICENSE
<Files "README*">
    Order Deny,Allow
    Deny from all
</Files>

<Files "LICENSE*">
    Order Deny,Allow
    Deny from all
</Files>

# ============================================================================
# Configuración MIME types adicionales (opcional)
# ============================================================================

<IfModule mod_mime.c>
    AddType application/json .json
    AddType font/woff .woff
    AddType font/woff2 .woff2
    AddEncoding gzip .js.gz
    AddEncoding gzip .css.gz
</IfModule>

# ============================================================================
# Compresión GZIP (opcional pero recomendado)
# ============================================================================

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>

# ============================================================================
# Fin de configuración
# ============================================================================
EOF

if [ -f "$HTACCESS_FILE" ]; then
    print_success ".htaccess creado/actualizado en: $PUBLIC_PATH/.htaccess"
else
    print_error "Error al crear .htaccess"
    read -p "  Presione Enter para salir..."
    exit 1
fi

# ============================================================================
# PASO 4: Crear .htaccess en raíz (opcional pero recomendado)
# ============================================================================
echo -e "${YELLOW}[4]${WHITE} Configurando .htaccess en raíz del proyecto...${NC}"

ROOT_HTACCESS="$PROJECT_PATH/.htaccess"

cat > "$ROOT_HTACCESS" << 'EOF'
# ============================================================================
# DentalMX - Protección de raíz del proyecto
# ============================================================================
# Evitar acceso directo a archivos y directorios sensibles

# Denegar acceso a todo por defecto
Order Deny,Allow
Deny from all

# Permitir acceso solo a public/
<Directory "public">
    Order Allow,Deny
    Allow from all
</Directory>

# Específicamente permitir acceso solo a public/
<Files "~">
    Order Allow,Deny
    Deny from all
</Files>

# Denegar acceso a directorios sensibles
<DirectoryMatch "^/(app|vendor|writable|database|docs|tests)">
    Order Deny,Allow
    Deny from all
</DirectoryMatch>
EOF

if [ -f "$ROOT_HTACCESS" ]; then
    print_success ".htaccess creado en raíz: $PROJECT_PATH/.htaccess"
else
    print_warning "No se pudo crear .htaccess en raíz (continuando...)"
fi

# ============================================================================
# PASO 5: Verificar que mod_rewrite está habilitado en MAMP
# ============================================================================
echo -e "${YELLOW}[5]${WHITE} Verificando mod_rewrite en MAMP...${NC}"

# Buscar apache en MAMP
APACHE_BIN=""
MAMP_VERSIONS=("/Applications/MAMP/Library/bin/httpd" "/usr/local/bin/httpd" "/usr/sbin/httpd")

for apache in "${MAMP_VERSIONS[@]}"; do
    if [ -x "$apache" ]; then
        APACHE_BIN="$apache"
        break
    fi
done

if [ -n "$APACHE_BIN" ]; then
    if "$APACHE_BIN" -M 2>/dev/null | grep -q "rewrite_module"; then
        print_success "mod_rewrite está habilitado en Apache"
    else
        print_warning "mod_rewrite NO está habilitado"
        print_info "Para habilitar mod_rewrite en MAMP:"
        print_info "1. Abre Terminal"
        print_info "2. Edita la configuración de Apache:"
        echo -e "${GRAY}   sudo nano /Applications/MAMP/conf/apache/httpd.conf${NC}"
        print_info "3. Busca la línea: #LoadModule rewrite_module..."
        print_info "4. Descomenta quitando el #"
        print_info "5. Guarda (Ctrl+O, Enter, Ctrl+X)"
        print_info "6. Reinicia los servidores MAMP"
    fi
else
    print_warning "No se encontró Apache en MAMP"
fi

# ============================================================================
# PASO 6: Crear archivo de prueba
# ============================================================================
echo -e "${YELLOW}[6]${WHITE} Creando archivo de prueba...${NC}"

TEST_FILE="$PUBLIC_PATH/test-redirect.php"

cat > "$TEST_FILE" << 'EOF'
<?php
echo "¡Redirección funcionando correctamente!<br>";
echo "URI solicitada: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Script: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "PHP versión: " . phpversion() . "<br>";
echo "<a href='/test-redirect'>Volver a cargar sin index.php</a>";
?>
EOF

if [ -f "$TEST_FILE" ]; then
    print_success "Archivo de prueba creado: $TEST_FILE"
fi

# ============================================================================
# Mostrar resumen
# ============================================================================
echo ""
echo -e "${GREEN}════════════════════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${GREEN}  ¡CONFIGURACIÓN COMPLETADA!${NC}"
echo ""
echo -e "${GREEN}════════════════════════════════════════════════════════════════════════${NC}"
echo ""

echo -e "${CYAN}  ARCHIVOS CONFIGURADOS:${NC}"
echo ""
echo -e "${GRAY}    ✓ $PUBLIC_PATH/.htaccess${NC}"
echo -e "${GRAY}    ✓ $PROJECT_PATH/.htaccess${NC}"
echo ""

echo -e "${CYAN}  PRÓXIMOS PASOS:${NC}"
echo ""
echo -e "${GRAY}    1. Reinicia los servidores MAMP (Stop Servers → Start Servers)${NC}"
echo -e "${GRAY}    2. Abre tu navegador en:${NC}"
echo -e "${YELLOW}       http://localhost:8888/DentalMX_Joss/public${NC}"
echo -e "${GRAY}       (Sin /index.php/)${NC}"
echo ""

echo -e "${CYAN}  PARA PROBAR:${NC}"
echo ""
echo -e "${GRAY}    1. Prueba el archivo de prueba:${NC}"
echo -e "${YELLOW}       http://localhost:8888/test-redirect${NC}"
echo ""
echo -e "${GRAY}    2. Si ves un mensaje, la redirección funciona${NC}"
echo -e "${GRAY}    3. Elimina test-redirect.php después de probar${NC}"
echo ""

echo -e "${RED}  IMPORTANTE - SI NO FUNCIONA:${NC}"
echo ""
echo -e "${GRAY}    1. Verifica que mod_rewrite esté habilitado:${NC}"
echo -e "${GRAY}       cd /Applications/MAMP/Library/bin${NC}"
echo -e "${GRAY}       ./httpd -M | grep rewrite${NC}"
echo ""
echo -e "${GRAY}    2. Si no aparece 'rewrite_module', edita httpd.conf:${NC}"
echo -e "${GRAY}       sudo nano /Applications/MAMP/conf/apache/httpd.conf${NC}"
echo -e "${GRAY}       Busca: #LoadModule rewrite_module${NC}"
echo -e "${GRAY}       Descomenta quitando el #${NC}"
echo ""
echo -e "${GRAY}    3. Verifica los logs de Apache:${NC}"
echo -e "${GRAY}       tail -f /Applications/MAMP/logs/apache_error.log${NC}"
echo ""

echo -e "${GRAY}════════════════════════════════════════════════════════════════════════${NC}"
echo ""

read -p "  Presione Enter para terminar..."
