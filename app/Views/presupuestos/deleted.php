<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">🗑️ Presupuestos Eliminados</h1>
    </div>
    <div class="ds-container ds-container--fluid">
    <div class="ds-row">
        <div class="ds-col-12">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">Presupuestos Eliminados</h3>
                    <div class="ds-card__actions">
                        <a href="<?= base_url('/presupuestos') ?>" class="ds-btn ds-btn--primary ds-btn--sm">
                            <i class="fas fa-arrow-left"></i> Volver a Presupuestos
                        </a>
                    </div>
                </div>
                <div class="ds-card__body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="ds-alert ds-alert--success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="ds-alert ds-alert--danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="ds-table-responsive">
                        <table class="ds-table ds-table--bordered ds-table--hover">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Paciente</th>
                                    <th>Médico</th>
                                    <th>Fecha Emisión</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha Eliminación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($presupuestos)): ?>
                                    <tr>
                                        <td colspan="8" class="ds-text-center">No hay presupuestos eliminados</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($presupuestos as $presupuesto): ?>
                                    <tr>
                                        <td><?= $presupuesto['folio'] ?></td>
                                        <td><?= $presupuesto['paciente_nombre'] . ' ' . $presupuesto['paciente_apellido'] ?></td>
                                        <td><?= $presupuesto['medico_nombre'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($presupuesto['fecha_emision'])) ?></td>
                                        <td>$<?= number_format($presupuesto['total'], 2) ?></td>
                                        <td>
                                            <?= $this->include('presupuestos/_badge_estado', ['estado' => $presupuesto['estado']]) ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($presupuesto['deleted_at'])) ?></td>
                                        <td>
                                            <div class="ds-btn-group">
                                                <a href="<?= base_url('/presupuestos/restore/' . $presupuesto['id']) ?>" 
                                                   class="ds-btn ds-btn--success ds-btn--sm" title="Restaurar"
                                                   onclick="return confirm('¿Está seguro de restaurar este presupuesto?')">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                                <a href="<?= base_url('/presupuestos/force-delete/' . $presupuesto['id']) ?>" 
                                                   class="ds-btn ds-btn--danger ds-btn--sm" title="Eliminar Permanentemente"
                                                   onclick="return confirm('¿Está seguro de eliminar permanentemente este presupuesto? Esta acción no se puede deshacer.')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
