<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">📋 Detalles de la Cita</h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/citas'); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">⬅️</span>
                Volver a Citas
            </a>
            <?php if ($cita['estado'] !== 'completada' && $cita['estado'] !== 'cancelada'): ?>
            <a href="<?= base_url('/citas/' . $cita['id'] . '/editar'); ?>" class="ds-btn ds-btn--primary">
                <span class="ds-btn__icon ds-btn__icon--left">✏️</span>
                Editar
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert--success">
            <span class="ds-alert__icon">✅</span>
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert--danger">
            <span class="ds-alert__icon">❌</span>
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div class="ds-grid ds-grid--2">
        <!-- Información de la Cita -->
        <div class="ds-card">
            <div class="ds-card__header">
                <h3 class="ds-card__title">📅 Información de la Cita</h3>
            </div>
            <div class="ds-card__body">
                <div class="ds-info-list">
                    <div class="ds-info-item">
                        <span class="ds-info-label">Título:</span>
                        <span class="ds-info-value"><?= esc($cita['titulo']); ?></span>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-info-label">Fecha y Hora:</span>
                        <span class="ds-info-value">
                            <?= date('d/m/Y', strtotime($cita['fecha_inicio'])); ?> 
                            de <?= date('H:i', strtotime($cita['fecha_inicio'])); ?> 
                            a <?= date('H:i', strtotime($cita['fecha_fin'])); ?>
                        </span>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-info-label">Tipo de Cita:</span>
                        <span class="ds-info-value"><?= ucfirst($cita['tipo_cita']); ?></span>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-info-label">Estado:</span>
                        <span class="ds-info-value">
                            <?php 
                            $estadoBadge = match($cita['estado']) {
                                'programada' => 'ds-badge--info',
                                'confirmada' => 'ds-badge--success',
                                'en_progreso' => 'ds-badge--warning',
                                'completada' => 'ds-badge--secondary',
                                'cancelada' => 'ds-badge--danger',
                                default => 'ds-badge--secondary'
                            };
                            ?>
                            <span class="ds-badge <?= $estadoBadge; ?>"><?= ucfirst(str_replace('_', ' ', $cita['estado'])); ?></span>
                        </span>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-info-label">Servicio:</span>
                        <span class="ds-info-value"><?= esc($cita['servicio_nombre'] ?? 'No especificado'); ?></span>
                    </div>
                    <?php if (!empty($cita['notas'])): ?>
                    <div class="ds-info-item">
                        <span class="ds-info-label">Notas:</span>
                        <span class="ds-info-value"><?= nl2br(esc($cita['notas'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Información del Paciente -->
        <div class="ds-card">
            <div class="ds-card__header">
                <h3 class="ds-card__title">👤 Paciente</h3>
            </div>
            <div class="ds-card__body">
                <div class="ds-info-list">
                    <div class="ds-info-item">
                        <span class="ds-info-label">Nombre:</span>
                        <span class="ds-info-value">
                            <a href="<?= base_url('/pacientes/' . $cita['id_paciente']); ?>" class="ds-link">
                                <?= esc(trim($cita['paciente_nombre'] . ' ' . ($cita['paciente_apellido'] ?? ''))); ?>
                            </a>
                        </span>
                    </div>
                    <?php if (!empty($cita['paciente_telefono'])): ?>
                    <div class="ds-info-item">
                        <span class="ds-info-label">Teléfono:</span>
                        <span class="ds-info-value">
                            <a href="tel:<?= esc($cita['paciente_telefono']); ?>"><?= esc($cita['paciente_telefono']); ?></a>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($cita['paciente_email'])): ?>
                    <div class="ds-info-item">
                        <span class="ds-info-label">Email:</span>
                        <span class="ds-info-value">
                            <a href="mailto:<?= esc($cita['paciente_email']); ?>"><?= esc($cita['paciente_email']); ?></a>
                        </span>
                    </div>
                    <?php endif; ?>
                    <div class="ds-info-item">
                        <span class="ds-info-label">Doctor:</span>
                        <span class="ds-info-value"><?= esc($cita['doctor_nombre'] ?? 'No asignado'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones según estado -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h3 class="ds-card__title">⚡ Acciones</h3>
        </div>
        <div class="ds-card__body">
            <div class="ds-flex ds-gap-3 ds-flex-wrap">
                <?php if ($cita['estado'] === 'programada'): ?>
                    <form action="<?= base_url('/citas/' . $cita['id'] . '/cambiar-estado'); ?>" method="POST" style="display: inline;">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="estado" value="confirmada">
                        <button type="submit" class="ds-btn ds-btn--success" onclick="return confirm('¿Confirmar esta cita?')">
                            <span class="ds-btn__icon ds-btn__icon--left">✓</span>
                            Confirmar Cita
                        </button>
                    </form>
                <?php endif; ?>
                
                <?php if ($cita['estado'] === 'confirmada'): ?>
                    <form action="<?= base_url('/citas/' . $cita['id'] . '/cambiar-estado'); ?>" method="POST" style="display: inline;">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="estado" value="en_progreso">
                        <button type="submit" class="ds-btn ds-btn--warning">
                            <span class="ds-btn__icon ds-btn__icon--left">▶️</span>
                            Iniciar Cita
                        </button>
                    </form>
                <?php endif; ?>
                
                <?php if ($cita['estado'] === 'en_progreso'): ?>
                    <form action="<?= base_url('/citas/' . $cita['id'] . '/cambiar-estado'); ?>" method="POST" style="display: inline;">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="estado" value="completada">
                        <button type="submit" class="ds-btn ds-btn--success" onclick="return confirm('¿Marcar como completada?')">
                            <span class="ds-btn__icon ds-btn__icon--left">✓</span>
                            Completar Cita
                        </button>
                    </form>
                <?php endif; ?>
                
                <?php if ($cita['estado'] !== 'completada' && $cita['estado'] !== 'cancelada'): ?>
                    <form action="<?= base_url('/citas/' . $cita['id'] . '/cambiar-estado'); ?>" method="POST" style="display: inline;">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="estado" value="cancelada">
                        <button type="submit" class="ds-btn ds-btn--danger" onclick="return confirm('¿Está seguro de cancelar esta cita?')">
                            <span class="ds-btn__icon ds-btn__icon--left">❌</span>
                            Cancelar Cita
                        </button>
                    </form>
                <?php endif; ?>
                
                <?php if ($cita['estado'] === 'cancelada'): ?>
                    <form action="<?= base_url('/citas/' . $cita['id'] . '/cambiar-estado'); ?>" method="POST" style="display: inline;">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="estado" value="programada">
                        <button type="submit" class="ds-btn ds-btn--info" onclick="return confirm('¿Reactivar esta cita?')">
                            <span class="ds-btn__icon ds-btn__icon--left">🔄</span>
                            Reactivar Cita
                        </button>
                    </form>
                <?php endif; ?>
                
                <a href="<?= base_url('/citas/calendario'); ?>" class="ds-btn ds-btn--secondary">
                    <span class="ds-btn__icon ds-btn__icon--left">📅</span>
                    Ver en Calendario
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.ds-info-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.ds-info-item {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.ds-info-label {
    font-weight: 600;
    color: var(--ds-text-secondary);
    min-width: 120px;
}

.ds-info-value {
    flex: 1;
}

.ds-link {
    color: var(--ds-primary);
    text-decoration: none;
}

.ds-link:hover {
    text-decoration: underline;
}

.ds-flex-wrap {
    flex-wrap: wrap;
}
</style>
<?= $this->endSection(); ?>
