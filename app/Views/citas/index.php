<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">📅 Citas</h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/citas/nueva'); ?>" class="ds-btn ds-btn--primary">
                <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                Nueva Cita
            </a>
            <a href="<?= base_url('/citas/calendario'); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">📅</span>
                Ver Calendario
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert--success">
            <span class="ds-alert__icon">✅</span>
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert--danger">
            <span class="ds-alert__icon">❌</span>
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- Estadísticas del día -->
    <?php if (!empty($estadisticas)): ?>
    <div class="ds-grid ds-grid--4 ds-mb-4">
        <div class="ds-card ds-card--stat">
            <div class="ds-card__body">
                <div class="ds-stat__icon">📋</div>
                <div class="ds-stat__number"><?= $estadisticas['total'] ?? 0; ?></div>
                <div class="ds-stat__label">Total Hoy</div>
            </div>
        </div>
        <div class="ds-card ds-card--stat ds-card--success">
            <div class="ds-card__body">
                <div class="ds-stat__icon">✓</div>
                <div class="ds-stat__number"><?= $estadisticas['confirmadas'] ?? 0; ?></div>
                <div class="ds-stat__label">Confirmadas</div>
            </div>
        </div>
        <div class="ds-card ds-card--stat ds-card--warning">
            <div class="ds-card__body">
                <div class="ds-stat__icon">⏳</div>
                <div class="ds-stat__number"><?= $estadisticas['pendientes'] ?? 0; ?></div>
                <div class="ds-stat__label">Pendientes</div>
            </div>
        </div>
        <div class="ds-card ds-card--stat ds-card--info">
            <div class="ds-card__body">
                <div class="ds-stat__icon">✅</div>
                <div class="ds-stat__number"><?= $estadisticas['completadas'] ?? 0; ?></div>
                <div class="ds-stat__label">Completadas</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="ds-card ds-mb-4">
        <div class="ds-card__header">
            <h3 class="ds-card__title">🔍 Filtros</h3>
        </div>
        <div class="ds-card__body">
            <form method="GET" action="<?= base_url('/citas'); ?>" class="ds-form-row">
                <div class="ds-form-group">
                    <label class="ds-label">Fecha</label>
                    <input type="date" class="ds-input" name="fecha" value="<?= esc($filtros['fecha'] ?? date('Y-m-d')); ?>">
                </div>
                <div class="ds-form-group">
                    <label class="ds-label">Estado</label>
                    <select class="ds-input" name="estado">
                        <option value="">Todos</option>
                        <option value="programada" <?= ($filtros['estado'] ?? '') == 'programada' ? 'selected' : ''; ?>>Programada</option>
                        <option value="confirmada" <?= ($filtros['estado'] ?? '') == 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                        <option value="en_progreso" <?= ($filtros['estado'] ?? '') == 'en_progreso' ? 'selected' : ''; ?>>En Progreso</option>
                        <option value="completada" <?= ($filtros['estado'] ?? '') == 'completada' ? 'selected' : ''; ?>>Completada</option>
                        <option value="cancelada" <?= ($filtros['estado'] ?? '') == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                </div>
                <div class="ds-form-group">
                    <label class="ds-label">Tipo</label>
                    <select class="ds-input" name="tipo">
                        <option value="">Todos</option>
                        <option value="consulta" <?= ($filtros['tipo'] ?? '') == 'consulta' ? 'selected' : ''; ?>>Consulta</option>
                        <option value="tratamiento" <?= ($filtros['tipo'] ?? '') == 'tratamiento' ? 'selected' : ''; ?>>Tratamiento</option>
                        <option value="revision" <?= ($filtros['tipo'] ?? '') == 'revision' ? 'selected' : ''; ?>>Revisión</option>
                        <option value="urgencia" <?= ($filtros['tipo'] ?? '') == 'urgencia' ? 'selected' : ''; ?>>Urgencia</option>
                    </select>
                </div>
                <div class="ds-form-group">
                    <label class="ds-label">&nbsp;</label>
                    <div class="ds-flex ds-gap-2">
                        <button type="submit" class="ds-btn ds-btn--primary">
                            <span class="ds-btn__icon ds-btn__icon--left">🔍</span>
                            Filtrar
                        </button>
                        <a href="<?= base_url('/citas'); ?>" class="ds-btn ds-btn--secondary">
                            🔄 Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Citas -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h3 class="ds-card__title">
                <?= isset($filtros['fecha']) ? 'Citas del ' . date('d/m/Y', strtotime($filtros['fecha'])) : 'Citas de Hoy'; ?>
            </h3>
        </div>
        <div class="ds-card__body">
            <?php if (empty($citas)): ?>
                <div class="ds-empty-state">
                    <div class="ds-empty-state__icon">📅</div>
                    <h3 class="ds-empty-state__text">No hay citas para mostrar</h3>
                    <p class="ds-text-muted">No se encontraron citas con los filtros seleccionados</p>
                    <div class="ds-empty-state__action">
                        <a href="<?= base_url('/citas/nueva'); ?>" class="ds-btn ds-btn--primary">
                            <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                            Programar Cita
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="ds-table-responsive">
                    <table class="ds-table ds-table--hover ds-table--striped">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Título</th>
                                <th>Paciente</th>
                                <th>Doctor</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th class="ds-text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($citas as $cita): ?>
                            <tr>
                                <td>
                                    <strong><?= $cita['hora'] ?? '--:--'; ?></strong>
                                    <small class="ds-text-muted">- <?= $cita['hora_fin'] ?? '--:--'; ?></small>
                                </td>
                                <td><?= esc($cita['titulo'] ?? ''); ?></td>
                                <td>
                                    <a href="<?= base_url('/pacientes/' . ($cita['id_paciente'] ?? 0)); ?>" class="ds-link">
                                        <?= esc(trim(($cita['paciente_nombre'] ?? '') . ' ' . ($cita['paciente_apellido'] ?? ''))); ?>
                                    </a>
                                </td>
                                <td><?= esc($cita['doctor_nombre'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                    $tipoIcon = match($cita['tipo_cita'] ?? '') {
                                        'consulta' => '🩺',
                                        'tratamiento' => '💊',
                                        'revision' => '🔍',
                                        'urgencia' => '🚨',
                                        default => '📋'
                                    };
                                    ?>
                                    <?= $tipoIcon; ?> <?= ucfirst($cita['tipo_cita'] ?? ''); ?>
                                </td>
                                <td>
                                    <?php 
                                    $badgeClass = match($cita['estado'] ?? '') {
                                        'programada' => 'ds-badge--info',
                                        'confirmada' => 'ds-badge--success',
                                        'en_progreso' => 'ds-badge--warning',
                                        'completada' => 'ds-badge--secondary',
                                        'cancelada' => 'ds-badge--danger',
                                        default => 'ds-badge--secondary'
                                    };
                                    ?>
                                    <span class="ds-badge <?= $badgeClass; ?>"><?= ucfirst(str_replace('_', ' ', $cita['estado'] ?? '')); ?></span>
                                </td>
                                <td class="ds-table__actions">
                                    <div class="ds-btn-group">
                                        <a href="<?= base_url('/citas/' . ($cita['id'] ?? 0)); ?>" 
                                           class="ds-btn ds-btn--sm ds-btn--info" title="Ver detalles">👁️</a>
                                        
                                        <?php if (($cita['estado'] ?? '') !== 'completada' && ($cita['estado'] ?? '') !== 'cancelada'): ?>
                                            <a href="<?= base_url('/citas/' . ($cita['id'] ?? 0) . '/editar'); ?>" 
                                               class="ds-btn ds-btn--sm ds-btn--warning" title="Editar">✏️</a>
                                            
                                            <?php if (($cita['estado'] ?? '') === 'programada'): ?>
                                                <form action="<?= base_url('/citas/' . ($cita['id'] ?? 0) . '/cambiar-estado'); ?>" 
                                                      method="POST" style="display: inline;">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="estado" value="confirmada">
                                                    <button type="submit" class="ds-btn ds-btn--sm ds-btn--success" 
                                                            title="Confirmar cita">✓</button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <button type="button" 
                                                    class="ds-btn ds-btn--sm ds-btn--danger" 
                                                    onclick="cancelarCita(<?= $cita['id'] ?? 0; ?>)"
                                                    title="Cancelar cita">❌</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Cancelación -->
<div class="ds-modal-overlay" id="modalCancelar">
    <div class="ds-modal">
        <div class="ds-modal__header">
            <h3 class="ds-modal__title">❌ Cancelar Cita</h3>
            <button class="ds-modal__close" type="button" onclick="cerrarModal()">×</button>
        </div>
        <div class="ds-modal__body">
            <p>¿Está seguro de que desea cancelar esta cita?</p>
            <p class="ds-text-muted">Esta acción marcará la cita como cancelada pero no la eliminará del sistema.</p>
        </div>
        <div class="ds-modal__footer">
            <form id="formCancelar" method="POST">
                <?= csrf_field(); ?>
                <input type="hidden" name="estado" value="cancelada">
                <button type="button" class="ds-btn ds-btn--secondary" onclick="cerrarModal()">No, volver</button>
                <button type="submit" class="ds-btn ds-btn--danger">Sí, cancelar cita</button>
            </form>
        </div>
    </div>
</div>

<style>
/* Tarjetas de estadísticas */
.ds-card--stat {
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
}

.ds-card--stat:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.ds-stat__icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.ds-stat__number {
    font-size: 1.75rem;
    font-weight: bold;
    color: var(--ds-text-primary);
}

.ds-stat__label {
    font-size: 0.875rem;
    color: var(--ds-text-secondary);
}

.ds-card--success .ds-stat__number { color: var(--ds-success); }
.ds-card--warning .ds-stat__number { color: var(--ds-warning); }
.ds-card--info .ds-stat__number { color: var(--ds-info); }

/* Tabla mejorada */
.ds-table-responsive {
    overflow-x: auto;
    border-radius: 8px;
}

.ds-table {
    border-collapse: collapse;
    width: 100%;
}

.ds-table thead {
    background-color: var(--ds-bg-secondary);
}

.ds-table thead th {
    padding: 1rem;
    font-weight: 600;
    text-align: left;
    border-bottom: 2px solid var(--ds-border-color);
}

.ds-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid var(--ds-border-color);
}

.ds-table tbody tr:hover {
    background-color: var(--ds-bg-secondary);
}

.ds-table__actions {
    text-align: right;
}

.ds-btn-group {
    display: flex;
    gap: 0.25rem;
    justify-content: flex-end;
}

.ds-btn--sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
}

/* Estilos de links en tabla */
.ds-link {
    color: var(--ds-primary);
    text-decoration: none;
    transition: color 0.2s;
}

.ds-link:hover {
    color: var(--ds-primary-dark);
    text-decoration: underline;
}

/* Estado de la tabla vacía */
.ds-empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.ds-empty-state__icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.ds-empty-state__text {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.ds-empty-state__action {
    margin-top: 1.5rem;
}

/* Modal de cancelación */
.ds-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s;
}

.ds-modal-overlay.is-active {
    opacity: 1;
    visibility: visible;
}

.ds-modal {
    background: var(--ds-white);
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.ds-modal__header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--ds-border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ds-modal__title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.ds-modal__close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--ds-text-secondary);
    transition: color 0.2s;
}

.ds-modal__close:hover {
    color: var(--ds-text-primary);
}

.ds-modal__body {
    padding: 1.5rem;
}

.ds-modal__footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--ds-border-color);
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.ds-modal__footer button {
    flex: 1;
}

@media (max-width: 768px) {
    .ds-table-responsive {
        overflow-x: auto;
    }
    
    .ds-btn-group {
        flex-wrap: wrap;
    }
}
</style>

<script>
function cancelarCita(id) {
    document.getElementById('formCancelar').action = '<?= base_url('/citas/'); ?>' + id + '/cambiar-estado';
    const modal = document.getElementById('modalCancelar');
    modal.classList.add('is-active');
}

function cerrarModal() {
    const modal = document.getElementById('modalCancelar');
    modal.classList.remove('is-active');
}

// Cerrar modal al hacer clic en el overlay (fuera del modal)
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalCancelar');
    if (e.target === modal) {
        cerrarModal();
    }
});
</script>
<?= $this->endSection(); ?>
