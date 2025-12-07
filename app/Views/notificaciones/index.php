<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">🔔 Notificaciones</h1>
    </div>

    <div class="ds-card">
        <div class="ds-card__header">
            <h2 class="ds-card__title">Centro de Notificaciones</h2>
        </div>
        <div class="ds-card__body">
            <?php if (empty($notificaciones)): ?>
                <div class="ds-empty-state">
                    <div class="ds-empty-state__icon">🔔</div>
                    <h3 class="ds-empty-state__text">No hay notificaciones</h3>
                    <p class="ds-text-muted">Las notificaciones del sistema aparecerán aquí.</p>
                </div>
            <?php else: ?>
                <div class="ds-list">
                    <?php foreach ($notificaciones as $notif): ?>
                        <div class="ds-list__item <?= $notif['leida'] ? '' : 'ds-list__item--unread'; ?>">
                            <div class="ds-list__icon">
                                <?php
                                $iconos = [
                                    'info' => 'ℹ️',
                                    'success' => '✅',
                                    'warning' => '⚠️',
                                    'error' => '❌'
                                ];
                                echo $iconos[$notif['tipo']] ?? '🔔';
                                ?>
                            </div>
                            <div class="ds-list__content">
                                <h4 class="ds-list__title"><?= esc($notif['titulo']); ?></h4>
                                <p class="ds-list__text"><?= esc($notif['mensaje']); ?></p>
                                <span class="ds-list__meta"><?= esc($notif['fecha']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
