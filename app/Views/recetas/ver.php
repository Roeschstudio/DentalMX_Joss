<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>

<?php
// Obtener configuración de la clínica
$configModel = new \App\Models\ConfiguracionClinicaModel();
$clinicaConfig = $configModel->getConfiguracion();
$nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';
$telefonoClinica = $clinicaConfig['telefono'] ?? '';
$emailClinica = $clinicaConfig['email'] ?? '';
$direccionClinica = $clinicaConfig['direccion'] ?? '';
?>

<div class="ds-page">
    <!-- Encabezado de página -->
    <div class="ds-page__header">
        <div>
            <h1 class="ds-page__title">📋 Receta Médica</h1>
            <p class="ds-page__subtitle">Folio: <span class="ds-text-primary ds-font-bold"><?= esc($receta['folio']) ?></span></p>
        </div>
        <div class="ds-page__actions">
            <button class="ds-btn ds-btn--secondary" onclick="window.history.back()" title="Volver atrás">
                <span class="ds-btn__icon">←</span> Volver
            </button>
            <button class="ds-btn ds-btn--primary" onclick="window.print()" title="Imprimir">
                <span class="ds-btn__icon">🖨️</span> Imprimir
            </button>
            <a href="<?= base_url('/recetas/imprimir/' . $receta['id']) ?>" class="ds-btn ds-btn--primary" title="Descargar PDF" target="_blank">
                <span class="ds-btn__icon">📄</span> PDF
            </a>
        </div>
    </div>

    <!-- Información de la clínica y médico -->
    <div class="ds-card ds-mb-4">
        <div class="ds-card__header ds-text-center">
            <h2 class="ds-heading-2"><?= esc($nombreClinica) ?></h2>
            <?php if (!empty($direccionClinica) || !empty($telefonoClinica) || !empty($emailClinica)): ?>
                <p class="ds-text-muted ds-text-sm">
                    <?php if (!empty($direccionClinica)): ?>
                        <span>📍 <?= esc($direccionClinica) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($telefonoClinica)): ?>
                        <span class="ds-mx-2">|</span>
                        <span>📞 <?= esc($telefonoClinica) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($emailClinica)): ?>
                        <span class="ds-mx-2">|</span>
                        <span>✉️ <?= esc($emailClinica) ?></span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <?php if ($medico): ?>
                <div class="ds-mt-3 ds-pt-3 ds-border-top">
                    <p class="ds-text-muted">Profesional Médico</p>
                    <p class="ds-font-bold">🏥 Dr./Dra. <?= esc($medico['nombre']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Información del paciente y fecha -->
    <div class="ds-row ds-mb-4">
        <div class="ds-col-6">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">👤 Información del Paciente</h3>
                </div>
                <div class="ds-card__body">
                    <?php if ($paciente): ?>
                        <div class="ds-flex ds-align-center ds-gap-3">
                            <div class="ds-avatar ds-avatar--lg ds-avatar--primary">
                                <?= strtoupper(substr($paciente['nombre'] ?? 'P', 0, 1)) ?>
                            </div>
                            <div>
                                <p class="ds-text-muted ds-text-sm">Nombre</p>
                                <p class="ds-font-bold"><?= esc($paciente['nombre'] ?? '') ?></p>
                                <p class="ds-font-bold"><?= esc($paciente['primer_apellido'] ?? '') ?> <?= esc($paciente['segundo_apellido'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="ds-col-6">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">📅 Información de la Receta</h3>
                </div>
                <div class="ds-card__body">
                    <div class="ds-grid ds-gap-3">
                        <div>
                            <p class="ds-text-muted ds-text-sm">Folio</p>
                            <p class="ds-font-bold ds-text-lg"><?= esc($receta['folio']) ?></p>
                        </div>
                        <div>
                            <p class="ds-text-muted ds-text-sm">Fecha y Hora</p>
                            <p class="ds-font-bold"><?= date('d/m/Y', strtotime($receta['fecha'])) ?></p>
                            <p class="ds-text-muted"><?= date('H:i', strtotime($receta['fecha'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medicamentos Prescritos -->
    <div class="ds-card ds-mb-4">
        <div class="ds-card__header">
            <h3 class="ds-card__title">💊 Medicamentos Prescritos</h3>
            <?php if (!empty($detalles)): ?>
                <span class="ds-badge ds-badge--primary"><?= count($detalles) ?> medicamento(s)</span>
            <?php endif; ?>
        </div>
        <div class="ds-card__body">
            <?php if (!empty($detalles)): ?>
                <div class="ds-table-responsive">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Medicamento</th>
                                <th>Sustancia Activa</th>
                                <th>Dosis</th>
                                <th>Duración</th>
                                <th class="ds-text-center">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($detalles as $det): ?>
                            <tr>
                                <td>
                                    <span class="ds-font-bold ds-text-primary"><?= esc($det['nombre_comercial']) ?></span>
                                </td>
                                <td>
                                    <small class="ds-text-muted"><?= esc($det['sustancia_activa']) ?></small>
                                </td>
                                <td>
                                    <span class="ds-badge ds-badge--info"><?= esc($det['dosis']) ?></span>
                                </td>
                                <td>
                                    <span><?= esc($det['duracion']) ?></span>
                                </td>
                                <td class="ds-text-center">
                                    <span class="ds-badge ds-badge--success"><?= esc($det['cantidad']) ?> unid.</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="ds-empty-state-sm">
                    <p class="ds-text-muted">ℹ️ No hay medicamentos registrados en esta receta</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notas Adicionales -->
    <?php if(!empty($receta['notas_adicionales'])): ?>
    <div class="ds-card ds-mb-4">
        <div class="ds-card__header">
            <h3 class="ds-card__title">📝 Notas Adicionales</h3>
        </div>
        <div class="ds-card__body">
            <div class="ds-alert ds-alert--info">
                <span class="ds-alert__icon">ℹ️</span>
                <?= nl2br(esc($receta['notas_adicionales'])) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Acciones finales -->
    <div class="ds-flex ds-gap-3 ds-justify-end ds-mb-4">
        <button class="ds-btn ds-btn--secondary" onclick="window.history.back()">
            ← Volver
        </button>
        <button class="ds-btn ds-btn--primary" onclick="window.print()">
            🖨️ Imprimir
        </button>
        <a href="<?= base_url('/recetas/imprimir/' . $receta['id']) ?>" class="ds-btn ds-btn--success" target="_blank">
            📄 Descargar PDF
        </a>
    </div>
</div>

<?= $this->endSection(); ?>
