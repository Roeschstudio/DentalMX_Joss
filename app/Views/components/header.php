<?php
/**
 * Componente Header - Design System
 * 
 * Variables disponibles:
 * - $pageTitle: string - Título de la página
 * - $breadcrumb: array - Array de breadcrumbs [{title, url, active}]
 * - $showMobileMenu: bool - Mostrar menú móvil (default: true)
 */
$pageTitle = $pageTitle ?? 'Dashboard';
$breadcrumb = $breadcrumb ?? [];
$showMobileMenu = $showMobileMenu ?? true;

// Obtener usuario de sesión - compatible con ambas estructuras
$session = session();
$isLoggedIn = $session->get('isLoggedIn') ?? false;

if ($session->has('usuario')) {
    $headerUser = $session->get('usuario');
} elseif ($isLoggedIn) {
    $headerUser = [
        'nombre' => $session->get('nombre'),
        'apellido' => $session->get('apellido') ?? '',
        'email' => $session->get('email'),
        'rol' => $session->get('rol')
    ];
} else {
    $headerUser = null;
}

$headerUserName = $headerUser['nombre'] ?? 'Usuario';
$headerUserApellido = $headerUser['apellido'] ?? '';
$headerUserEmail = $headerUser['email'] ?? '';
$headerUserRole = $headerUser['rol'] ?? 'Invitado';

// Generar iniciales
$headerIniciales = strtoupper(substr($headerUserName, 0, 1));
if (!empty($headerUserApellido)) {
    $headerIniciales .= strtoupper(substr($headerUserApellido, 0, 1));
} else {
    $headerIniciales .= strtoupper(substr($headerUserName, 1, 1));
}
?>

<header class="ds-header">
    <!-- Sección Izquierda -->
    <div class="ds-header__left">
        <?php if ($showMobileMenu): ?>
            <button class="ds-header__menu-toggle" 
                    onclick="toggleMobileMenu()" 
                    aria-label="Abrir menú"
                    aria-expanded="false"
                    aria-controls="sidebar">
                <span class="ds-header__hamburger">
                    <span class="ds-header__hamburger-line"></span>
                    <span class="ds-header__hamburger-line"></span>
                    <span class="ds-header__hamburger-line"></span>
                </span>
            </button>
        <?php endif; ?>
        
        <?php if (!empty($breadcrumb)): ?>
            <div class="ds-header__breadcrumb-wrapper">
                <?= view('components/breadcrumbs', ['breadcrumb' => $breadcrumb, 'showHome' => false]) ?>
            </div>
        <?php else: ?>
            <div class="ds-header__title-wrapper">
                <h1 class="ds-header__title"><?= esc($pageTitle) ?></h1>
                <?php if (isset($pageSubtitle)): ?>
                    <p class="ds-header__subtitle"><?= esc($pageSubtitle) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Búsqueda Desktop -->
    <div class="ds-header__search">
        <span class="ds-header__search-icon">🔍</span>
        <input type="text" 
               class="ds-header__search-input" 
               placeholder="Buscar pacientes, citas..." 
               id="globalSearch">
        <button class="ds-header__search-clear" onclick="clearSearch()" aria-label="Limpiar">×</button>
    </div>
    
    <!-- Sección Derecha -->
    <div class="ds-header__right">
        <!-- Búsqueda Móvil Toggle -->
        <button class="ds-header__icon-btn ds-header__search-toggle" 
                onclick="toggleMobileSearch()"
                aria-label="Buscar">
            🔍
        </button>
        
        <!-- Theme Toggle (Dark/Light Mode) -->
        <button class="ds-header__icon-btn ds-header__theme-toggle" 
                id="themeToggle"
                onclick="toggleTheme()"
                aria-label="Cambiar tema"
                title="Cambiar a modo oscuro">
            🌙
        </button>
        
        <!-- Notificaciones -->
        <div class="ds-notifications" id="notifications">
            <button class="ds-header__icon-btn" onclick="toggleNotifications()" aria-label="Notificaciones">
                🔔
                <span class="ds-header__badge ds-d-none" id="notificationCount">0</span>
            </button>
            
            <div class="ds-notifications__menu">
                <div class="ds-notifications__header">
                    <h4 class="ds-notifications__title">Notificaciones</h4>
                    <button class="ds-notifications__mark-read" onclick="markAllRead()">Marcar leídas</button>
                </div>
                <div class="ds-notifications__body" id="notificationsList">
                    <div class="ds-notifications__empty ds-p-4 ds-text-center ds-text-gray-500">
                        No hay notificaciones nuevas
                    </div>
                </div>
                <div class="ds-notifications__footer">
                    <a href="<?= base_url('/notificaciones') ?>" class="ds-notifications__view-all">Ver todas</a>
                </div>
            </div>
        </div>
        
        <div class="ds-header__divider"></div>
        
        <!-- Menú de Usuario -->
        <div class="ds-dropdown ds-header__user" id="userMenu">
            <button class="ds-header__user-btn" onclick="toggleUserMenu()">
                <div class="ds-header__user-avatar">
                    <?= $headerIniciales ?>
                </div>
                <div class="ds-header__user-info">
                    <div class="ds-header__user-name"><?= esc($headerUserName) ?></div>
                    <div class="ds-header__user-role"><?= esc($headerUserRole) ?></div>
                </div>
                <span class="ds-header__user-arrow">▼</span>
            </button>
            
            <div class="ds-dropdown__menu">
                <div class="ds-dropdown__header">
                    <div class="ds-dropdown__header-name"><?= esc($headerUserName . ' ' . $headerUserApellido) ?></div>
                    <div class="ds-dropdown__header-email"><?= esc($headerUserEmail) ?></div>
                </div>
                <div class="ds-dropdown__body">
                    <a href="<?= base_url('/perfil') ?>" class="ds-dropdown__item">
                        <span class="ds-dropdown__item-icon">👤</span>
                        Mi Perfil
                    </a>
                    <a href="<?= base_url('/configuracion') ?>" class="ds-dropdown__item">
                        <span class="ds-dropdown__item-icon">⚙️</span>
                        Configuración
                    </a>
                    <div class="ds-dropdown__divider"></div>
                    <a href="<?= base_url('/ayuda') ?>" class="ds-dropdown__item">
                        <span class="ds-dropdown__item-icon">❓</span>
                        Ayuda
                    </a>
                    <div class="ds-dropdown__divider"></div>
                    <a href="<?= base_url('/logout') ?>" class="ds-dropdown__item ds-dropdown__item--danger">
                        <span class="ds-dropdown__item-icon">🚪</span>
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Búsqueda Móvil Overlay -->
    <div class="ds-header__mobile-search" id="mobileSearch">
        <div class="ds-header__mobile-search-inner">
            <button class="ds-header__mobile-search-back" onclick="toggleMobileSearch()" aria-label="Volver">←</button>
            <div class="ds-header__mobile-search-input-wrapper">
                <span class="ds-header__search-icon">🔍</span>
                <input type="text" 
                       class="ds-header__mobile-search-input" 
                       placeholder="Buscar pacientes, citas..." 
                       id="mobileSearchInput">
            </div>
            <button class="ds-header__mobile-search-clear" onclick="clearMobileSearch()" aria-label="Limpiar">×</button>
        </div>
    </div>
</header>

<script>
// Estado de menús
let mobileMenuOpen = false;
let mobileSearchOpen = false;

// Toggle menú móvil
function toggleMobileMenu() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const menuToggle = document.querySelector('.ds-header__menu-toggle');
    
    if (!sidebar || !backdrop) return;
    
    mobileMenuOpen = !mobileMenuOpen;
    
    if (mobileMenuOpen) {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-active');
        menuToggle?.classList.add('is-active');
        menuToggle?.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-active');
        menuToggle?.classList.remove('is-active');
        menuToggle?.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }
}

// Toggle búsqueda móvil
function toggleMobileSearch() {
    const mobileSearch = document.getElementById('mobileSearch');
    const searchInput = document.getElementById('mobileSearchInput');
    
    if (!mobileSearch) return;
    
    mobileSearchOpen = !mobileSearchOpen;
    
    if (mobileSearchOpen) {
        mobileSearch.classList.add('is-active');
        setTimeout(() => searchInput?.focus(), 100);
        document.body.style.overflow = 'hidden';
    } else {
        mobileSearch.classList.remove('is-active');
        document.body.style.overflow = '';
    }
}

// Toggle notificaciones
function toggleNotifications() {
    const notifications = document.getElementById('notifications');
    const userMenu = document.getElementById('userMenu');
    
    // Cerrar menú usuario si está abierto
    userMenu?.classList.remove('is-open');
    
    notifications?.classList.toggle('is-open');
}

// Toggle menú usuario
function toggleUserMenu() {
    const userMenu = document.getElementById('userMenu');
    const notifications = document.getElementById('notifications');
    
    // Cerrar notificaciones si están abiertas
    notifications?.classList.remove('is-open');
    
    userMenu?.classList.toggle('is-open');
}

// Marcar notificaciones como leídas
function markAllRead() {
    const badge = document.getElementById('notificationCount');
    if (badge) {
        badge.style.display = 'none';
        badge.textContent = '0';
    }
}

// Limpiar búsqueda
function clearSearch() {
    const input = document.getElementById('globalSearch');
    if (input) {
        input.value = '';
        input.focus();
    }
}

function clearMobileSearch() {
    const input = document.getElementById('mobileSearchInput');
    if (input) {
        input.value = '';
        input.focus();
    }
}

// Cerrar sidebar
function closeSidebar() {
    if (mobileMenuOpen) {
        toggleMobileMenu();
    }
}

// Cerrar dropdowns al hacer click fuera
document.addEventListener('click', function(event) {
    const notifications = document.getElementById('notifications');
    const userMenu = document.getElementById('userMenu');
    
    // Cerrar notificaciones si click fuera
    if (notifications && !notifications.contains(event.target)) {
        notifications.classList.remove('is-open');
    }
    
    // Cerrar menú usuario si click fuera
    if (userMenu && !userMenu.contains(event.target)) {
        userMenu.classList.remove('is-open');
    }
});

// Cerrar con ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if (mobileMenuOpen) closeSidebar();
        if (mobileSearchOpen) toggleMobileSearch();
        
        document.getElementById('notifications')?.classList.remove('is-open');
        document.getElementById('userMenu')?.classList.remove('is-open');
    }
});

// Cerrar menús móviles al redimensionar a desktop
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        if (mobileMenuOpen) closeSidebar();
        if (mobileSearchOpen) toggleMobileSearch();
    }
});
</script>
