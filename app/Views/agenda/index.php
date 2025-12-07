<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">🕐 Horario de Atención</h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/agenda/excepciones'); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">📅</span>
                Excepciones
            </a>
            <a href="<?= base_url('/agenda/nueva'); ?>" class="ds-btn ds-btn--primary">
                <span class="ds-btn__icon ds-btn__icon--left">⚙️</span>
                Configurar Horario
            </a>
        </div>
    </div>

    <!-- Preferencias actuales -->
    <div class="ds-grid ds-grid--3 ds-mb-6">
        <div class="ds-stat-card ds-stat-card--primary">
            <div class="ds-stat-card__content">
                <div class="ds-stat-card__info">
                    <p class="ds-stat-card__title">Duración de Cita</p>
                    <p class="ds-stat-card__value"><?= esc($preferencias['duracion_cita'] ?? 30); ?> min</p>
                </div>
                <div class="ds-stat-card__icon">⏱️</div>
            </div>
        </div>
        <div class="ds-stat-card ds-stat-card--info">
            <div class="ds-stat-card__content">
                <div class="ds-stat-card__info">
                    <p class="ds-stat-card__title">Descanso entre Citas</p>
                    <p class="ds-stat-card__value"><?= esc($preferencias['tiempo_descanso'] ?? 15); ?> min</p>
                </div>
                <div class="ds-stat-card__icon">☕</div>
            </div>
        </div>
        <div class="ds-stat-card ds-stat-card--warning">
            <div class="ds-stat-card__content">
                <div class="ds-stat-card__info">
                    <p class="ds-stat-card__title">Próximas Excepciones</p>
                    <p class="ds-stat-card__value"><?= count($excepciones_futuras ?? []); ?></p>
                </div>
                <div class="ds-stat-card__icon">📋</div>
            </div>
        </div>
    </div>

    <div class="ds-card">
        <div class="ds-card__header">
            <h2 class="ds-card__title">Horarios Configurados</h2>
        </div>
        <div class="ds-card__body">
            <?php 
            $tieneHorarios = false;
            foreach ($horarios as $horario) {
                if (!empty($horario['hora_inicio'])) {
                    $tieneHorarios = true;
                    break;
                }
            }
            ?>
            <?php if (!$tieneHorarios): ?>
                <div class="ds-empty-state">
                    <div class="ds-empty-state__icon">🕐</div>
                    <h3 class="ds-empty-state__text">No hay horarios configurados</h3>
                    <p class="ds-text-muted">Configure su horario de atención para comenzar a recibir citas.</p>
                    <a href="<?= base_url('/agenda/nueva'); ?>" class="ds-btn ds-btn--primary ds-mt-4">
                        <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                        Configurar Horario
                    </a>
                </div>
            <?php else: ?>
                <div class="ds-table-responsive">
                    <table class="ds-table ds-table--hover ds-table--striped">
                        <thead>
                            <tr>
                                <th>Día</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($horarios as $horario): ?>
                            <tr>
                                <td><strong><?= esc($horario['dia']); ?></strong></td>
                                <td><?= !empty($horario['hora_inicio']) ? esc($horario['hora_inicio']) : '-'; ?></td>
                                <td><?= !empty($horario['hora_fin']) ? esc($horario['hora_fin']) : '-'; ?></td>
                                <td>
                                    <?php if ($horario['activo']): ?>
                                        <span class="ds-badge ds-badge--success">Activo</span>
                                    <?php else: ?>
                                        <span class="ds-badge ds-badge--secondary">Cerrado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Próximas excepciones -->
    <?php if (!empty($excepciones_futuras)): ?>
    <div class="ds-card ds-mt-6">
        <div class="ds-card__header">
            <h2 class="ds-card__title">Próximas Excepciones</h2>
            <a href="<?= base_url('/agenda/excepciones'); ?>" class="ds-btn ds-btn--sm ds-btn--secondary">
                Ver todas
            </a>
        </div>
        <div class="ds-card__body">
            <div class="ds-table-responsive">
                <table class="ds-table ds-table--hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Motivo</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($excepciones_futuras, 0, 5) as $excepcion): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($excepcion['fecha'])); ?></td>
                            <td><?= esc($excepcion['motivo'] ?? 'Sin motivo'); ?></td>
                            <td>
                                <?php if ($excepcion['todo_el_dia']): ?>
                                    <span class="ds-badge ds-badge--danger">Todo el día</span>
                                <?php else: ?>
                                    <span class="ds-badge ds-badge--warning">
                                        <?= substr($excepcion['hora_inicio'], 0, 5); ?> - <?= substr($excepcion['hora_fin'], 0, 5); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>
