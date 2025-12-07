<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">📋 Recetas Médicas</h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/recetas/nueva'); ?>" class="ds-btn ds-btn--primary">
                <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                Nueva Receta
            </a>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert--success ds-mb-4">
            <span class="ds-alert__icon">✓</span>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert--danger ds-mb-4">
            <span class="ds-alert__icon">✗</span>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="ds-card">
        <div class="ds-card__header">
            <h2 class="ds-card__title">Lista de Recetas</h2>
            <span class="ds-badge ds-badge--primary"><?= count($recetas ?? []) ?> recetas</span>
        </div>
        <div class="ds-card__body">
            <?php if (!empty($recetas) && count($recetas) > 0): ?>
                <!-- Tabla de recetas -->
                <div class="ds-table-responsive">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Medicamentos</th>
                                <th>Fecha</th>
                                <th class="ds-text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recetas as $receta): ?>
                            <tr>
                                <td>
                                    <span class="ds-text-primary ds-font-bold"><?= esc($receta['folio']) ?></span>
                                </td>
                                <td>
                                    <div class="ds-flex ds-align-center ds-gap-2">
                                        <div class="ds-avatar ds-avatar--sm ds-avatar--primary">
                                            <?= strtoupper(substr($receta['paciente_nombre'] ?? 'P', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="ds-font-medium">
                                                <?= esc($receta['paciente_nombre'] ?? '') ?> 
                                                <?= esc($receta['paciente_apellido'] ?? '') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="ds-text-muted">Dr./Dra.</span> 
                                    <?= esc($receta['medico_nombre'] ?? 'Sin asignar') ?>
                                </td>
                                <td>
                                    <span class="ds-badge ds-badge--info">
                                        <?= $receta['total_medicamentos'] ?? 0 ?> medicamento(s)
                                    </span>
                                </td>
                                <td>
                                    <span class="ds-text-muted"><?= date('d/m/Y', strtotime($receta['fecha'])) ?></span>
                                    <br>
                                    <small class="ds-text-muted"><?= date('H:i', strtotime($receta['fecha'])) ?></small>
                                </td>
                                <td class="ds-text-center">
                                    <div class="ds-btn-group">
                                        <a href="<?= base_url('/recetas/' . $receta['id']) ?>" 
                                           class="ds-btn ds-btn--sm ds-btn--secondary" 
                                           title="Ver detalles">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        <a href="<?= base_url('/recetas/imprimir/' . $receta['id']) ?>" 
                                           class="ds-btn ds-btn--sm ds-btn--primary" 
                                           title="Imprimir PDF"
                                           target="_blank">
                                            <i class="bi bi-printer"></i> PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- Estado vacío -->
                <div class="ds-empty-state">
                    <div class="ds-empty-state__icon">📋</div>
                    <h3 class="ds-empty-state__text">No hay recetas registradas</h3>
                    <p class="ds-text-muted">Para crear una receta, primero selecciona un paciente desde la sección de Pacientes o usa el botón "Nueva Receta".</p>
                    <div class="ds-empty-state__action ds-flex ds-gap-2 ds-justify-center">
                        <a href="<?= base_url('/recetas/nueva'); ?>" class="ds-btn ds-btn--primary">
                            <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                            Nueva Receta
                        </a>
                        <a href="<?= base_url('/pacientes'); ?>" class="ds-btn ds-btn--secondary">
                            <span class="ds-btn__icon ds-btn__icon--left">👥</span>
                            Ir a Pacientes
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Resumen rápido -->
    <?php if (!empty($recetas) && count($recetas) > 0): ?>
    <div class="ds-row ds-mt-4">
        <div class="ds-col-4">
            <div class="ds-card">
                <div class="ds-card__body ds-text-center">
                    <div class="ds-text-3xl ds-font-bold ds-text-primary"><?= count($recetas) ?></div>
                    <div class="ds-text-muted">Total de Recetas</div>
                </div>
            </div>
        </div>
        <div class="ds-col-4">
            <div class="ds-card">
                <div class="ds-card__body ds-text-center">
                    <?php 
                    $hoy = date('Y-m-d');
                    $recetasHoy = array_filter($recetas, function($r) use ($hoy) {
                        return date('Y-m-d', strtotime($r['fecha'])) === $hoy;
                    });
                    ?>
                    <div class="ds-text-3xl ds-font-bold ds-text-success"><?= count($recetasHoy) ?></div>
                    <div class="ds-text-muted">Recetas de Hoy</div>
                </div>
            </div>
        </div>
        <div class="ds-col-4">
            <div class="ds-card">
                <div class="ds-card__body ds-text-center">
                    <?php 
                    $totalMedicamentos = array_sum(array_column($recetas, 'total_medicamentos'));
                    ?>
                    <div class="ds-text-3xl ds-font-bold ds-text-info"><?= $totalMedicamentos ?></div>
                    <div class="ds-text-muted">Medicamentos Prescritos</div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>
