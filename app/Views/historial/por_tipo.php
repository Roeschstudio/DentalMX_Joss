<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url('css/components/timeline.css'); ?>">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <div class="ds-page__header-content">
            <a href="<?= base_url('/historial/' . $paciente['id']); ?>" class="ds-btn ds-btn--outline ds-btn--sm mb-2">
                ← Volver al historial
            </a>
            <h1 class="ds-page__title">
                <?= $tipo_config['icon']; ?> <?= $tipo_config['label']; ?>
            </h1>
            <p class="ds-page__subtitle">
                Historial de <?= strtolower($tipo_config['label']); ?> de <?= esc($paciente['nombre'] . ' ' . $paciente['primer_apellido']); ?>
            </p>
        </div>
    </div>

    <div class="ds-card">
        <div class="ds-card__header ds-card__header--with-actions">
            <h2 class="ds-card__title">
                <?= count($actividades); ?> actividades de tipo "<?= $tipo_config['label']; ?>"
            </h2>
            <span class="ds-badge <?= $tipo_config['class']; ?>"><?= count($actividades); ?></span>
        </div>
        <div class="ds-card__body">
            <?php if (empty($actividades)): ?>
            <div class="ds-empty-state">
                <div class="ds-empty-state__icon"><?= $tipo_config['icon']; ?></div>
                <h3 class="ds-empty-state__title">Sin actividades</h3>
                <p class="ds-empty-state__text">
                    No hay actividades de tipo "<?= $tipo_config['label']; ?>" registradas para este paciente.
                </p>
            </div>
            <?php else: ?>
            <div class="ds-timeline">
                <?php foreach ($actividades as $actividad): ?>
                <div class="ds-timeline__item">
                    <div class="ds-timeline__marker ds-timeline__marker--<?= $tipo_config['color'] ?? 'primary'; ?>">
                        <?= $tipo_config['icon']; ?>
                    </div>
                    <div class="ds-timeline__content">
                        <div class="ds-timeline__header">
                            <span class="ds-timeline__date">
                                📅 <?= date('d/m/Y', strtotime($actividad['fecha_actividad'])); ?>
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
                                👨‍⚕️ <?= esc(($actividad['medico_nombre'] ?? '') . ' ' . ($actividad['medico_apellido'] ?? '')); ?>
                            </span>
                            <a href="<?= base_url('/historial/detalles/' . $actividad['id']); ?>" class="ds-btn ds-btn--sm ds-btn--outline">
                                Ver detalles →
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
