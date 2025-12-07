<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <div>
            <h1 class="ds-page__title">💵 Cotizaciones</h1>
            <p class="ds-page__subtitle">Gestión de presupuestos y cotizaciones</p>
        </div>
        <div class="ds-page__actions">
            <a href="<?= base_url('/cotizaciones/nueva'); ?>" class="ds-btn ds-btn--primary">
                <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                Nueva Cotización
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
            <h2 class="ds-card__title">Lista de Cotizaciones</h2>
            <span class="ds-badge ds-badge--primary"><?= count($cotizaciones ?? []) ?> cotizaciones</span>
        </div>
        <div class="ds-card__body">
            <?php if (!empty($cotizaciones)): ?>
                <div class="ds-table-responsive">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Paciente</th>
                                <th>Fecha Emisión</th>
                                <th>Vigencia</th>
                                <th class="ds-text-right">Total</th>
                                <th class="ds-text-center">Estado</th>
                                <th class="ds-text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cotizaciones as $cotizacion): ?>
                                <tr>
                                    <td>
                                        <span class="ds-text-primary ds-font-bold">#<?= esc($cotizacion['id']) ?></span>
                                    </td>
                                    <td>
                                        <div class="ds-flex ds-align-center ds-gap-2">
                                            <div class="ds-avatar ds-avatar--sm ds-avatar--primary">
                                                <?= strtoupper(substr($cotizacion['paciente_nombre'] ?? 'P', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <span class="ds-font-medium">
                                                    <?= esc($cotizacion['paciente_nombre'] ?? '') ?> 
                                                    <?= esc($cotizacion['paciente_apellido'] ?? '') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span><?= date('d/m/Y', strtotime($cotizacion['fecha_emision'])) ?></span>
                                        <br>
                                        <small class="ds-text-muted"><?= date('H:i', strtotime($cotizacion['fecha_emision'])) ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                        $vigencia = strtotime($cotizacion['fecha_vigencia']);
                                        $hoy = strtotime(date('Y-m-d'));
                                        $vencido = $vigencia < $hoy;
                                        ?>
                                        <span class="<?= $vencido ? 'ds-text-danger' : '' ?>">
                                            <?= date('d/m/Y', $vigencia) ?>
                                        </span>
                                        <?php if ($vencido): ?>
                                            <br><small class="ds-text-danger">⚠️ Vencido</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="ds-text-right">
                                        <span class="ds-font-bold ds-text-lg">$<?= number_format($cotizacion['total'], 2) ?></span>
                                    </td>
                                    <td class="ds-text-center">
                                        <?php 
                                        $estadoClases = [
                                            'pendiente' => 'ds-badge--warning',
                                            'aceptada' => 'ds-badge--success',
                                            'rechazada' => 'ds-badge--danger'
                                        ];
                                        $estadoTexto = [
                                            'pendiente' => '⏳ Pendiente',
                                            'aceptada' => '✅ Aceptada',
                                            'rechazada' => '❌ Rechazada'
                                        ];
                                        $estado = $cotizacion['estado'] ?? 'pendiente';
                                        ?>
                                        <span class="ds-badge <?= $estadoClases[$estado] ?? 'ds-badge--secondary' ?>">
                                            <?= $estadoTexto[$estado] ?? ucfirst($estado) ?>
                                        </span>
                                    </td>
                                    <td class="ds-text-center">
                                        <div class="ds-btn-group">
                                            <a href="<?= base_url('/cotizaciones/' . $cotizacion['id']); ?>" 
                                               class="ds-btn ds-btn--sm ds-btn--secondary" 
                                               title="Ver detalles">
                                                👁️ Ver
                                            </a>
                                            <a href="<?= base_url('/cotizaciones/imprimir/' . $cotizacion['id']); ?>" 
                                               class="ds-btn ds-btn--sm ds-btn--primary" 
                                               title="Imprimir PDF"
                                               target="_blank">
                                                📄 PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="ds-empty-state">
                    <div class="ds-empty-state__icon">💵</div>
                    <h3 class="ds-empty-state__text">No hay cotizaciones registradas</h3>
                    <p class="ds-text-muted">Para crear una cotización, selecciona un paciente o usa el botón "Nueva Cotización".</p>
                    <div class="ds-empty-state__action ds-flex ds-gap-2 ds-justify-center">
                        <a href="<?= base_url('/cotizaciones/nueva'); ?>" class="ds-btn ds-btn--primary">
                            <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                            Nueva Cotización
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
    <?php if (!empty($cotizaciones) && count($cotizaciones) > 0): ?>
    <div class="ds-row ds-mt-4">
        <div class="ds-col-3">
            <div class="ds-card">
                <div class="ds-card__body ds-text-center">
                    <div class="ds-text-3xl ds-font-bold ds-text-primary"><?= count($cotizaciones) ?></div>
                    <div class="ds-text-muted">Total Cotizaciones</div>
                </div>
            </div>
        </div>
        <div class="ds-col-3">
            <div class="ds-card">
                <div class="ds-card__body ds-text-center">
                    <?php 
                    $pendientes = array_filter($cotizaciones, fn($c) => ($c['estado'] ?? '') === 'pendiente');
                    ?>
                    <div class="ds-text-3xl ds-font-bold ds-text-warning"><?= count($pendientes) ?></div>
                    <div class="ds-text-muted">Pendientes</div>
                </div>
            </div>
        </div>
        <div class="ds-col-3">
            <div class="ds-card">
                <div class="ds-card__body ds-text-center">
                    <?php 
                    $aceptadas = array_filter($cotizaciones, fn($c) => ($c['estado'] ?? '') === 'aceptada');
                    ?>
                    <div class="ds-text-3xl ds-font-bold ds-text-success"><?= count($aceptadas) ?></div>
                    <div class="ds-text-muted">Aceptadas</div>
                </div>
            </div>
        </div>
        <div class="ds-col-3">
            <div class="ds-card">
                <div class="ds-card__body ds-text-center">
                    <?php 
                    $totalMonto = array_sum(array_column($cotizaciones, 'total'));
                    ?>
                    <div class="ds-text-2xl ds-font-bold ds-text-info">$<?= number_format($totalMonto, 2) ?></div>
                    <div class="ds-text-muted">Monto Total</div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>
