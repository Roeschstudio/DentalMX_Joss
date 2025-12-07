<?= $this->extend('layout/ds_layout') ?>

<?php
// Obtener configuración de la clínica para mensaje de bienvenida
$configModel = new \App\Models\ConfiguracionClinicaModel();
$clinicaConfig = $configModel->getConfiguracion();
$nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';
$mensajeBienvenida = $clinicaConfig['mensaje_bienvenida'] ?? '';

// Obtener nombre del usuario
$session = session();
$nombreUsuario = $session->get('nombre') ?? 'Doctor';
?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">
            📊 Panel Principal - <?= esc($nombreClinica) ?>
        </h1>
        <p class="ds-page__subtitle">
            ¡Bienvenido, <?= esc($nombreUsuario) ?>!
            <?php if (!empty($mensajeBienvenida)): ?>
                <span class="ds-text-gray-500"> — <?= esc($mensajeBienvenida) ?></span>
            <?php endif; ?>
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="ds-grid ds-grid--4">
        <div class="ds-stat-card ds-stat-card--primary">
            <div class="ds-stat-card__content">
                <div class="ds-stat-card__info">
                    <h3 class="ds-stat-card__title">Pacientes</h3>
                    <p class="ds-stat-card__value"><?= $stats['totalPatients'] ?></p>
                </div>
                <div class="ds-stat-card__icon">
                    👥
                </div>
            </div>
        </div>
        
        <div class="ds-stat-card ds-stat-card--success">
            <div class="ds-stat-card__content">
                <div class="ds-stat-card__info">
                    <h3 class="ds-stat-card__title">Citas Totales</h3>
                    <p class="ds-stat-card__value"><?= $stats['totalAppointments'] ?></p>
                </div>
                <div class="ds-stat-card__icon">
                    ✅
                </div>
            </div>
        </div>
        
        <div class="ds-stat-card ds-stat-card--info">
            <div class="ds-stat-card__content">
                <div class="ds-stat-card__info">
                    <h3 class="ds-stat-card__title">Citas Hoy</h3>
                    <p class="ds-stat-card__value"><?= $stats['todayAppointments'] ?></p>
                </div>
                <div class="ds-stat-card__icon">
                    📅
                </div>
            </div>
        </div>
        
        <div class="ds-stat-card ds-stat-card--warning">
            <div class="ds-stat-card__content">
                <div class="ds-stat-card__info">
                    <h3 class="ds-stat-card__title">Recetas Pendientes</h3>
                    <p class="ds-stat-card__value"><?= $stats['pendingPrescriptions'] ?></p>
                </div>
                <div class="ds-stat-card__icon">
                    📋
                </div>
            </div>
        </div>
    </div>

    <!-- Recent and Upcoming Appointments -->
    <div class="ds-grid ds-grid--2">
        <!-- Recent Appointments -->
        <div class="ds-card">
            <div class="ds-card__header">
                <h2 class="ds-card__title">
                    🕐 Citas Recientes
                </h2>
            </div>
            <div class="ds-card__body">
                <?php if (!empty($recentAppointments)): ?>
                    <div class="ds-table-container">
                        <table class="ds-table ds-table--striped">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentAppointments as $appointment): ?>
                                <tr>
                                    <td><?= esc($appointment['patient']) ?></td>
                                    <td><?= esc($appointment['time']) ?></td>
                                    <td>
                                        <?php if ($appointment['status'] === 'completed'): ?>
                                            <span class="ds-badge ds-badge--success">Completada</span>
                                        <?php else: ?>
                                            <span class="ds-badge ds-badge--warning">En Proceso</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="ds-empty-state ds-empty-state--small">
                        <div class="ds-empty-state__icon">
                            📭
                        </div>
                        <p class="ds-empty-state__text">No hay citas recientes</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="ds-card">
            <div class="ds-card__header">
                <h2 class="ds-card__title">
                    📆 Próximas Citas
                </h2>
            </div>
            <div class="ds-card__body">
                <?php if (!empty($upcomingAppointments)): ?>
                    <div class="ds-table-container">
                        <table class="ds-table ds-table--striped">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Hora</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingAppointments as $appointment): ?>
                                <tr>
                                    <td><?= esc($appointment['patient']) ?></td>
                                    <td><?= esc($appointment['time']) ?></td>
                                    <td><?= esc($appointment['date']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="ds-empty-state ds-empty-state--small">
                        <div class="ds-empty-state__icon">
                            📭
                        </div>
                        <p class="ds-empty-state__text">No hay próximas citas programadas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h2 class="ds-card__title">
                ⚡ Acciones Rápidas
            </h2>
        </div>
        <div class="ds-card__body">
            <div class="ds-grid ds-grid--4">
                <div class="ds-quick-action">
                    <a href="<?= base_url('/pacientes/nuevo') ?>" class="ds-quick-action__link">
                        <div class="ds-quick-action__icon ds-quick-action__icon--primary">
                            👤
                        </div>
                        <span class="ds-quick-action__text">Nuevo Paciente</span>
                    </a>
                </div>
                <div class="ds-quick-action">
                    <a href="<?= base_url('/citas/nueva') ?>" class="ds-quick-action__link">
                        <div class="ds-quick-action__icon ds-quick-action__icon--info">
                            📅
                        </div>
                        <span class="ds-quick-action__text">Nueva Cita</span>
                    </a>
                </div>
                <div class="ds-quick-action">
                    <a href="<?= base_url('/medicamentos') ?>" class="ds-quick-action__link">
                        <div class="ds-quick-action__icon ds-quick-action__icon--success">
                            💊
                        </div>
                        <span class="ds-quick-action__text">Medicamentos</span>
                    </a>
                </div>
                <div class="ds-quick-action">
                    <a href="<?= base_url('/servicios') ?>" class="ds-quick-action__link">
                        <div class="ds-quick-action__icon ds-quick-action__icon--warning">
                            🛠️
                        </div>
                        <span class="ds-quick-action__text">Servicios</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>