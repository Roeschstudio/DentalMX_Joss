<?php
/**
 * Componente Sidebar - Design System
 * 
 * Variables disponibles:
 * - $currentPage: string - Página actual para marcar como activa
 * - $userSession: array - Datos del usuario (opcional, usa session por defecto)
 * - $clinicaConfig: array - Configuración de la clínica (nombre, logo, etc.)
 */
$currentPage = $currentPage ?? '';

// Obtener configuración de la clínica para el nombre dinámico
$configModel = new \App\Models\ConfiguracionClinicaModel();
$clinicaConfig = $configModel->getConfiguracion();
$nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';

// Obtener datos del usuario - compatible con ambas estructuras de sesión
$session = session();
$isLoggedIn = $session->get('isLoggedIn') ?? false;

// Intentar obtener usuario de 'usuario' key o directamente de la sesión
if (isset($userSession)) {
    $usuario = $userSession;
} elseif ($session->has('usuario')) {
    $usuario = $session->get('usuario');
} elseif ($isLoggedIn) {
    // La sesión guarda los datos directamente
    $usuario = [
        'id' => $session->get('id'),
        'nombre' => $session->get('nombre'),
        'apellido' => $session->get('apellido') ?? '',
        'email' => $session->get('email'),
        'rol' => $session->get('rol')
    ];
} else {
    $usuario = null;
}

$userName = $usuario['nombre'] ?? 'Usuario';
$userApellido = $usuario['apellido'] ?? '';
$userRole = $usuario['rol'] ?? 'Doctor';
$userEmail = $usuario['email'] ?? '';
$userAvatar = $usuario['avatar'] ?? null;

// Generar iniciales para avatar
$iniciales = strtoupper(substr($userName, 0, 1));
if (!empty($userApellido)) {
    $iniciales .= strtoupper(substr($userApellido, 0, 1));
} else {
    $iniciales .= strtoupper(substr($userName, 1, 1));
}
?>
<aside class="ds-sidebar" id="sidebar" data-state="expanded">
    <div class="ds-sidebar__header">
        <a href="<?= base_url('/') ?>" class="ds-sidebar__logo">
            <span class="ds-sidebar__logo-icon">🦷</span>
            <span class="ds-sidebar__logo-text"><?= esc($nombreClinica) ?></span>
        </a>
        <button class="ds-sidebar__toggle" 
                id="sidebarToggle"
                onclick="toggleSidebarCollapse()" 
                aria-label="Contraer sidebar"
                aria-expanded="true"
                aria-controls="sidebar"
                title="Contraer/Expandir sidebar">
            <span class="ds-sidebar__toggle-icon">◀</span>
        </button>
    </div>
    
    <nav class="ds-sidebar__nav">
        <ul class="ds-sidebar__menu">
            <!-- 1. Pacientes -->
            <li class="ds-sidebar__item">
                <a href="<?= base_url('/pacientes') ?>" 
                   class="ds-sidebar__link <?= $currentPage == 'pacientes' ? 'is-active' : '' ?>" 
                   data-tooltip="Pacientes">
                    <span class="ds-sidebar__icon">👥</span>
                    <span class="ds-sidebar__text">Pacientes</span>
                    <?php if ($currentPage == 'pacientes'): ?>
                        <span class="ds-sidebar__active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- 2. Citas -->
            <li class="ds-sidebar__item">
                <a href="<?= base_url('/citas') ?>" 
                   class="ds-sidebar__link <?= in_array($currentPage, ['citas', 'calendario']) ? 'is-active' : '' ?>" 
                   data-tooltip="Citas">
                    <span class="ds-sidebar__icon">📅</span>
                    <span class="ds-sidebar__text">Citas</span>
                    <?php if (in_array($currentPage, ['citas', 'calendario'])): ?>
                        <span class="ds-sidebar__active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- 3. Recetas -->
            <li class="ds-sidebar__item">
                <a href="<?= base_url('/recetas') ?>" 
                   class="ds-sidebar__link <?= $currentPage == 'recetas' ? 'is-active' : '' ?>" 
                   data-tooltip="Recetas">
                    <span class="ds-sidebar__icon">📋</span>
                    <span class="ds-sidebar__text">Recetas</span>
                    <?php if ($currentPage == 'recetas'): ?>
                        <span class="ds-sidebar__active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- 4. Presupuestos -->
            <li class="ds-sidebar__item">
                <a href="<?= base_url('/presupuestos') ?>" 
                   class="ds-sidebar__link <?= $currentPage == 'presupuestos' ? 'is-active' : '' ?>" 
                   data-tooltip="Presupuestos">
                    <span class="ds-sidebar__icon">📄</span>
                    <span class="ds-sidebar__text">Presupuestos</span>
                    <?php if ($currentPage == 'presupuestos'): ?>
                        <span class="ds-sidebar__active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- 5. Cotizaciones -->
            <li class="ds-sidebar__item">
                <a href="<?= base_url('/cotizaciones') ?>" 
                   class="ds-sidebar__link <?= $currentPage == 'cotizaciones' ? 'is-active' : '' ?>" 
                   data-tooltip="Cotizaciones">
                    <span class="ds-sidebar__icon">💰</span>
                    <span class="ds-sidebar__text">Cotizaciones</span>
                    <?php if ($currentPage == 'cotizaciones'): ?>
                        <span class="ds-sidebar__active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- 6. Medicamentos -->
            <li class="ds-sidebar__item">
                <a href="<?= base_url('/medicamentos') ?>" 
                   class="ds-sidebar__link <?= $currentPage == 'medicamentos' ? 'is-active' : '' ?>" 
                   data-tooltip="Medicamentos">
                    <span class="ds-sidebar__icon">💊</span>
                    <span class="ds-sidebar__text">Medicamentos</span>
                    <?php if ($currentPage == 'medicamentos'): ?>
                        <span class="ds-sidebar__active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- 7. Servicios -->
            <li class="ds-sidebar__item">
                <a href="<?= base_url('/servicios') ?>" 
                   class="ds-sidebar__link <?= $currentPage == 'servicios' ? 'is-active' : '' ?>" 
                   data-tooltip="Servicios">
                    <span class="ds-sidebar__icon">🔧</span>
                    <span class="ds-sidebar__text">Servicios</span>
                    <?php if ($currentPage == 'servicios'): ?>
                        <span class="ds-sidebar__active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li class="ds-sidebar__divider"></li>
            
            <!-- 8. Ajustes -->
            <li class="ds-sidebar__item">
                <a href="<?= base_url('/ajustes') ?>" 
                   class="ds-sidebar__link <?= in_array($currentPage, ['ajustes', 'configuracion']) ? 'is-active' : '' ?>" 
                   data-tooltip="Ajustes">
                    <span class="ds-sidebar__icon">⚙️</span>
                    <span class="ds-sidebar__text">Ajustes</span>
                    <?php if (in_array($currentPage, ['ajustes', 'configuracion'])): ?>
                        <span class="ds-sidebar__active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- 9. Cerrar Sesión -->
            <li class="ds-sidebar__item ds-sidebar__item--logout">
                <a href="<?= base_url('/logout') ?>" 
                   class="ds-sidebar__link ds-sidebar__link--danger"
                   data-tooltip="Cerrar Sesión"
                   onclick="return confirm('¿Estás seguro de que deseas cerrar sesión?')">
                    <span class="ds-sidebar__icon">🚪</span>
                    <span class="ds-sidebar__text">Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Footer simplificado - Usuario solo en Header -->
    <div class="ds-sidebar__footer">
        <div class="ds-sidebar__branding">
            <span class="ds-sidebar__version">v2.0</span>
            <span class="ds-sidebar__copyright">© <?= date('Y') ?> <?= esc($nombreClinica) ?></span>
        </div>
    </div>
</aside>

<!-- Backdrop para móvil -->
<div class="ds-sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

<!-- Script para funcionalidad del sidebar -->
<script>
// Toggle sidebar collapse con data-state y animación
function toggleSidebarCollapse() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    
    if (!sidebar || !toggle) return;
    
    const isCollapsed = sidebar.getAttribute('data-state') === 'collapsed';
    const newState = isCollapsed ? 'expanded' : 'collapsed';
    
    // Actualizar estado
    sidebar.setAttribute('data-state', newState);
    sidebar.classList.toggle('is-collapsed', !isCollapsed);
    
    // Actualizar aria
    toggle.setAttribute('aria-expanded', isCollapsed ? 'true' : 'false');
    toggle.setAttribute('aria-label', isCollapsed ? 'Contraer sidebar' : 'Expandir sidebar');
    
    // Guardar preferencia
    localStorage.setItem('sidebar-state', newState);
}

// Restaurar estado del sidebar al cargar
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const savedState = localStorage.getItem('sidebar-state');
    
    if (sidebar && savedState) {
        const isCollapsed = savedState === 'collapsed';
        sidebar.setAttribute('data-state', savedState);
        sidebar.classList.toggle('is-collapsed', isCollapsed);
        
        if (toggle) {
            toggle.setAttribute('aria-expanded', !isCollapsed ? 'true' : 'false');
        }
    }
});

// Mobile sidebar toggle
function toggleSidebarMobile() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    
    if (!sidebar) return;
    
    sidebar.classList.toggle('is-open');
    if (backdrop) backdrop.classList.toggle('is-active');
    document.body.style.overflow = sidebar.classList.contains('is-open') ? 'hidden' : '';
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    
    if (sidebar) sidebar.classList.remove('is-open');
    if (backdrop) backdrop.classList.remove('is-active');
    document.body.style.overflow = '';
}
</script>

