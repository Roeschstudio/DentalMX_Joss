<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url('css/components/timeline.css'); ?>">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <!-- Header del Paciente -->
    <div class="ds-patient-header">
        <div class="ds-patient-header__info">
            <div class="ds-avatar ds-avatar--lg">
                <?= strtoupper(substr($paciente['nombre'] ?? 'P', 0, 1)); ?>
            </div>
            <div class="ds-patient-header__details">
                <h1 class="ds-patient-header__name">
                    <?= esc($paciente['nombre'] . ' ' . $paciente['primer_apellido'] . ' ' . ($paciente['segundo_apellido'] ?? '')); ?>
                </h1>
                <div class="ds-patient-header__meta">
                    <span class="ds-patient-header__meta-item">
                        🆔 ID: <?= $paciente['id']; ?>
                    </span>
                    <?php if (!empty($paciente['telefono'])): ?>
                    <span class="ds-patient-header__meta-item">
                        📱 <?= esc($paciente['telefono']); ?>
                    </span>
                    <?php endif; ?>
                    <?php if (!empty($paciente['email'])): ?>
                    <span class="ds-patient-header__meta-item">
                        ✉️ <?= esc($paciente['email']); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="ds-patient-header__actions">
            <a href="<?= base_url('/pacientes/' . $paciente['id']); ?>" class="ds-btn ds-btn--outline">
                👤 Ficha Paciente
            </a>
            <div class="ds-dropdown">
                <button class="ds-btn ds-btn--outline ds-dropdown__toggle" type="button">
                    📥 Exportar
                </button>
                <div class="ds-dropdown__menu">
                    <a class="ds-dropdown__item" href="<?= base_url('/historial/' . $paciente['id'] . '/exportar/pdf'); ?>">
                        📄 Exportar PDF
                    </a>
                    <a class="ds-dropdown__item" href="<?= base_url('/historial/' . $paciente['id'] . '/exportar/csv'); ?>">
                        📊 Exportar CSV
                    </a>
                    <a class="ds-dropdown__item" href="<?= base_url('/historial/' . $paciente['id'] . '/exportar/json'); ?>">
                        📋 Exportar JSON
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="ds-stats-grid">
        <div class="ds-stat-card">
            <div class="ds-stat-card__icon ds-stat-card__icon--primary">📊</div>
            <div class="ds-stat-card__content">
                <span class="ds-stat-card__value"><?= $pagination['total'] ?? 0; ?></span>
                <span class="ds-stat-card__label">Total Actividades</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-card__icon ds-stat-card__icon--success">🦷</div>
            <div class="ds-stat-card__content">
                <span class="ds-stat-card__value"><?= count($tratamientos_activos ?? []); ?></span>
                <span class="ds-stat-card__label">Tratamientos Activos</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-card__icon ds-stat-card__icon--warning">💰</div>
            <div class="ds-stat-card__content">
                <span class="ds-stat-card__value">$<?= number_format($resumen_financiero['saldo_pendiente'] ?? 0, 2); ?></span>
                <span class="ds-stat-card__label">Saldo Pendiente</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-card__icon ds-stat-card__icon--info">✅</div>
            <div class="ds-stat-card__content">
                <span class="ds-stat-card__value"><?= ($resumen_financiero['porcentaje_pagado'] ?? 0); ?>%</span>
                <span class="ds-stat-card__label">Pagado</span>
            </div>
        </div>
    </div>

    <div class="ds-grid ds-grid--2">
        <!-- Columna Principal: Timeline -->
        <div class="ds-grid__col--lg-8">
            <!-- Filtros -->
            <div class="ds-card mb-4">
                <div class="ds-card__body">
                    <form action="<?= base_url('/historial/' . $paciente['id']); ?>" method="get" class="ds-filter-form">
                        <div class="ds-filter-form__row">
                            <div class="ds-form-group">
                                <label class="ds-form-label">Tipo de Actividad</label>
                                <select name="tipo" class="ds-form-select">
                                    <option value="">Todos los tipos</option>
                                    <?php foreach ($tipos_actividad as $key => $tipo): ?>
                                    <option value="<?= $key; ?>" <?= (isset($filtros['tipo_actividad']) && in_array($key, $filtros['tipo_actividad'])) ? 'selected' : ''; ?>>
                                        <?= $tipo['icon']; ?> <?= $tipo['label']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ds-form-group">
                                <label class="ds-form-label">Desde</label>
                                <input type="date" name="fecha_inicio" class="ds-form-input" value="<?= isset($filtros['fecha_inicio']) ? substr($filtros['fecha_inicio'], 0, 10) : ''; ?>">
                            </div>
                            <div class="ds-form-group">
                                <label class="ds-form-label">Hasta</label>
                                <input type="date" name="fecha_fin" class="ds-form-input" value="<?= isset($filtros['fecha_fin']) ? substr($filtros['fecha_fin'], 0, 10) : ''; ?>">
                            </div>
                            <div class="ds-form-group">
                                <label class="ds-form-label">Buscar</label>
                                <input type="text" name="q" class="ds-form-input" placeholder="Buscar en descripción..." value="<?= esc($filtros['busqueda'] ?? ''); ?>">
                            </div>
                            <div class="ds-form-group ds-form-group--actions">
                                <button type="submit" class="ds-btn ds-btn--primary">
                                    🔍 Filtrar
                                </button>
                                <a href="<?= base_url('/historial/' . $paciente['id']); ?>" class="ds-btn ds-btn--outline">
                                    ✕ Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Timeline de Actividades -->
            <div class="ds-card">
                <div class="ds-card__header ds-card__header--with-actions">
                    <h2 class="ds-card__title">📅 Timeline de Actividades</h2>
                    <span class="ds-badge ds-badge--primary"><?= $pagination['total'] ?? 0; ?> actividades</span>
                </div>
                <div class="ds-card__body">
                    <?php if (empty($timeline)): ?>
                    <div class="ds-empty-state">
                        <div class="ds-empty-state__icon">📋</div>
                        <h3 class="ds-empty-state__title">Sin actividades registradas</h3>
                        <p class="ds-empty-state__text">No hay actividades en el historial de este paciente que coincidan con los filtros aplicados.</p>
                    </div>
                    <?php else: ?>
                    <div class="ds-timeline">
                        <?php 
                        $currentDate = '';
                        foreach ($timeline as $actividad): 
                            $fecha = date('Y-m-d', strtotime($actividad['fecha_actividad']));
                            $tipoConfig = $tipos_actividad[$actividad['tipo_actividad']] ?? ['icon' => '📌', 'label' => 'Actividad', 'class' => 'ds-badge--secondary'];
                            
                            if ($fecha !== $currentDate):
                                $currentDate = $fecha;
                        ?>
                        <div class="ds-timeline__date-separator">
                            <span class="ds-timeline__date">
                                <?= date('d', strtotime($fecha)); ?>
                                <small><?= date('M Y', strtotime($fecha)); ?></small>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="ds-timeline__item" data-id="<?= $actividad['id']; ?>">
                            <div class="ds-timeline__marker ds-timeline__marker--<?= $tipoConfig['color'] ?? 'primary'; ?>">
                                <?= $tipoConfig['icon']; ?>
                            </div>
                            <div class="ds-timeline__content">
                                <div class="ds-timeline__header">
                                    <span class="ds-badge <?= $tipoConfig['class']; ?>">
                                        <?= $tipoConfig['label']; ?>
                                    </span>
                                    <span class="ds-timeline__time">
                                        🕐 <?= date('H:i', strtotime($actividad['fecha_actividad'])); ?>
                                    </span>
                                </div>
                                <p class="ds-timeline__description">
                                    <?= esc($actividad['descripcion'] ?? 'Sin descripción'); ?>
                                </p>
                                <div class="ds-timeline__footer">
                                    <span class="ds-timeline__author">
                                        👨‍⚕️ <?= esc($actividad['medico_nombre'] ?? 'Sin asignar'); ?>
                                    </span>
                                    <a href="<?= base_url('/historial/detalles/' . $actividad['id']); ?>" class="ds-btn ds-btn--sm ds-btn--outline">
                                        Ver detalles →
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
                    <div class="ds-pagination">
                        <?php 
                        $currentPage = $pagination['current_page'];
                        $totalPages = $pagination['total_pages'];
                        $baseUrl = '/historial/' . $paciente['id'];
                        $queryParams = $_GET;
                        unset($queryParams['page']);
                        $queryString = http_build_query($queryParams);
                        ?>
                        
                        <?php if ($currentPage > 1): ?>
                        <a href="<?= base_url($baseUrl . '?' . $queryString . '&page=' . ($currentPage - 1)); ?>" class="ds-pagination__item">
                            ← Anterior
                        </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <a href="<?= base_url($baseUrl . '?' . $queryString . '&page=' . $i); ?>" class="ds-pagination__item <?= $i === $currentPage ? 'ds-pagination__item--active' : ''; ?>">
                            <?= $i; ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= base_url($baseUrl . '?' . $queryString . '&page=' . ($currentPage + 1)); ?>" class="ds-pagination__item">
                            Siguiente →
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="ds-grid__col--lg-4">
            <!-- Estadísticas por Tipo -->
            <div class="ds-card mb-4">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">📊 Por Tipo de Actividad</h3>
                </div>
                <div class="ds-card__body">
                    <?php if (empty($estadisticas)): ?>
                    <p class="ds-text-muted">Sin datos estadísticos</p>
                    <?php else: ?>
                    <div class="ds-stats-list">
                        <?php foreach ($estadisticas as $stat): 
                            $tipoConfig = $tipos_actividad[$stat['tipo_actividad']] ?? ['icon' => '📌', 'label' => 'Otro', 'class' => 'ds-badge--secondary'];
                        ?>
                        <a href="<?= base_url('/historial/' . $paciente['id'] . '/tipo/' . $stat['tipo_actividad']); ?>" class="ds-stats-list__item">
                            <span class="ds-stats-list__icon"><?= $tipoConfig['icon']; ?></span>
                            <span class="ds-stats-list__label"><?= $tipoConfig['label']; ?></span>
                            <span class="ds-badge ds-badge--sm"><?= $stat['total']; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tratamientos Activos -->
            <div class="ds-card mb-4">
                <div class="ds-card__header ds-card__header--with-actions">
                    <h3 class="ds-card__title">🦷 Tratamientos Activos</h3>
                    <a href="<?= base_url('/historial/' . $paciente['id'] . '/tratamientos'); ?>" class="ds-btn ds-btn--sm ds-btn--outline">
                        Ver todos
                    </a>
                </div>
                <div class="ds-card__body">
                    <?php if (empty($tratamientos_activos)): ?>
                    <p class="ds-text-muted">No hay tratamientos activos</p>
                    <?php else: ?>
                    <div class="ds-treatment-list">
                        <?php foreach (array_slice($tratamientos_activos, 0, 5) as $tratamiento): ?>
                        <div class="ds-treatment-item">
                            <div class="ds-treatment-item__info">
                                <span class="ds-treatment-item__name"><?= esc($tratamiento['servicio_nombre']); ?></span>
                                <?php if (!empty($tratamiento['diente'])): ?>
                                <span class="ds-treatment-item__tooth">Diente: <?= esc($tratamiento['diente']); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="ds-badge ds-badge--sm ds-badge--<?= $tratamiento['estado'] === 'en_progreso' ? 'warning' : 'info'; ?>">
                                <?= ucfirst(str_replace('_', ' ', $tratamiento['estado'])); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Resumen Financiero -->
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">💰 Resumen Financiero</h3>
                </div>
                <div class="ds-card__body">
                    <div class="ds-financial-summary">
                        <div class="ds-financial-summary__row">
                            <span>Costo Total:</span>
                            <strong>$<?= number_format($resumen_financiero['costo_total'] ?? 0, 2); ?></strong>
                        </div>
                        <div class="ds-financial-summary__row ds-financial-summary__row--success">
                            <span>Total Pagado:</span>
                            <strong>$<?= number_format($resumen_financiero['total_pagado'] ?? 0, 2); ?></strong>
                        </div>
                        <div class="ds-financial-summary__row ds-financial-summary__row--warning">
                            <span>Saldo Pendiente:</span>
                            <strong>$<?= number_format($resumen_financiero['saldo_pendiente'] ?? 0, 2); ?></strong>
                        </div>
                        
                        <!-- Barra de progreso -->
                        <div class="ds-progress mt-3">
                            <div class="ds-progress__bar" style="width: <?= $resumen_financiero['porcentaje_pagado'] ?? 0; ?>%"></div>
                        </div>
                        <small class="ds-text-muted"><?= $resumen_financiero['porcentaje_pagado'] ?? 0; ?>% pagado</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar dropdowns
    const dropdownToggles = document.querySelectorAll('.ds-dropdown__toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.closest('.ds-dropdown');
            dropdown.classList.toggle('ds-dropdown--open');
        });
    });

    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function() {
        document.querySelectorAll('.ds-dropdown--open').forEach(d => {
            d.classList.remove('ds-dropdown--open');
        });
    });
});
</script>
<?= $this->endSection(); ?>
