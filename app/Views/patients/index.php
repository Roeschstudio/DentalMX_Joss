<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<?php
// Debug: Log if pageTitle is set in the view
if (function_exists('log_message')) {
    log_message('debug', 'patients/index view - pageTitle: ' . ($pageTitle ?? 'NOT SET'));
}
?>
<div class="ds-content">
    <!-- Page Header -->
    <div class="ds-page-header">
        <div>
            <h1 class="ds-page-title"><?= esc($pageTitle); ?></h1>
            <p class="ds-page-subtitle">Gestión de pacientes del sistema</p>
        </div>
        <div class="ds-page-actions">
            <a href="<?= base_url('/pacientes/nuevo'); ?>" class="ds-btn ds-btn--primary">
                <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                Nuevo Paciente
            </a>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="ds-card ds-mb-6">
        <div class="ds-card__header">
            <h3 class="ds-card__title">Filtros de Búsqueda</h3>
        </div>
        <div class="ds-card__body">
            <form method="GET" action="<?= base_url('/pacientes'); ?>" id="filterForm">
                <div class="ds-form-row">
                    <div class="ds-form-group">
                        <label for="search" class="ds-label">Buscar</label>
                        <div class="ds-input-wrapper">
                            <span class="ds-input-icon">🔍</span>
                            <input type="text" class="ds-input" id="search" name="search"
                                   value="<?= esc($search ?? ''); ?>"
                                   placeholder="Nombre, Email, Teléfono o Cédula">
                        </div>
                    </div>
                    <div class="ds-form-group">
                        <label for="estado" class="ds-label">Estado</label>
                        <select class="ds-input" id="estado" name="estado">
                            <option value="">Todos</option>
                            <option value="activo" <?= (isset($estado) && $estado == 'activo') ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactivo" <?= (isset($estado) && $estado == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="ds-form-group">
                        <label>&nbsp;</label>
                        <div class="ds-btn-group">
                            <button type="submit" class="ds-btn ds-btn--primary">
                                <span class="ds-btn__icon ds-btn__icon--left">🔍</span>
                                Buscar
                            </button>
                            <a href="<?= base_url('/pacientes'); ?>" class="ds-btn ds-btn--secondary">
                                <span class="ds-btn__icon ds-btn__icon--left">🔄</span>
                                Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Pacientes -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h3 class="ds-card__title">Listado de Pacientes</h3>
        </div>
        <div class="ds-card__body">
            <?php if (empty($patients)): ?>
                <div class="ds-empty-state">
                    <div class="ds-empty-state__icon">👥</div>
                    <h3 class="ds-empty-state__text">No se encontraron pacientes</h3>
                    <p class="ds-text-muted">Crea tu primer paciente para comenzar</p>
                    <div class="ds-empty-state__action">
                        <a href="<?= base_url('/pacientes/nuevo'); ?>" class="ds-btn ds-btn--primary">
                            <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                            Crear Primer Paciente
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="ds-table-responsive">
                    <table class="ds-table ds-table--hover ds-table--striped" id="patientsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th class="ds-text-center">Clínica</th>
                                <th class="ds-text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td><?= $patient['id']; ?></td>
                                    <td>
                                        <a href="<?= base_url('/pacientes/' . $patient['id']); ?>" class="ds-card__link">
                                            <?= esc($patient['nombre'] . ' ' . $patient['primer_apellido'] . ' ' . $patient['segundo_apellido']); ?>
                                        </a>
                                    </td>
                                    <td><?= esc($patient['email'] ?? '-'); ?></td>
                                    <td><?= esc($patient['telefono'] ?? $patient['celular'] ?? '-'); ?></td>
                                    <td>
                                        <span class="ds-badge ds-badge--success">Activo</span>
                                    </td>
                                    <td class="ds-text-center">
                                        <div class="ds-btn-group">
                                            <a href="<?= base_url('/historial/' . $patient['id']); ?>"
                                               class="ds-btn ds-btn--sm ds-btn--success" title="Historial Clínico">
                                                📋
                                            </a>
                                            <a href="<?= base_url('/odontograma/' . $patient['id']); ?>"
                                               class="ds-btn ds-btn--sm ds-btn--primary" title="Odontograma">
                                                🦷
                                            </a>
                                        </div>
                                    </td>
                                    <td class="ds-table__actions">
                                        <a href="<?= base_url('/pacientes/' . $patient['id']); ?>"
                                           class="ds-btn ds-btn--sm ds-btn--info" title="Ver Ficha">
                                            👁️
                                        </a>
                                        <a href="<?= base_url('/pacientes/' . $patient['id'] . '/pdf'); ?>"
                                           class="ds-btn ds-btn--sm ds-btn--secondary" title="Imprimir PDF" target="_blank">
                                            📄
                                        </a>
                                        <a href="<?= base_url('/pacientes/' . $patient['id'] . '/editar'); ?>"
                                           class="ds-btn ds-btn--sm ds-btn--warning" title="Editar">
                                            ✏️
                                        </a>
                                        <button type="button" class="ds-btn ds-btn--sm ds-btn--danger"
                                                onclick="confirmDelete(<?= $patient['id']; ?>)"
                                                title="Eliminar">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="ds-card__footer">
                    <div class="ds-flex ds-items-center ds-justify-between">
                        <div class="ds-text-muted">
                            Mostrando <?= count($patients); ?> de <?= $pager->getTotal(); ?> pacientes
                        </div>
                        <div>
                            <?= $pager->links('default', 'default_full'); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="ds-modal-overlay" id="deleteModal">
    <div class="ds-modal">
        <div class="ds-modal__dialog">
            <div class="ds-modal__content">
                <div class="ds-modal__header">
                    <h2 class="ds-modal__title">Confirmar Eliminación</h2>
                    <button type="button" class="ds-modal__close" onclick="closeDeleteModal()">×</button>
                </div>
                <div class="ds-modal__body">
                    <p>¿Está seguro de que desea eliminar este paciente? Esta acción no se puede deshacer.</p>
                </div>
                <div class="ds-modal__footer">
                    <button type="button" class="ds-btn ds-btn--secondary" onclick="closeDeleteModal()">Cancelar</button>
                    <form id="deleteForm" method="POST" action="" class="ds-d-inline">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="ds-btn ds-btn--danger">
                            <span class="ds-btn__icon ds-btn__icon--left">🗑️</span>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('deleteForm').action = '<?= base_url('/pacientes/'); ?>' + id + '/eliminar';
    document.getElementById('deleteModal').classList.add('is-active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('is-active');
}

// Cerrar modal al hacer clic fuera
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
<?= $this->endSection(); ?>
