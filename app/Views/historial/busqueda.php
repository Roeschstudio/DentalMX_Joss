<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <div class="ds-page__header-content">
            <a href="<?= base_url('/historial/' . $paciente['id']); ?>" class="ds-btn ds-btn--outline ds-btn--sm mb-2">
                ← Volver al historial
            </a>
            <h1 class="ds-page__title">Resultados de búsqueda</h1>
            <p class="ds-page__subtitle">
                Búsqueda: "<?= esc($termino); ?>" - <?= count($actividades); ?> resultado(s)
            </p>
        </div>
    </div>

    <div class="ds-card">
        <div class="ds-card__header">
            <h2 class="ds-card__title">
                🔍 Actividades encontradas para "<?= esc($termino); ?>"
            </h2>
        </div>
        <div class="ds-card__body">
            <?php if (empty($actividades)): ?>
            <div class="ds-empty-state">
                <div class="ds-empty-state__icon">🔍</div>
                <h3 class="ds-empty-state__title">Sin resultados</h3>
                <p class="ds-empty-state__text">
                    No se encontraron actividades que coincidan con "<?= esc($termino); ?>".
                </p>
                <a href="<?= base_url('/historial/' . $paciente['id']); ?>" class="ds-btn ds-btn--primary">
                    Ver todo el historial
                </a>
            </div>
            <?php else: ?>
            <div class="ds-table-responsive">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Médico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actividades as $actividad): 
                            $tipoConfig = $tipos_actividad[$actividad['tipo_actividad']] ?? ['icon' => '📌', 'label' => 'Otro', 'class' => 'ds-badge--secondary'];
                        ?>
                        <tr>
                            <td>
                                <span class="ds-text-nowrap">
                                    <?= date('d/m/Y', strtotime($actividad['fecha_actividad'])); ?>
                                </span>
                                <br>
                                <small class="ds-text-muted"><?= date('H:i', strtotime($actividad['fecha_actividad'])); ?></small>
                            </td>
                            <td>
                                <span class="ds-badge <?= $tipoConfig['class']; ?>">
                                    <?= $tipoConfig['icon']; ?> <?= $tipoConfig['label']; ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $desc = esc($actividad['descripcion'] ?? 'Sin descripción');
                                $highlighted = preg_replace('/(' . preg_quote($termino, '/') . ')/i', '<mark>$1</mark>', $desc);
                                echo $highlighted;
                                ?>
                            </td>
                            <td>
                                <?= esc(($actividad['medico_nombre'] ?? '') . ' ' . ($actividad['medico_apellido'] ?? '')); ?>
                            </td>
                            <td>
                                <a href="<?= base_url('/historial/detalles/' . $actividad['id']); ?>" class="ds-btn ds-btn--sm ds-btn--outline">
                                    Ver →
                                </a>
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
<?= $this->endSection(); ?>
