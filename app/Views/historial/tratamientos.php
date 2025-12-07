<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <div class="ds-page__header-content">
            <a href="<?= base_url('/historial/' . $paciente['id']); ?>" class="ds-btn ds-btn--outline ds-btn--sm mb-2">
                ← Volver al historial
            </a>
            <h1 class="ds-page__title">🦷 Tratamientos</h1>
            <p class="ds-page__subtitle">
                Tratamientos realizados a <?= esc($paciente['nombre'] . ' ' . $paciente['primer_apellido']); ?>
            </p>
        </div>
        <div class="ds-page__actions">
            <a href="<?= base_url('/historial/' . $paciente['id']); ?>" class="ds-btn ds-btn--outline">
                📅 Ver Timeline
            </a>
        </div>
    </div>

    <!-- Resumen Financiero -->
    <div class="ds-stats-grid mb-4">
        <div class="ds-stat-card">
            <div class="ds-stat-card__icon ds-stat-card__icon--primary">🦷</div>
            <div class="ds-stat-card__content">
                <span class="ds-stat-card__value"><?= $resumen_financiero['total_tratamientos']; ?></span>
                <span class="ds-stat-card__label">Total Tratamientos</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-card__icon ds-stat-card__icon--success">✅</div>
            <div class="ds-stat-card__content">
                <span class="ds-stat-card__value"><?= $resumen_financiero['completados']; ?></span>
                <span class="ds-stat-card__label">Completados</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-card__icon ds-stat-card__icon--warning">⏳</div>
            <div class="ds-stat-card__content">
                <span class="ds-stat-card__value"><?= $resumen_financiero['activos']; ?></span>
                <span class="ds-stat-card__label">En Progreso</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-card__icon ds-stat-card__icon--info">💰</div>
            <div class="ds-stat-card__content">
                <span class="ds-stat-card__value">$<?= number_format($resumen_financiero['saldo_pendiente'], 2); ?></span>
                <span class="ds-stat-card__label">Saldo Pendiente</span>
            </div>
        </div>
    </div>

    <div class="ds-grid ds-grid--2">
        <!-- Lista de Tratamientos -->
        <div class="ds-grid__col--lg-8">
            <!-- Filtro por Estado -->
            <div class="ds-card mb-4">
                <div class="ds-card__body">
                    <div class="ds-filter-tabs">
                        <a href="<?= base_url('/historial/' . $paciente['id'] . '/tratamientos'); ?>" 
                           class="ds-filter-tab <?= empty($estado_actual) ? 'ds-filter-tab--active' : ''; ?>">
                            Todos
                        </a>
                        <?php foreach ($estados as $key => $estado): ?>
                        <a href="<?= base_url('/historial/' . $paciente['id'] . '/tratamientos?estado=' . $key); ?>" 
                           class="ds-filter-tab <?= $estado_actual === $key ? 'ds-filter-tab--active' : ''; ?>">
                            <?= $estado['icon']; ?> <?= $estado['label']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="ds-card">
                <div class="ds-card__header">
                    <h2 class="ds-card__title">Lista de Tratamientos</h2>
                </div>
                <div class="ds-card__body">
                    <?php if (empty($tratamientos)): ?>
                    <div class="ds-empty-state">
                        <div class="ds-empty-state__icon">🦷</div>
                        <h3 class="ds-empty-state__title">Sin tratamientos</h3>
                        <p class="ds-empty-state__text">
                            No hay tratamientos registrados para este paciente.
                        </p>
                    </div>
                    <?php else: ?>
                    <div class="ds-treatment-cards">
                        <?php foreach ($tratamientos as $tratamiento): 
                            $estadoConfig = $estados[$tratamiento['estado']] ?? ['icon' => '⚪', 'label' => 'Desconocido', 'class' => 'ds-badge--secondary'];
                            $saldo = ($tratamiento['costo'] ?? 0) - ($tratamiento['pagado'] ?? 0);
                        ?>
                        <div class="ds-treatment-card">
                            <div class="ds-treatment-card__header">
                                <div class="ds-treatment-card__title">
                                    <h4><?= esc($tratamiento['servicio_nombre']); ?></h4>
                                    <?php if (!empty($tratamiento['diente'])): ?>
                                    <span class="ds-treatment-card__tooth">🦷 Diente <?= esc($tratamiento['diente']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="ds-badge <?= $estadoConfig['class']; ?>">
                                    <?= $estadoConfig['icon']; ?> <?= $estadoConfig['label']; ?>
                                </span>
                            </div>
                            <div class="ds-treatment-card__body">
                                <div class="ds-treatment-card__info">
                                    <div class="ds-treatment-card__info-item">
                                        <span class="ds-treatment-card__label">Fecha Inicio:</span>
                                        <span class="ds-treatment-card__value"><?= date('d/m/Y', strtotime($tratamiento['fecha_inicio'])); ?></span>
                                    </div>
                                    <?php if (!empty($tratamiento['fecha_fin'])): ?>
                                    <div class="ds-treatment-card__info-item">
                                        <span class="ds-treatment-card__label">Fecha Fin:</span>
                                        <span class="ds-treatment-card__value"><?= date('d/m/Y', strtotime($tratamiento['fecha_fin'])); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="ds-treatment-card__info-item">
                                        <span class="ds-treatment-card__label">Médico:</span>
                                        <span class="ds-treatment-card__value"><?= esc(($tratamiento['medico_nombre'] ?? '') . ' ' . ($tratamiento['medico_apellido'] ?? '')); ?></span>
                                    </div>
                                </div>
                                <?php if (!empty($tratamiento['observaciones'])): ?>
                                <p class="ds-treatment-card__notes">
                                    <?= esc($tratamiento['observaciones']); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="ds-treatment-card__footer">
                                <div class="ds-treatment-card__financial">
                                    <span class="ds-treatment-card__cost">
                                        💰 $<?= number_format($tratamiento['costo'] ?? 0, 2); ?>
                                    </span>
                                    <span class="ds-treatment-card__paid ds-text-success">
                                        ✅ $<?= number_format($tratamiento['pagado'] ?? 0, 2); ?> pagado
                                    </span>
                                    <?php if ($saldo > 0): ?>
                                    <span class="ds-treatment-card__balance ds-text-warning">
                                        ⏳ $<?= number_format($saldo, 2); ?> pendiente
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="ds-treatment-card__actions">
                                    <a href="<?= base_url('/tratamientos/' . $tratamiento['id']); ?>" class="ds-btn ds-btn--sm ds-btn--outline">
                                        Ver detalles
                                    </a>
                                    <?php if (in_array($tratamiento['estado'], ['iniciado', 'en_progreso'])): ?>
                                    <button class="ds-btn ds-btn--sm ds-btn--primary" onclick="abrirModalPago(<?= $tratamiento['id']; ?>, <?= $saldo; ?>)">
                                        💵 Registrar Pago
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="ds-grid__col--lg-4">
            <!-- Estadísticas por Estado -->
            <div class="ds-card mb-4">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">📊 Por Estado</h3>
                </div>
                <div class="ds-card__body">
                    <div class="ds-stats-list">
                        <?php foreach ($estadisticas['por_estado'] as $stat): 
                            $estadoConfig = $estados[$stat['estado']] ?? ['icon' => '⚪', 'label' => 'Otro'];
                        ?>
                        <div class="ds-stats-list__item">
                            <span class="ds-stats-list__icon"><?= $estadoConfig['icon']; ?></span>
                            <span class="ds-stats-list__label"><?= $estadoConfig['label']; ?></span>
                            <span class="ds-badge ds-badge--sm"><?= $stat['total']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Tratamientos por Servicio -->
            <div class="ds-card mb-4">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">🦷 Por Servicio</h3>
                </div>
                <div class="ds-card__body">
                    <?php if (empty($estadisticas['por_servicio'])): ?>
                    <p class="ds-text-muted">Sin datos</p>
                    <?php else: ?>
                    <div class="ds-stats-list">
                        <?php foreach ($estadisticas['por_servicio'] as $stat): ?>
                        <div class="ds-stats-list__item">
                            <span class="ds-stats-list__label"><?= esc($stat['servicio']); ?></span>
                            <span class="ds-badge ds-badge--sm"><?= $stat['total']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Resumen Financiero Detallado -->
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">💰 Resumen Financiero</h3>
                </div>
                <div class="ds-card__body">
                    <div class="ds-financial-summary">
                        <div class="ds-financial-summary__row">
                            <span>Costo Total:</span>
                            <strong>$<?= number_format($resumen_financiero['costo_total'], 2); ?></strong>
                        </div>
                        <div class="ds-financial-summary__row ds-financial-summary__row--success">
                            <span>Total Pagado:</span>
                            <strong>$<?= number_format($resumen_financiero['total_pagado'], 2); ?></strong>
                        </div>
                        <div class="ds-financial-summary__row ds-financial-summary__row--warning">
                            <span>Saldo Pendiente:</span>
                            <strong>$<?= number_format($resumen_financiero['saldo_pendiente'], 2); ?></strong>
                        </div>
                        
                        <div class="ds-progress mt-3">
                            <div class="ds-progress__bar" style="width: <?= $resumen_financiero['porcentaje_pagado']; ?>%"></div>
                        </div>
                        <small class="ds-text-muted"><?= $resumen_financiero['porcentaje_pagado']; ?>% pagado</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pago -->
<div class="ds-modal" id="pagoModal">
    <div class="ds-modal__dialog">
        <div class="ds-modal__content">
            <div class="ds-modal__header">
                <h2 class="ds-modal__title">💵 Registrar Pago</h2>
                <button type="button" class="ds-modal__close" onclick="cerrarModalPago()">✕</button>
            </div>
            <form id="pagoForm">
                <div class="ds-modal__body">
                    <input type="hidden" id="tratamiento_id" name="tratamiento_id">
                    <div class="ds-form-group">
                        <label class="ds-form-label">Monto a pagar</label>
                        <div class="ds-input-group">
                            <span class="ds-input-group__prefix">$</span>
                            <input type="number" id="monto" name="monto" class="ds-form-input" step="0.01" min="0.01" required>
                        </div>
                        <small class="ds-form-text" id="saldoInfo"></small>
                    </div>
                </div>
                <div class="ds-modal__footer">
                    <button type="button" class="ds-btn ds-btn--outline" onclick="cerrarModalPago()">Cancelar</button>
                    <button type="submit" class="ds-btn ds-btn--success">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
function abrirModalPago(tratamientoId, saldo) {
    document.getElementById('tratamiento_id').value = tratamientoId;
    document.getElementById('monto').max = saldo;
    document.getElementById('monto').value = saldo;
    document.getElementById('saldoInfo').textContent = 'Saldo pendiente: $' + saldo.toFixed(2);
    document.getElementById('pagoModal').classList.add('ds-modal--show');
    document.getElementById('pagoModal').style.display = 'block';
}

function cerrarModalPago() {
    document.getElementById('pagoModal').classList.remove('ds-modal--show');
    document.getElementById('pagoModal').style.display = 'none';
}

document.getElementById('pagoForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const tratamientoId = document.getElementById('tratamiento_id').value;
    const monto = document.getElementById('monto').value;
    
    try {
        const response = await fetch('<?= base_url('/tratamientos/'); ?>' + tratamientoId + '/pago', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'monto=' + monto
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al registrar el pago');
    }
});
</script>
<?= $this->endSection(); ?>
