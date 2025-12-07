<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('styles') ?>
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--space-6);
        margin-bottom: var(--space-8);
    }
    
    .demo-card {
        background: linear-gradient(135deg, var(--color-primary), var(--color-info));
        color: white;
        padding: var(--space-6);
        border-radius: var(--radius-lg);
        text-align: center;
        transition: transform var(--transition-fast);
    }
    
    .demo-card:hover {
        transform: translateY(-4px);
    }
    
    .demo-card h3 {
        margin: 0 0 var(--space-2) 0;
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
    }
    
    .demo-card p {
        margin: 0;
        opacity: 0.9;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ds-page-header">
    <div>
        <h1 class="ds-page-title">Dashboard de Ejemplo</h1>
        <p class="ds-page-subtitle">Vista demostrativa del nuevo Design System Layout</p>
    </div>
    <div class="ds-page-actions">
        <button class="ds-btn ds-btn--primary" onclick="showToast('success', 'Bienvenido', 'Esta es una notificación de ejemplo')">
            <span>🎉</span>
            Mostrar Toast
        </button>
        <button class="ds-btn ds-btn--secondary" onclick="openModal('demoModal')">
            <span>📋</span>
            Abrir Modal
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="ds-card ds-card--elevated demo-card">
        <h3>👥</h3>
        <p>Pacientes Activos</p>
    </div>
    <div class="ds-card ds-card--elevated demo-card">
        <h3>📅</h3>
        <p>Citas Hoy</p>
    </div>
    <div class="ds-card ds-card--elevated demo-card">
        <h3>💊</h3>
        <p>Medicamentos</p>
    </div>
    <div class="ds-card ds-card--elevated demo-card">
        <h3>📋</h3>
        <p>Recetas</p>
    </div>
</div>

<!-- Content Grid -->
<div class="ds-grid ds-grid--3">
    <!-- Recent Patients -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h3 class="ds-card__title">Pacientes Recientes</h3>
            <a href="#" class="ds-card__link">Ver todos</a>
        </div>
        <div class="ds-card__body">
            <div class="ds-table ds-table--compact">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Última visita</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>María García</td>
                            <td>55 1234 5678</td>
                            <td>Hace 2 días</td>
                        </tr>
                        <tr>
                            <td>Juan Pérez</td>
                            <td>55 8765 4321</td>
                            <td>Hace 5 días</td>
                        </tr>
                        <tr>
                            <td>Ana Rodríguez</td>
                            <td>55 2468 1357</td>
                            <td>Hace 1 semana</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h3 class="ds-card__title">Acciones Rápidas</h3>
        </div>
        <div class="ds-card__body">
            <div class="ds-flex ds-flex-col ds-gap-3">
                <button class="ds-btn ds-btn--primary ds-btn--wide" onclick="showToast('info', 'Nueva Cita', 'Formulario de cita abierto')">
                    📅 Agendar Cita
                </button>
                <button class="ds-btn ds-btn--success ds-btn--wide" onclick="showToast('success', 'Nuevo Paciente', 'Formulario de paciente abierto')">
                    👥 Registrar Paciente
                </button>
                <button class="ds-btn ds-btn--info ds-btn--wide" onclick="showToast('info', 'Nueva Receta', 'Formulario de receta abierto')">
                    📋 Crear Receta
                </button>
                <button class="ds-btn ds-btn--warning ds-btn--wide" onclick="showToast('warning', 'Inventario', 'Revisando stock de medicamentos')">
                    💊 Ver Inventario
                </button>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h3 class="ds-card__title">Actividad Reciente</h3>
        </div>
        <div class="ds-card__body">
            <div class="ds-flex ds-flex-col ds-gap-4">
                <div class="ds-alert ds-alert--success ds-alert--inline">
                    <span class="ds-alert__icon">✅</span>
                    <div class="ds-alert__content">
                        <p class="ds-alert__text">Nueva cita agendada para María García</p>
                    </div>
                </div>
                
                <div class="ds-alert ds-alert--info ds-alert--inline">
                    <span class="ds-alert__icon">ℹ️</span>
                    <div class="ds-alert__content">
                        <p class="ds-alert__text">Stock bajo de Anestesia Local (10 unidades)</p>
                    </div>
                </div>
                
                <div class="ds-alert ds-alert--warning ds-alert--inline">
                    <span class="ds-alert__icon">⚠️</span>
                    <div class="ds-alert__content">
                        <p class="ds-alert__text">3 pagos pendientes por procesar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<!-- Demo Modal -->
<div class="ds-modal-overlay" id="demoModal" onclick="closeModalOnOverlayClick(event, 'demoModal')">
    <div class="ds-modal ds-modal--md" onclick="event.stopPropagation()">
        <div class="ds-modal__header">
            <h3 class="ds-modal__title">Modal de Ejemplo</h3>
            <button class="ds-modal__close" onclick="closeModalOnOverlayClick(event, 'demoModal')">×</button>
        </div>
        <div class="ds-modal__body">
            <p>Este es un modal de ejemplo que demuestra la integración del Design System con el layout.</p>
            <p>El modal incluye:</p>
            <ul>
                <li>Animaciones suaves de entrada/salida</li>
                <li>Overlay con efecto blur</li>
                <li>Cierre con click fuera o tecla ESC</li>
                <li>Diferentes tamaños disponibles</li>
            </ul>
            
            <div class="ds-form-group">
                <label class="ds-label">Ejemplo de campo</label>
                <input type="text" class="ds-input" placeholder="Escribe algo aquí...">
            </div>
        </div>
        <div class="ds-modal__footer">
            <button class="ds-btn ds-btn--secondary" onclick="closeModalOnOverlayClick(event, 'demoModal')">Cancelar</button>
            <button class="ds-btn ds-btn--primary" onclick="showToast('success', 'Guardado', 'Los datos fueron guardados correctamente'); closeModal('demoModal');">Guardar</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Demostrar notificaciones al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            showToast('info', 'Bienvenido', 'Esta es una demostración del Design System');
        }, 1000);
        
        // Simular contador de notificaciones
        const notificationCount = document.getElementById('notificationCount');
        if (notificationCount) {
            notificationCount.textContent = '3';
            notificationCount.classList.remove('ds-d-none');
        }
    });
</script>
<?= $this->endSection() ?>
