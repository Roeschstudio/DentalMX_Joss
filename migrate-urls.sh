#!/bin/bash
# ============================================================================
# DENTALMX - Migrar base_url a site_url
# ============================================================================
# Este script reemplaza base_url('/ruta') por site_url('ruta') en las vistas
# Esto es necesario para que las URLs funcionen con app.indexPage = 'index.php'
#
# Versión: 1.0.0
# © 2024 Roesch Studio
# ============================================================================

set -e

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
VIEWS_DIR="$SCRIPT_DIR/app/Views"
CONTROLLERS_DIR="$SCRIPT_DIR/app/Controllers"

echo ""
echo -e "${CYAN}╔══════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║${NC}     🔄 Migrar base_url() a site_url() en vistas                 ${CYAN}║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Verificar directorio de vistas
if [ ! -d "$VIEWS_DIR" ]; then
    echo -e "${RED}✗${NC} Directorio de vistas no encontrado: $VIEWS_DIR"
    exit 1
fi

echo -e "${CYAN}→${NC} Directorio de vistas: $VIEWS_DIR"
echo ""

# Contar archivos afectados
FILES_COUNT=$(grep -rl "base_url('/" "$VIEWS_DIR" 2>/dev/null | wc -l | tr -d ' ')

echo -e "${YELLOW}⚠${NC} Se encontraron $FILES_COUNT archivos con base_url('/')"
echo ""

read -p "¿Desea continuar con el reemplazo? (s/n): " CONFIRM
if [[ ! "$CONFIRM" =~ ^[Ss]$ ]]; then
    echo "Operación cancelada."
    exit 0
fi

echo ""
echo -e "${CYAN}→${NC} Creando backup..."

# Crear backup
BACKUP_DIR="$SCRIPT_DIR/backup_views_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -R "$VIEWS_DIR" "$BACKUP_DIR/"
echo -e "${GREEN}✓${NC} Backup creado en: $BACKUP_DIR"

echo ""
echo -e "${CYAN}→${NC} Procesando archivos..."

# Patrones a reemplazar:
# base_url('/algo') -> site_url('algo')
# base_url("/algo") -> site_url("algo")

# Procesar archivos PHP en Views
find "$VIEWS_DIR" -name "*.php" -type f | while read -r file; do
    if grep -q "base_url('/" "$file" 2>/dev/null || grep -q 'base_url("/' "$file" 2>/dev/null; then
        echo -e "  ${CYAN}→${NC} Procesando: $(basename "$file")"
        
        # Reemplazar base_url('/...) por site_url('...
        # Nota: Mantenemos las URLs de assets (css, js, assets, uploads)
        
        if [[ "$OSTYPE" == "darwin"* ]]; then
            # macOS usa sed con -i ''
            sed -i '' "s/base_url('\\/\\([^']*\\)')/site_url('\\1')/g" "$file"
            sed -i '' 's/base_url("\/\([^"]*\)")/site_url("\1")/g' "$file"
        else
            # Linux usa sed con -i
            sed -i "s/base_url('\\/\\([^']*\\)')/site_url('\\1')/g" "$file"
            sed -i 's/base_url("\/\([^"]*\)")/site_url("\1")/g' "$file"
        fi
    fi
done

# Procesar controladores
echo ""
echo -e "${CYAN}→${NC} Procesando controladores..."

find "$CONTROLLERS_DIR" -name "*.php" -type f | while read -r file; do
    if grep -q "base_url('/" "$file" 2>/dev/null || grep -q 'base_url("/' "$file" 2>/dev/null; then
        echo -e "  ${CYAN}→${NC} Procesando: $(basename "$file")"
        
        if [[ "$OSTYPE" == "darwin"* ]]; then
            sed -i '' "s/base_url('\\/\\([^']*\\)')/site_url('\\1')/g" "$file"
            sed -i '' 's/base_url("\/\([^"]*\)")/site_url("\1")/g' "$file"
        else
            sed -i "s/base_url('\\/\\([^']*\\)')/site_url('\\1')/g" "$file"
            sed -i 's/base_url("\/\([^"]*\)")/site_url("\1")/g' "$file"
        fi
    fi
done

echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║${NC}              ✓ MIGRACIÓN COMPLETADA                              ${GREEN}║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${CYAN}Backup:${NC} $BACKUP_DIR"
echo ""
echo -e "  ${YELLOW}IMPORTANTE:${NC}"
echo -e "  Después de ejecutar este script, verifica que:"
echo -e "  1. Los enlaces de navegación funcionan"
echo -e "  2. Los formularios envían correctamente"
echo -e "  3. Las llamadas AJAX funcionan"
echo ""
echo -e "  Si algo no funciona, restaura el backup:"
echo -e "  ${CYAN}cp -R $BACKUP_DIR/Views/* app/Views/${NC}"
echo ""
