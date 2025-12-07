<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">Ficha del Paciente</h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/pacientes'); ?>" class="ds-btn ds-btn--outline">
                ⬅️ Volver al Listado
            </a>
            <a href="<?= base_url('/pacientes/' . $patient['id'] . '/pdf'); ?>" class="ds-btn ds-btn--secondary" target="_blank">
                📄 Imprimir PDF
            </a>
            <a href="<?= base_url('/pacientes/' . $patient['id'] . '/editar'); ?>" class="ds-btn ds-btn--warning">
                ✏️ Editar
            </a>
        </div>
    </div>

    <!-- Mensajes de sesión -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert--success ds-alert--dismissible">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="ds-alert__close" data-dismiss="alert" aria-label="Close">
                ✕
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert--error ds-alert--dismissible">
            <?= session()->getFlashdata('error'); ?>
            <button type="button" class="ds-alert__close" data-dismiss="alert" aria-label="Close">
                ✕
            </button>
        </div>
    <?php endif; ?>

    <!-- Información Personal -->
    <div class="ds-grid ds-grid--3">
        <div class="ds-grid__col--lg-4">
            <div class="ds-card">
                <div class="ds-card__header ds-card__header--with-actions">
                    <h2 class="ds-card__title">Información Personal</h2>
                    <div class="ds-dropdown">
                        <button class="ds-dropdown__toggle" type="button" id="patientDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            ⋮
                        </button>
                        <div class="ds-dropdown__menu" aria-labelledby="patientDropdown">
                            <a class="ds-dropdown__item" href="<?= base_url('/pacientes/' . $patient['id'] . '/editar'); ?>">
                                ✏️ Editar Datos
                            </a>
                            <div class="ds-dropdown__divider"></div>
                            <a class="ds-dropdown__item ds-dropdown__item--danger" href="#" onclick="confirmDelete(<?= $patient['id']; ?>)">
                                🗑️ Eliminar Paciente
                            </a>
                        </div>
                    </div>
                </div>
                <div class="ds-card__body">
                    <div class="ds-avatar ds-avatar--large ds-avatar--centered mb-4">
                        <img src="<?= base_url('public/assets/dashboard/img/avatar9.jpg'); ?>" alt="Avatar" class="ds-avatar__img">
                    </div>
                    
                    <div class="ds-info-list">
                        <div class="ds-info-list__item">
                            <span class="ds-info-list__label">ID:</span>
                            <span class="ds-info-list__value"><?= $patient['id']; ?></span>
                        </div>
                        
                        <div class="ds-info-list__item">
                            <span class="ds-info-list__label">Nombre:</span>
                            <span class="ds-info-list__value"><?= esc($patient['nombre'] . ' ' . $patient['primer_apellido'] . ' ' . $patient['segundo_apellido']); ?></span>
                        </div>
                        
                        <div class="ds-info-list__item">
                            <span class="ds-info-list__label">Email:</span>
                            <span class="ds-info-list__value"><?= esc($patient['email'] ?? '-'); ?></span>
                        </div>
                        
                        <div class="ds-info-list__item">
                            <span class="ds-info-list__label">Teléfono:</span>
                            <span class="ds-info-list__value"><?= esc($patient['telefono'] ?? '-'); ?></span>
                        </div>
                        
                        <div class="ds-info-list__item">
                            <span class="ds-info-list__label">Nacionalidad:</span>
                            <span class="ds-info-list__value"><?= esc($patient['nacionalidad'] ?? '-'); ?></span>
                        </div>
                        
                        <div class="ds-info-list__item">
                            <span class="ds-info-list__label">Fecha Nac.:</span>
                            <span class="ds-info-list__value">
                                <?php
                                if ($patient['fecha_nacimiento']) {
                                    $fecha = new DateTime($patient['fecha_nacimiento']);
                                    $hoy = new DateTime();
                                    $edad = $hoy->diff($fecha)->y;
                                    echo $fecha->format('d/m/Y') . ' (' . $edad . ' años)';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </span>
                        </div>
                        
                        <!-- Campo sexo no existe en tabla pacientes, omitido -->
                        
                        <div class="ds-info-list__item">
                            <span class="ds-info-list__label">Domicilio:</span>
                            <span class="ds-info-list__value"><?= esc($patient['domicilio'] ?? '-'); ?></span>
                        </div>
                        
                        <!-- Campo estado no existe en tabla pacientes, mostrar estado de soft delete -->
                        <div class="ds-info-list__item">
                            <span class="ds-info-list__label">Estado:</span>
                            <span class="ds-info-list__value">
                                <?php if (is_null($patient['deleted_at'])): ?>
                                    <span class="ds-badge ds-badge--success">Activo</span>
                                <?php else: ?>
                                    <span class="ds-badge ds-badge--secondary">Eliminado</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="ds-info-list__item">
                            <span class="ds-info-list__label">Registrado:</span>
                            <span class="ds-info-list__value">
                                <?php
                                $fechaRegistro = new DateTime($patient['created_at']);
                                echo $fechaRegistro->format('d/m/Y H:i');
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha - Secciones de Historial -->
        <div class="ds-grid__col--lg-8">
            <!-- Acciones Rápidas -->
            <div class="ds-card">
                <div class="ds-card__header">
                    <h2 class="ds-card__title">Acciones Rápidas</h2>
                </div>
                <div class="ds-card__body">
                    <div class="ds-grid ds-grid--4 ds-grid--centered">
                        <div class="ds-quick-action">
                            <a href="<?= base_url('/citas/nueva?paciente=' . $patient['id']); ?>" class="ds-quick-action__link">
                                <div class="ds-quick-action__icon ds-quick-action__icon--primary">
                                    📅
                                </div>
                                <span class="ds-quick-action__text">Nueva Cita</span>
                            </a>
                        </div>
                        <div class="ds-quick-action">
                            <a href="<?= base_url('/recetas/crear/' . $patient['id']); ?>" class="ds-quick-action__link">
                                <div class="ds-quick-action__icon ds-quick-action__icon--info">
                                    📋
                                </div>
                                <span class="ds-quick-action__text">Nueva Receta</span>
                            </a>
                        </div>
                        <div class="ds-quick-action">
                            <a href="<?= base_url('/presupuestos/create?paciente=' . $patient['id']); ?>" class="ds-quick-action__link">
                                <div class="ds-quick-action__icon ds-quick-action__icon--warning">
                                    💰
                                </div>
                                <span class="ds-quick-action__text">Nuevo Presupuesto</span>
                            </a>
                        </div>
                        <div class="ds-quick-action">
                            <a href="<?= base_url('/historial/' . $patient['id']); ?>" class="ds-quick-action__link">
                                <div class="ds-quick-action__icon ds-quick-action__icon--success">
                                    🩺
                                </div>
                                <span class="ds-quick-action__text">Historial Clínico</span>
                            </a>
                        </div>
                        <div class="ds-quick-action">
                            <a href="<?= base_url('/odontograma/' . $patient['id']); ?>" class="ds-quick-action__link">
                                <div class="ds-quick-action__icon ds-quick-action__icon--primary">
                                    🦷
                                </div>
                                <span class="ds-quick-action__text">Odontograma</span>
                            </a>
                        </div>
                        <div class="ds-quick-action">
                            <a href="<?= base_url('/cotizaciones/crear/' . $patient['id']); ?>" class="ds-quick-action__link">
                                <div class="ds-quick-action__icon ds-quick-action__icon--secondary">
                                    📊
                                </div>
                                <span class="ds-quick-action__text">Cotización</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de Citas -->
            <div class="ds-card">
                <div class="ds-card__header ds-card__header--with-actions">
                    <h2 class="ds-card__title">Historial de Citas</h2>
                    <a href="#" class="ds-btn ds-btn--sm ds-btn--primary" onclick="showModal('cita')">
                        ➕ Nueva Cita
                    </a>
                </div>
                <div class="ds-card__body">
                    <div class="ds-empty-state">
                        <div class="ds-empty-state__icon">
                            📆
                        </div>
                        <h3 class="ds-empty-state__title">No hay citas registradas</h3>
                        <p class="ds-empty-state__text">Esta funcionalidad estará disponible en próximos pasos.</p>
                    </div>
                </div>
            </div>

            <!-- Recetas Emitidas -->
            <div class="ds-card">
                <div class="ds-card__header ds-card__header--with-actions">
                    <h2 class="ds-card__title">Recetas Emitidas</h2>
                    <a href="#" class="ds-btn ds-btn--sm ds-btn--info" onclick="showModal('receta')">
                        ➕ Nueva Receta
                    </a>
                </div>
                <div class="ds-card__body">
                    <div class="ds-empty-state">
                        <div class="ds-empty-state__icon">
                            📋
                        </div>
                        <h3 class="ds-empty-state__title">No hay recetas registradas</h3>
                        <p class="ds-empty-state__text">Esta funcionalidad estará disponible en próximos pasos.</p>
                    </div>
                </div>
            </div>

            <!-- Presupuestos Ofrecidos -->
            <div class="ds-card">
                <div class="ds-card__header ds-card__header--with-actions">
                    <h2 class="ds-card__title">Presupuestos Ofrecidos</h2>
                    <a href="#" class="ds-btn ds-btn--sm ds-btn--warning" onclick="showModal('presupuesto')">
                        ➕ Nuevo Presupuesto
                    </a>
                </div>
                <div class="ds-card__body">
                    <div class="ds-empty-state">
                        <div class="ds-empty-state__icon">
                            💰
                        </div>
                        <h3 class="ds-empty-state__title">No hay presupuestos registrados</h3>
                        <p class="ds-empty-state__text">Esta funcionalidad estará disponible en próximos pasos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="ds-modal-overlay" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="ds-modal">
        <div class="ds-modal__dialog" role="document">
            <div class="ds-modal__content">
                <div class="ds-modal__header">
                    <h2 class="ds-modal__title" id="deleteModalLabel">Confirmar Eliminación</h2>
                    <button type="button" class="ds-modal__close" onclick="closeDeleteModal()" aria-label="Close">
                        ✕
                    </button>
                </div>
                <div class="ds-modal__body">
                    <p>¿Está seguro de que desea eliminar este paciente? Esta acción no se puede deshacer.</p>
                </div>
                <div class="ds-modal__footer">
                    <button type="button" class="ds-btn ds-btn--outline" onclick="closeDeleteModal()">Cancelar</button>
                    <form id="deleteForm" method="POST" action="" class="ds-inline-form">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="ds-btn ds-btn--danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Información (placeholder para futuras funcionalidades) -->
<div class="ds-modal-overlay" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="ds-modal">
        <div class="ds-modal__dialog" role="document">
            <div class="ds-modal__content">
                <div class="ds-modal__header">
                    <h2 class="ds-modal__title" id="infoModalLabel">Información</h2>
                    <button type="button" class="ds-modal__close" onclick="closeInfoModal()" aria-label="Close">
                        ✕
                    </button>
                </div>
                <div class="ds-modal__body">
                    <p id="modalMessage">Esta funcionalidad estará disponible en próximos pasos.</p>
                </div>
                <div class="ds-modal__footer">
                    <button type="button" class="ds-btn ds-btn--outline" onclick="closeInfoModal()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('deleteForm').setAttribute('action', '<?= base_url('/pacientes/'); ?>' + id + '/eliminar');
    const modal = document.getElementById('deleteModal');
    modal.classList.add('is-active');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('is-active');
}

function showModal(type) {
    let message = '';
    switch(type) {
        case 'cita':
            message = 'La funcionalidad para crear citas estará disponible en próximos pasos.';
            break;
        case 'receta':
            message = 'La funcionalidad para crear recetas estará disponible en próximos pasos.';
            break;
        case 'presupuesto':
            message = 'La funcionalidad para crear presupuestos estará disponible en próximos pasos.';
            break;
        case 'historial':
            message = 'La funcionalidad para historial clínico estará disponible en próximos pasos.';
            break;
        default:
            message = 'Esta funcionalidad estará disponible en próximos pasos.';
    }
    document.getElementById('modalMessage').textContent = message;
    const modal = document.getElementById('infoModal');
    modal.classList.add('is-active');
}

function closeInfoModal() {
    const modal = document.getElementById('infoModal');
    modal.classList.remove('is-active');
}

// Cerrar modal al hacer clic en el overlay (fuera del modal)
document.addEventListener('click', function(e) {
    if (e.target.id === 'deleteModal') {
        closeDeleteModal();
    }
    if (e.target.id === 'infoModal') {
        closeInfoModal();
    }
});

// Handle dropdown toggle
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('ds-dropdown__toggle') || e.target.closest('.ds-dropdown__toggle')) {
        e.preventDefault();
        const dropdown = e.target.closest('.ds-dropdown');
        const menu = dropdown.querySelector('.ds-dropdown__menu');
        
        // Close all other dropdowns
        document.querySelectorAll('.ds-dropdown__menu').forEach(m => {
            if (m !== menu) {
                m.classList.remove('ds-dropdown__menu--show');
            }
        });
        
        menu.classList.toggle('ds-dropdown__menu--show');
    } else if (!e.target.closest('.ds-dropdown')) {
        // Close all dropdowns when clicking outside
        document.querySelectorAll('.ds-dropdown__menu').forEach(menu => {
            menu.classList.remove('ds-dropdown__menu--show');
        });
    }
});
</script>
<?= $this->endSection(); ?>