<?php
/**
 * Layout Principal del Design System
 * 
 * Variables disponibles:
 * - $pageTitle: string - Título de la página
 * - $pageSubtitle: string - Subtítulo opcional
 * - $currentPage: string - Página actual para sidebar
 * - $breadcrumb: array - Breadcrumbs personalizados (opcional)
 * - $showMobileMenu: bool - Mostrar menú móvil
 */

// Función helper para generar breadcrumbs desde el contexto
if (!function_exists('generateBreadcrumbFromContext')) {
    function generateBreadcrumbFromContext() {
        // Obtener la URL actual
        $uri = service('uri');
        $segments = $uri->getSegments();
        
        // Si no hay segmentos, estamos en el dashboard
        if (empty($segments)) {
            return [];
        }
        
        // Usar el helper si está disponible
        if (class_exists('\App\Helpers\BreadcrumbHelper')) {
            try {
                $currentUrl = current_url();
                return \App\Helpers\BreadcrumbHelper::generateFromUrl($currentUrl);
            } catch (\Exception $e) {
                log_message('error', 'Error generando breadcrumbs: ' . $e->getMessage());
            }
        }
        
        // Generación básica si no está disponible el helper
        $breadcrumb = [];
        $accumulatedPath = '';
        
        // Procesar segmentos
        foreach ($segments as $index => $segment) {
            if (empty($segment)) continue;
            
            $accumulatedPath .= '/' . $segment;
            
            // Ignorar segmentos numéricos (IDs)
            if (is_numeric($segment)) continue;
            
            $isLast = $index === count($segments) - 1;
            
            $title = getBreadcrumbTitle($segment);
            
            $breadcrumb[] = [
                'title' => $title,
                'url' => $isLast ? null : base_url($accumulatedPath),
                'active' => $isLast
            ];
        }
        
        return $breadcrumb;
    }
}

if (!function_exists('getBreadcrumbTitle')) {
    function getBreadcrumbTitle($segment) {
        $titles = [
            'pacientes' => 'Pacientes',
            'citas' => 'Citas',
            'recetas' => 'Recetas',
            'cotizaciones' => 'Presupuestos',
            'medicamentos' => 'Medicamentos',
            'servicios' => 'Servicios',
            'configuracion' => 'Ajustes',
            'agenda' => 'Horario',
            'calendario' => 'Calendario',
            'perfil' => 'Mi Perfil',
            'nueva' => 'Nueva',
            'nuevo' => 'Nuevo',
            'crear' => 'Crear',
            'editar' => 'Editar',
            'ver' => 'Ver',
            'detalle' => 'Detalle',
            'historial' => 'Historial'
        ];
        
        return $titles[$segment] ?? ucfirst($segment);
    }
}

// Generar breadcrumbs automáticos si no se proporcionan
$autoBreadcrumb = $breadcrumb ?? generateBreadcrumbFromContext();

// Obtener configuración de la clínica para el nombre dinámico
$configModel = new \App\Models\ConfiguracionClinicaModel();
$clinicaConfig = $configModel->getConfiguracion();
$nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';

// Obtener preferencias del usuario para el tema
$userTheme = 'light'; // Default
$userLang = 'es'; // Default
$userDateFormat = 'd/m/Y'; // Default
$session = session();
if ($session->get('id')) {
    $prefModel = new \App\Models\PreferenciasUsuarioModel();
    $userPrefs = $prefModel->getPreferencias($session->get('id'));
    if ($userPrefs) {
        $userTheme = $userPrefs['tema'] ?? 'light';
        $userLang = $userPrefs['idioma'] ?? 'es';
        $userDateFormat = $userPrefs['formato_fecha'] ?? 'd/m/Y';
    }
}
// Si el tema es 'auto', dejamos que JavaScript lo maneje
$initialTheme = ($userTheme === 'auto') ? 'light' : $userTheme;
?>
<!DOCTYPE html>
<html lang="<?= esc($userLang) ?>" data-theme="<?= esc($initialTheme) ?>" data-user-theme-pref="<?= esc($userTheme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($nombreClinica) ?> - Sistema de Gestión Dental">
    <meta name="theme-color" content="#5ccdde">
    
    <title><?= esc($pageTitle ?? 'Dashboard') ?> - <?= esc($nombreClinica) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    
    <!-- Design System CSS v2 (Modular Components) -->
    <link rel="stylesheet" href="<?= base_url('css/design-system-v2.css') ?>">
    
    <!-- Estilos adicionales de la página -->
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Skip Link para navegación por teclado -->
    <a href="#main-content" class="ds-skip-link">Saltar al contenido principal</a>

    <!-- Sidebar -->
    <?= view('components/sidebar', ['currentPage' => $currentPage ?? '']) ?>
    
    <!-- Main Content -->
    <main class="ds-main" id="main-content">
        <!-- Header con breadcrumbs automáticos -->
        <?= view('components/header', [
            'pageTitle' => $pageTitle ?? 'Dashboard',
            'pageSubtitle' => $pageSubtitle ?? null,
            'breadcrumb' => $autoBreadcrumb,
            'showMobileMenu' => $showMobileMenu ?? true
        ]) ?>
        
        <!-- Page Content -->
        <div class="ds-content">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="ds-alert ds-alert--success ds-fade-in">
                    <span class="ds-alert__icon">✅</span>
                    <div class="ds-alert__content">
                        <p class="ds-alert__text"><?= session()->getFlashdata('success') ?></p>
                    </div>
                    <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="ds-alert ds-alert--danger ds-fade-in">
                    <span class="ds-alert__icon">❌</span>
                    <div class="ds-alert__content">
                        <p class="ds-alert__text"><?= session()->getFlashdata('error') ?></p>
                    </div>
                    <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('warning')): ?>
                <div class="ds-alert ds-alert--warning ds-fade-in">
                    <span class="ds-alert__icon">⚠️</span>
                    <div class="ds-alert__content">
                        <p class="ds-alert__text"><?= session()->getFlashdata('warning') ?></p>
                    </div>
                    <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('info')): ?>
                <div class="ds-alert ds-alert--info ds-fade-in">
                    <span class="ds-alert__icon">ℹ️</span>
                    <div class="ds-alert__content">
                        <p class="ds-alert__text"><?= session()->getFlashdata('info') ?></p>
                    </div>
                    <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>
            
            <!-- Contenido de la página -->
            <?= $this->renderSection('content') ?>
        </div>
        
        <!-- Footer -->
        <?= view('components/footer') ?>
    </main>
    
    <!-- Toast Container -->
    <div class="ds-toast-container ds-toast-container--top-right" id="toastContainer"></div>
    
    <!-- Modal Templates (opcional) -->
    <?php if (isset($includeModalTemplates) && $includeModalTemplates): ?>
        <?= view('components/modal_templates') ?>
    <?php endif; ?>
    
    <!-- Design System JavaScript -->
    <script src="<?= base_url('js/design-system.js') ?>"></script>
    
    <!-- Scripts adicionales de la página -->
    <?= $this->renderSection('scripts') ?>
    
    <!-- Script para mejorar breadcrumbs dinámicamente (opcional) -->
    <script>
    /**
     * Mejora los breadcrumbs con información contextual de la página
     * Solo se ejecuta si los breadcrumbs fueron generados automáticamente
     */
    document.addEventListener('DOMContentLoaded', function() {
        enhanceBreadcrumbsWithContext();
    });
    
    function enhanceBreadcrumbsWithContext() {
        const breadcrumbWrapper = document.querySelector('.ds-header__breadcrumb-wrapper');
        if (!breadcrumbWrapper) return;
        
        const breadcrumbItems = breadcrumbWrapper.querySelectorAll('.ds-breadcrumb__item');
        if (breadcrumbItems.length === 0) return;
        
        // Extraer información contextual de la página
        const context = extractPageContext();
        
        // Si hay un nombre de entidad, actualizar el último breadcrumb
        if (context.entityName) {
            const lastItem = breadcrumbItems[breadcrumbItems.length - 1];
            const currentSpan = lastItem.querySelector('.ds-breadcrumb__current, span[aria-current="page"]');
            
            if (currentSpan && !currentSpan.textContent.includes(context.entityName)) {
                // Solo actualizar si el nombre no está ya incluido
                const currentText = currentSpan.textContent.trim();
                
                // Evitar duplicados
                if (currentText !== context.entityName) {
                    currentSpan.textContent = context.entityName;
                }
            }
        }
        
        // Agregar data attributes para analytics
        breadcrumbItems.forEach((item, index) => {
            const link = item.querySelector('a, span');
            if (link) {
                link.setAttribute('data-breadcrumb-position', index + 1);
                link.setAttribute('data-breadcrumb-total', breadcrumbItems.length);
            }
        });
    }
    
    function extractPageContext() {
        const context = {
            entityName: null,
            entityType: null,
            action: null
        };
        
        // Intentar extraer nombre de entidad de diferentes fuentes
        const selectors = [
            '[data-entity-name]',
            '.ds-entity-name',
            '.patient-name',
            '.appointment-patient',
            '.ds-page-header h1',
            '.ds-card__title:first-of-type'
        ];
        
        for (const selector of selectors) {
            const element = document.querySelector(selector);
            if (element) {
                let name = element.getAttribute('data-entity-name') || element.textContent.trim();
                
                // Validar que el nombre sea razonable
                if (name && name.length > 0 && name.length < 100 && !name.includes('undefined')) {
                    context.entityName = name;
                    break;
                }
            }
        }
        
        // Detectar tipo de entidad desde la URL
        const pathSegments = window.location.pathname.split('/').filter(Boolean);
        if (pathSegments.length > 0) {
            const firstSegment = pathSegments[0];
            const entityTypes = ['pacientes', 'citas', 'recetas', 'cotizaciones', 'medicamentos', 'servicios'];
            
            if (entityTypes.includes(firstSegment)) {
                context.entityType = firstSegment;
            }
        }
        
        // Detectar acción
        const actionKeywords = ['nueva', 'nuevo', 'crear', 'editar', 'ver', 'detalle'];
        for (const segment of pathSegments) {
            if (actionKeywords.includes(segment)) {
                context.action = segment;
                break;
            }
        }
        
        return context;
    }
    
    // Función de utilidad para escapar HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Analytics de breadcrumbs (opcional)
    function trackBreadcrumbClick(event) {
        const link = event.currentTarget;
        const position = link.getAttribute('data-breadcrumb-position');
        const total = link.getAttribute('data-breadcrumb-total');
        const title = link.textContent.trim();
        
        // Aquí puedes enviar a Google Analytics, Mixpanel, etc.
        console.log('Breadcrumb clicked:', {
            title: title,
            position: position,
            total: total,
            url: link.href
        });
        
        // Ejemplo con Google Analytics (si está disponible)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'breadcrumb_click', {
                'breadcrumb_title': title,
                'breadcrumb_position': position,
                'breadcrumb_total': total
            });
        }
    }
    
    // Agregar listeners para analytics
    document.querySelectorAll('.ds-breadcrumb__link').forEach(link => {
        link.addEventListener('click', trackBreadcrumbClick);
    });
    </script>
</body>
</html>