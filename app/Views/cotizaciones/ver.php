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
            <h1 class="ds-page__title">💵 Cotización</h1>
            <p class="ds-page__subtitle">Cotización <span class="ds-text-primary ds-font-bold">#<?= esc($cotizacion['id']) ?></span></p>
        </div>
        <div class="ds-page__actions">
            <button class="ds-btn ds-btn--secondary" onclick="window.history.back()" title="Volver atrás">
                <span class="ds-btn__icon">←</span> Volver
            </button>
            <button class="ds-btn ds-btn--primary" onclick="window.print()" title="Imprimir">
                <span class="ds-btn__icon">🖨️</span> Imprimir
            </button>
            <a href="<?= base_url('/cotizaciones/imprimir/' . $cotizacion['id']) ?>" class="ds-btn ds-btn--primary" title="Descargar PDF" target="_blank">
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

    <!-- Información del paciente y cotización -->
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
                    <h3 class="ds-card__title">📅 Información de la Cotización</h3>
                </div>
                <div class="ds-card__body">
                    <div class="ds-grid ds-gap-3">
                        <div class="ds-row">
                            <div class="ds-col-6">
                                <p class="ds-text-muted ds-text-sm">ID Cotización</p>
                                <p class="ds-font-bold ds-text-lg">#<?= esc($cotizacion['id']) ?></p>
                            </div>
                            <div class="ds-col-6">
                                <p class="ds-text-muted ds-text-sm">Estado</p>
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
                            </div>
                        </div>
                        <div class="ds-row">
                            <div class="ds-col-6">
                                <p class="ds-text-muted ds-text-sm">Fecha Emisión</p>
                                <p class="ds-font-bold"><?= date('d/m/Y H:i', strtotime($cotizacion['fecha_emision'])) ?></p>
                            </div>
                            <div class="ds-col-6">
                                <p class="ds-text-muted ds-text-sm">Válido Hasta</p>
                                <?php 
                                $vigencia = strtotime($cotizacion['fecha_vigencia']);
                                $hoy = strtotime(date('Y-m-d'));
                                $vencido = $vigencia < $hoy;
                                ?>
                                <p class="ds-font-bold <?= $vencido ? 'ds-text-danger' : '' ?>">
                                    <?= date('d/m/Y', $vigencia) ?>
                                    <?= $vencido ? ' ⚠️ Vencido' : '' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios Cotizados -->
    <div class="ds-card ds-mb-4">
        <div class="ds-card__header">
            <h3 class="ds-card__title">🦷 Servicios Cotizados</h3>
            <?php if (!empty($detalles)): ?>
                <span class="ds-badge ds-badge--primary"><?= count($detalles) ?> servicio(s)</span>
            <?php endif; ?>
        </div>
        <div class="ds-card__body">
            <?php if (!empty($detalles)): ?>
                <div class="ds-table-responsive">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th class="ds-text-right">Precio Unitario</th>
                                <th class="ds-text-center">Cantidad</th>
                                <th class="ds-text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($detalles as $det): ?>
                            <tr>
                                <td>
                                    <span class="ds-font-bold ds-text-primary"><?= esc($det['nombre']) ?></span>
                                </td>
                                <td class="ds-text-right">
                                    $<?= number_format($det['precio_unitario'], 2) ?>
                                </td>
                                <td class="ds-text-center">
                                    <span class="ds-badge ds-badge--info"><?= esc($det['cantidad']) ?></span>
                                </td>
                                <td class="ds-text-right">
                                    <span class="ds-font-bold">$<?= number_format($det['subtotal'], 2) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f5f5f5;">
                                <td colspan="3" class="ds-text-right ds-font-bold">TOTAL:</td>
                                <td class="ds-text-right">
                                    <span class="ds-text-2xl ds-font-bold ds-text-primary">$<?= number_format($cotizacion['total'], 2) ?></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="ds-empty-state-sm">
                    <p class="ds-text-muted">ℹ️ No hay servicios registrados en esta cotización</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Observaciones -->
    <?php if(!empty($cotizacion['observaciones'])): ?>
    <div class="ds-card ds-mb-4">
        <div class="ds-card__header">
            <h3 class="ds-card__title">📝 Observaciones</h3>
        </div>
        <div class="ds-card__body">
            <div class="ds-alert ds-alert--info">
                <span class="ds-alert__icon">ℹ️</span>
                <?= nl2br(esc($cotizacion['observaciones'])) ?>
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
        <a href="<?= base_url('/cotizaciones/imprimir/' . $cotizacion['id']) ?>" class="ds-btn ds-btn--success" target="_blank">
            📄 Descargar PDF
        </a>
    </div>
</div>

<?= $this->endSection(); ?>
