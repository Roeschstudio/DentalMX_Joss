/**
 * Design System JavaScript
 * Dental MX
 */

// ============================================
// SIDEBAR
// ============================================

function toggleSidebarCollapse() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('is-collapsed');
    
    // Guardar preferencia
    localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('is-collapsed'));
}

function toggleSidebarMobile() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    sidebar.classList.toggle('is-open');
    backdrop.classList.toggle('is-active');
    document.body.style.overflow = sidebar.classList.contains('is-open') ? 'hidden' : '';
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    sidebar.classList.remove('is-open');
    backdrop.classList.remove('is-active');
    document.body.style.overflow = '';
}

function toggleSubmenu(event) {
    event.preventDefault();
    const item = event.currentTarget.parentElement;
    item.classList.toggle('is-open');
}

// Restaurar estado del sidebar
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar && localStorage.getItem('sidebar-collapsed') === 'true') {
        sidebar.classList.add('is-collapsed');
    }
});

// ============================================
// DROPDOWNS
// ============================================

function toggleNotifications() {
    const notifications = document.getElementById('notifications');
    const userMenu = document.getElementById('userMenu');
    notifications.classList.toggle('is-open');
    userMenu.classList.remove('is-open');
}

function toggleUserMenu() {
    const userMenu = document.getElementById('userMenu');
    const notifications = document.getElementById('notifications');
    userMenu.classList.toggle('is-open');
    notifications.classList.remove('is-open');
}

// Cerrar dropdowns al hacer click fuera
document.addEventListener('click', function(e) {
    const notifications = document.getElementById('notifications');
    const userMenu = document.getElementById('userMenu');
    
    if (notifications && !notifications.contains(e.target)) {
        notifications.classList.remove('is-open');
    }
    if (userMenu && !userMenu.contains(e.target)) {
        userMenu.classList.remove('is-open');
    }
});

// ============================================
// MODALES
// ============================================

function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('is-active');
        document.body.style.overflow = '';
    }
}

function closeModalOnOverlayClick(event, id) {
    if (event.target === event.currentTarget) {
        closeModal(id);
    }
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.ds-modal-overlay.is-active, .ds-drawer-overlay.is-active').forEach(modal => {
            modal.classList.remove('is-active');
        });
        document.body.style.overflow = '';
    }
});

// ============================================
// TOASTS
// ============================================

function showToast(type, title, message, duration = 5000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const icons = {
        success: '✅',
        warning: '⚠️',
        danger: '❌',
        info: 'ℹ️'
    };
    
    const toast = document.createElement('div');
    toast.className = `ds-toast ds-toast--${type}`;
    toast.innerHTML = `
        <span class="ds-toast__icon">${icons[type] || 'ℹ️'}</span>
        <div class="ds-toast__content">
            <div class="ds-toast__title">${title}</div>
            <p class="ds-toast__message">${message}</p>
        </div>
        <button class="ds-toast__close" onclick="removeToast(this.parentElement)">×</button>
    `;
    
    container.appendChild(toast);
    
    // Auto-remove
    setTimeout(() => removeToast(toast), duration);
}

function removeToast(toast) {
    toast.classList.add('is-hiding');
    setTimeout(() => toast.remove(), 200);
}

// ============================================
// DARK MODE / THEME TOGGLE
// ============================================

function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    const toggleBtn = document.getElementById('themeToggle');
    
    // Agregar animación al botón
    if (toggleBtn) {
        toggleBtn.classList.add('is-animating');
        setTimeout(() => toggleBtn.classList.remove('is-animating'), 500);
    }
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    // Actualizar icono del toggle
    updateThemeIcon(newTheme);
    
    // Actualizar meta theme-color
    updateMetaThemeColor(newTheme);
    
    // Mostrar toast
    showToast('info', 'Tema', `Modo ${newTheme === 'dark' ? 'oscuro' : 'claro'} activado`);
}

function updateThemeIcon(theme) {
    const toggleBtn = document.getElementById('themeToggle');
    if (toggleBtn) {
        toggleBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
        toggleBtn.title = theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
        toggleBtn.setAttribute('aria-label', theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
    }
}

function updateMetaThemeColor(theme) {
    const metaTheme = document.querySelector('meta[name="theme-color"]');
    if (metaTheme) {
        metaTheme.content = theme === 'dark' ? '#0d1117' : '#5ccdde';
    }
}

function initTheme() {
    const html = document.documentElement;
    
    // Obtener preferencia del usuario desde el servidor (data-user-theme-pref)
    const userThemePref = html.getAttribute('data-user-theme-pref') || 'light';
    
    // Verificar preferencia guardada en localStorage
    const savedTheme = localStorage.getItem('theme');
    
    // Verificar preferencia del sistema
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Determinar el tema a aplicar
    let theme;
    if (savedTheme) {
        // Si hay un tema guardado en localStorage, usarlo
        theme = savedTheme;
    } else if (userThemePref === 'auto') {
        // Si la preferencia del usuario es 'auto', usar preferencia del sistema
        theme = prefersDark ? 'dark' : 'light';
    } else {
        // Usar la preferencia del usuario desde la base de datos
        theme = userThemePref;
    }
    
    // Aplicar tema
    html.setAttribute('data-theme', theme);
    updateThemeIcon(theme);
    updateMetaThemeColor(theme);
    
    // Escuchar cambios en preferencia del sistema (solo si userThemePref es 'auto')
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme') && userThemePref === 'auto') {
            const newTheme = e.matches ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            updateThemeIcon(newTheme);
            updateMetaThemeColor(newTheme);
        }
    });
}

// Inicializar tema al cargar
document.addEventListener('DOMContentLoaded', initTheme);

// ============================================
// RESPONSIVE
// ============================================

window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        closeSidebar();
    }
});

// ============================================
// UTILIDADES
// ============================================

// Marcar notificaciones como leídas
function markAllRead() {
    // Implementar lógica AJAX
    showToast('success', 'Notificaciones', 'Todas las notificaciones fueron marcadas como leídas');
}

// Búsqueda global (placeholder)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('globalSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = '/buscar?q=' + encodeURIComponent(query);
                }
            }
        });
    }
});

// ============================================
// ALERTS
// ============================================

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.ds-alert');
    alerts.forEach((alert, index) => {
        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000 + (index * 500)); // Stagger the animations
    });
});

// ============================================
// FORMS
// ============================================

// Auto-focus first input in modals
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.ds-modal-overlay');
    modals.forEach(modal => {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(mutation => {
                if (mutation.target.classList.contains('is-active')) {
                    const firstInput = modal.querySelector('input, textarea, select');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), 300);
                    }
                }
            });
        });
        
        observer.observe(modal, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
});

// ============================================
// PROGRESS HELPERS
// ============================================

function setProgressWidth(bar, value) {
    const v = Math.max(0, Math.min(100, Number(value)));
    bar.style.width = v + '%';
    bar.setAttribute('aria-valuenow', String(v));
}

function initProgressBars() {
    // Pattern A: .ds-progress[data-progress]
    document.querySelectorAll('.ds-progress[data-progress]').forEach(progress => {
        const bar = progress.querySelector('.ds-progress__bar');
        if (bar) {
            setProgressWidth(bar, progress.getAttribute('data-progress'));
        }
    });

    // Pattern B: .ds-progress__bar[data-progress]
    document.querySelectorAll('.ds-progress__bar[data-progress]').forEach(bar => {
        setProgressWidth(bar, bar.getAttribute('data-progress'));
    });
}

document.addEventListener('DOMContentLoaded', initProgressBars);

// Form validation visual feedback
function validateFormField(input) {
    const isValid = input.checkValidity();
    
    if (isValid) {
        input.classList.remove('ds-input--error');
        input.classList.add('ds-input--success');
        const errorMsg = input.parentElement.querySelector('.ds-form-error');
        if (errorMsg) errorMsg.remove();
    } else {
        input.classList.remove('ds-input--success');
        input.classList.add('ds-input--error');
        
        // Mostrar mensaje de error
        let errorMsg = input.parentElement.querySelector('.ds-form-error');
        if (!errorMsg) {
            errorMsg = document.createElement('span');
            errorMsg.className = 'ds-form-error';
            input.parentElement.appendChild(errorMsg);
        }
        errorMsg.textContent = input.validationMessage;
    }
    
    return isValid;
}

// ============================================
// TABLES
// ============================================

// Table row click to select
document.addEventListener('click', function(e) {
    const row = e.target.closest('.ds-table tbody tr');
    if (row && !e.target.closest('a, button, input')) {
        // Toggle selection
        row.classList.toggle('is-selected');
        
        // Emit custom event for other handlers
        const event = new CustomEvent('tableRowSelected', {
            detail: { row, selected: row.classList.contains('is-selected') }
        });
        document.dispatchEvent(event);
    }
});

// Table sorting
function sortTable(tableId, columnIndex, type = 'string') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const header = table.querySelector(`th:nth-child(${columnIndex + 1})`);
    
    // Determinar dirección
    const isAsc = header.classList.contains('ds-table__sortable--asc');
    
    // Limpiar estados de otros headers
    table.querySelectorAll('th').forEach(th => {
        th.classList.remove('ds-table__sortable--asc', 'ds-table__sortable--desc');
    });
    
    // Ordenar
    rows.sort((a, b) => {
        let aVal = a.cells[columnIndex].textContent.trim();
        let bVal = b.cells[columnIndex].textContent.trim();
        
        if (type === 'number') {
            aVal = parseFloat(aVal) || 0;
            bVal = parseFloat(bVal) || 0;
        } else if (type === 'date') {
            aVal = new Date(aVal);
            bVal = new Date(bVal);
        }
        
        if (aVal < bVal) return isAsc ? 1 : -1;
        if (aVal > bVal) return isAsc ? -1 : 1;
        return 0;
    });
    
    // Actualizar header
    header.classList.add(isAsc ? 'ds-table__sortable--desc' : 'ds-table__sortable--asc');
    
    // Reordenar filas
    rows.forEach(row => tbody.appendChild(row));
}

// ============================================
// CARDS
// ============================================

// Card click effects
document.addEventListener('click', function(e) {
    const card = e.target.closest('.ds-card--clickable');
    if (card) {
        // Add ripple effect
        const ripple = document.createElement('div');
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.backgroundColor = 'rgba(92, 205, 222, 0.3)';
        ripple.style.width = ripple.style.height = '40px';
        ripple.style.left = (e.clientX - card.offsetLeft - 20) + 'px';
        ripple.style.top = (e.clientY - card.offsetTop - 20) + 'px';
        ripple.style.animation = 'ripple 0.6s ease-out';
        ripple.style.pointerEvents = 'none';
        
        card.style.position = 'relative';
        card.style.overflow = 'hidden';
        card.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    }
});

// Add ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ============================================
// COPY TO CLIPBOARD
// ============================================

function copyToClipboard(text, message) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('success', 'Copiado', message || 'Texto copiado al portapapeles');
    }).catch(err => {
        showToast('danger', 'Error', 'No se pudo copiar al portapapeles');
        console.error('Copy failed:', err);
    });
}

// ============================================
// CONFIRM DIALOGS (sin modal_templates.php)
// ============================================

function confirm(message, callback) {
    if (window.confirm(message)) {
        if (typeof callback === 'function') {
            callback();
        }
        return true;
    }
    return false;
}

// ============================================
// LOADING STATES
// ============================================

function showButtonLoading(button) {
    button.classList.add('ds-btn--loading');
    button.disabled = true;
    button.dataset.originalText = button.textContent;
}

function hideButtonLoading(button) {
    button.classList.remove('ds-btn--loading');
    button.disabled = false;
    if (button.dataset.originalText) {
        button.textContent = button.dataset.originalText;
    }
}

// ============================================
// AJAX HELPERS
// ============================================

async function fetchJSON(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const response = await fetch(url, { ...defaultOptions, ...options });
    return response.json();
}

async function postJSON(url, data) {
    return fetchJSON(url, {
        method: 'POST',
        body: JSON.stringify(data)
    });
}

// ============================================
// DEBOUNCE / THROTTLE
// ============================================

function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit = 300) {
    let inThrottle;
    return function executedFunction(...args) {
        if (!inThrottle) {
            func(...args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}
