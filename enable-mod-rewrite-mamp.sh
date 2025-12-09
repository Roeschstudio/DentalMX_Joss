#!/bin/bash
# ============================================================================
# DENTALMX - Habilitar mod_rewrite en MAMP
# ============================================================================
# Este script habilita mod_rewrite en MAMP para que las URLs funcionen
# sin necesidad de agregar /index.php/
#
# Versión: 1.0.0
# © 2024 Roesch Studio
# ============================================================================

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
NC='\033[0m'

clear
echo ""
echo -e "${CYAN}╔══════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║${WHITE}           🔧 Habilitar mod_rewrite en MAMP                       ${CYAN}║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Verificar MAMP
if [ ! -d "/Applications/MAMP" ]; then
    echo -e "${RED}✗${NC} MAMP no encontrado"
    exit 1
fi

echo -e "${GREEN}✓${NC} MAMP encontrado"

# Archivo de configuración de Apache
HTTPD_CONF="/Applications/MAMP/conf/apache/httpd.conf"

if [ ! -f "$HTTPD_CONF" ]; then
    echo -e "${RED}✗${NC} Archivo httpd.conf no encontrado"
    exit 1
fi

echo -e "${GREEN}✓${NC} httpd.conf encontrado"
echo ""

# Hacer backup
BACKUP_FILE="$HTTPD_CONF.backup.$(date +%Y%m%d_%H%M%S)"
cp "$HTTPD_CONF" "$BACKUP_FILE"
echo -e "${CYAN}→${NC} Backup creado: $BACKUP_FILE"

# ============================================================================
# PASO 1: Habilitar mod_rewrite
# ============================================================================
echo ""
echo -e "${YELLOW}[1/3] Habilitando mod_rewrite...${NC}"

# Verificar si ya está habilitado
if grep -q "^LoadModule rewrite_module" "$HTTPD_CONF"; then
    echo -e "${GREEN}✓${NC} mod_rewrite ya está habilitado"
else
    # Buscar línea comentada y descomentarla
    if grep -q "#LoadModule rewrite_module" "$HTTPD_CONF"; then
        sed -i '' 's/#LoadModule rewrite_module/LoadModule rewrite_module/' "$HTTPD_CONF"
        echo -e "${GREEN}✓${NC} mod_rewrite habilitado"
    else
        echo -e "${YELLOW}⚠${NC} No se encontró la línea de mod_rewrite"
    fi
fi

# ============================================================================
# PASO 2: Configurar AllowOverride All
# ============================================================================
echo ""
echo -e "${YELLOW}[2/3] Configurando AllowOverride All...${NC}"

# Buscar y modificar la configuración de htdocs
# Esto es más complejo porque necesitamos modificar dentro de un bloque <Directory>

# Primero verificar si ya está configurado
if grep -A5 "htdocs" "$HTTPD_CONF" | grep -q "AllowOverride All"; then
    echo -e "${GREEN}✓${NC} AllowOverride All ya está configurado"
else
    echo -e "${YELLOW}⚠${NC} Necesita configurar AllowOverride manualmente"
    echo ""
    echo -e "${WHITE}Instrucciones:${NC}"
    echo -e "  1. Abre ${CYAN}/Applications/MAMP/conf/apache/httpd.conf${NC}"
    echo -e "  2. Busca la sección: ${WHITE}<Directory \"/Applications/MAMP/htdocs\">${NC}"
    echo -e "  3. Cambia: ${RED}AllowOverride None${NC}"
    echo -e "     Por:    ${GREEN}AllowOverride All${NC}"
    echo ""
fi

# ============================================================================
# PASO 3: Verificar configuración
# ============================================================================
echo ""
echo -e "${YELLOW}[3/3] Verificando configuración...${NC}"

# Mostrar estado
echo ""
echo -e "${CYAN}Estado actual de httpd.conf:${NC}"
echo ""

# mod_rewrite
if grep -q "^LoadModule rewrite_module" "$HTTPD_CONF"; then
    echo -e "  ${GREEN}✓${NC} mod_rewrite: HABILITADO"
else
    echo -e "  ${RED}✗${NC} mod_rewrite: DESHABILITADO"
fi

# AllowOverride
echo ""

# ============================================================================
# RESUMEN
# ============================================================================
echo ""
echo -e "${CYAN}╔══════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║${WHITE}                    PRÓXIMOS PASOS                                ${CYAN}║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${WHITE}1.${NC} Abre MAMP"
echo -e "  ${WHITE}2.${NC} Detén los servidores (Stop Servers)"
echo -e "  ${WHITE}3.${NC} Inicia los servidores (Start Servers)"
echo -e "  ${WHITE}4.${NC} Recarga tu navegador"
echo ""
echo -e "  ${CYAN}Si las URLs sin /index.php/ no funcionan:${NC}"
echo ""
echo -e "  ${WHITE}1.${NC} Abre: ${CYAN}/Applications/MAMP/conf/apache/httpd.conf${NC}"
echo -e "  ${WHITE}2.${NC} Busca: ${RED}<Directory \"/Applications/MAMP/htdocs\">${NC}"
echo -e "  ${WHITE}3.${NC} Cambia: ${RED}AllowOverride None${NC} → ${GREEN}AllowOverride All${NC}"
echo -e "  ${WHITE}4.${NC} Guarda y reinicia MAMP"
echo ""

read -p "Presione Enter para continuar..."
