<?php
/**
 * Componente Breadcrumbs - Design System
 * 
 * Variables disponibles:
 * - $breadcrumb: array|object - Array de items o instancia de BreadcrumbHelper
 * - $separator: string - Separador entre items (default: "/")
 * - $homeIcon: string - Icono para inicio (default: "🏠")
 * - $showHome: bool - Mostrar icono de inicio (default: true)
 * 
 * Estructura de items:
 * [
 *     ['title' => 'Dashboard', 'url' => '/', 'active' => false],
 *     ['title' => 'Pacientes', 'url' => '/pacientes', 'active' => false],
 *     ['title' => 'Juan Pérez', 'url' => null, 'active' => true]
 * ]
 */

// Variables por defecto
$separator = $separator ?? '/';
$homeIcon = $homeIcon ?? '🏠';
$showHome = $showHome ?? true;

// Procesar breadcrumb
$items = [];

if (is_object($breadcrumb) && method_exists($breadcrumb, 'getItems')) {
    // Es una instancia de BreadcrumbHelper
    $items = $breadcrumb->getItems();
} elseif (is_array($breadcrumb)) {
    // Es un array directo
    $items = $breadcrumb;
} else {
    // No hay breadcrumb válido
    return;
}

// Asegurar que siempre haya al menos Dashboard
if (empty($items) || (count($items) === 1 && $items[0]['title'] === 'Dashboard')) {
    return;
}
?>

<!-- Breadcrumbs con microdatos estructurados -->
<nav class="ds-breadcrumb-wrapper" aria-label="Navegación de migajas de pan">
    <ol class="ds-breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
        
        <?php if ($showHome && (!isset($items[0]['title']) || $items[0]['title'] !== 'Dashboard')): ?>
            <!-- Item de inicio -->
            <li class="ds-breadcrumb__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="<?= base_url('/') ?>" class="ds-breadcrumb__link ds-breadcrumb__link--home" 
                   itemprop="item" title="Inicio">
                    <span class="ds-breadcrumb__icon"><?= $homeIcon ?></span>
                    <span itemprop="name">Inicio</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
        <?php endif; ?>
        
        <?php foreach ($items as $index => $item): ?>
            <?php 
            $position = $showHome ? $index + 2 : $index + 1;
            $isLast = $index === array_key_last($items);
            $isActive = $item['active'] ?? false;
            $hasUrl = !empty($item['url']) && !$isActive;
            ?>
            
            <li class="ds-breadcrumb__item <?= $isActive ? 'ds-breadcrumb__item--active' : '' ?>" 
                itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                
                <?php if ($hasUrl): ?>
                    <!-- Item con enlace -->
                    <a href="<?= esc($item['url']) ?>" 
                       class="ds-breadcrumb__link" 
                       itemprop="item"
                       title="<?= esc($item['title']) ?>">
                        <span itemprop="name"><?= esc($item['title']) ?></span>
                    </a>
                <?php else: ?>
                    <!-- Item actual (sin enlace) -->
                    <span class="ds-breadcrumb__current" itemprop="name">
                        <?= esc($item['title']) ?>
                    </span>
                <?php endif; ?>
                
                <meta itemprop="position" content="<?= $position ?>">
            </li>
            
            <?php if (!$isLast): ?>
                <!-- Separador -->
                <li class="ds-breadcrumb__separator" aria-hidden="true">
                    <?= $separator ?>
                </li>
            <?php endif; ?>
            
        <?php endforeach; ?>
        
    </ol>
    
    <!-- Breadcrumb alternativo para móvil (solo íconos) -->
    <ol class="ds-breadcrumb ds-breadcrumb--mobile" aria-hidden="true">
        <?php if ($showHome): ?>
            <li class="ds-breadcrumb__item">
                <a href="<?= base_url('/') ?>" class="ds-breadcrumb__link" title="Inicio">
                    <span class="ds-breadcrumb__icon"><?= $homeIcon ?></span>
                </a>
            </li>
        <?php endif; ?>
        
        <?php if (!empty($items)): ?>
            <?php $lastItem = end($items); ?>
            <li class="ds-breadcrumb__item">
                <span class="ds-breadcrumb__current">
                    <?= esc($lastItem['title'] ?? 'Página actual') ?>
                </span>
            </li>
        <?php endif; ?>
    </ol>
</nav>

<!-- Script para accesibilidad -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Anunciar cambios de navegación para lectores de pantalla
    const breadcrumbLinks = document.querySelectorAll('.ds-breadcrumb__link');
    breadcrumbLinks.forEach(link => {
        link.addEventListener('click', function() {
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('class', 'sr-only');
            announcement.textContent = `Navegando a: ${this.textContent.trim()}`;
            document.body.appendChild(announcement);
            
            setTimeout(() => {
                document.body.removeChild(announcement);
            }, 1000);
        });
    });
});
</script>

