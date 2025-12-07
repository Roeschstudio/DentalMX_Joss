<?php
// Obtener configuración de la clínica
$configModel = new \App\Models\ConfiguracionClinicaModel();
$clinicaConfig = $configModel->getConfiguracion();
$nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';
$telefonoClinica = $clinicaConfig['telefono'] ?? '';
$emailClinica = $clinicaConfig['email'] ?? '';
$direccionClinica = $clinicaConfig['direccion'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Presupuesto <?= $presupuesto['folio'] ?></title>
    <link rel="stylesheet" href="<?= base_url('css/components/print.css') ?>">
</head>
<body>
    <div class="ds-print-section">
        <h1 class="ds-print-title">PRESUPUESTO</h1>
        <div class="ds-print-subtitle ds-print-muted">
            <strong><?= esc($nombreClinica) ?></strong><br>
            <?= !empty($direccionClinica) ? 'Dirección: ' . esc($direccionClinica) . '<br>' : '' ?>
            <?= !empty($telefonoClinica) ? 'Teléfono: ' . esc($telefonoClinica) . '<br>' : '' ?>
            <?= !empty($emailClinica) ? 'Email: ' . esc($emailClinica) : '' ?>
        </div>
        <div class="ds-print-divider"></div>
    </div>
    
    <div class="ds-print-section">
        <h3 class="ds-print-title">Información del Presupuesto</h3>
        <table class="ds-print-table">
            <tr>
                <td width="20%"><strong>Folio:</strong></td>
                <td width="30%"><?= $presupuesto['folio'] ?></td>
                <td width="20%"><strong>Fecha Emisión:</strong></td>
                <td width="30%"><?= date('d/m/Y H:i', strtotime($presupuesto['fecha_emision'])) ?></td>
            </tr>
            <tr>
                <td><strong>Fecha Vigencia:</strong></td>
                <td><?= date('d/m/Y', strtotime($presupuesto['fecha_vigencia'])) ?></td>
                <td><strong>Estado:</strong></td>
                <td>
                    <?php
                    $badgeClass = [
                        'borrador' => 'badge-secondary',
                        'pendiente' => 'badge-warning',
                        'aprobado' => 'badge-success',
                        'rechazado' => 'badge-danger',
                        'convertido' => 'badge-info'
                    ];
                    $badgeText = [
                        'borrador' => 'Borrador',
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                        'convertido' => 'Convertido'
                    ];
                    ?>
                    <span class="ds-print-badge <?=
                        $presupuesto['estado'] === 'aprobado' ? 'ds-print-badge--success' : (
                        $presupuesto['estado'] === 'pendiente' ? 'ds-print-badge--warning' : (
                        $presupuesto['estado'] === 'rechazado' ? 'ds-print-badge--danger' : (
                        $presupuesto['estado'] === 'convertido' ? 'ds-print-badge--info' : ''
                    ))) ?>">
                        <?= $badgeText[$presupuesto['estado']] ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="ds-print-section">
        <h3 class="ds-print-title">Información del Paciente</h3>
        <table class="ds-print-table">
            <tr>
                <td width="20%"><strong>Paciente:</strong></td>
                <td width="30%"><?= $presupuesto['paciente_nombre'] . ' ' . $presupuesto['paciente_apellido'] ?></td>
                <td width="20%"><strong>Médico:</strong></td>
                <td width="30%"><?= $presupuesto['medico_nombre'] ?></td>
            </tr>
            <?php if (!empty($presupuesto['observaciones'])): ?>
            <tr>
                <td><strong>Observaciones:</strong></td>
                <td colspan="3"><?= $presupuesto['observaciones'] ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    
    <div class="ds-print-section">
        <h3 class="ds-print-title">Detalles del Presupuesto</h3>
        <table class="ds-print-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="25%">Servicio</th>
                    <th width="30%">Descripción</th>
                    <th width="10%" class="ds-text-center">Cantidad</th>
                    <th width="15%" class="ds-text-right">Precio Unitario</th>
                    <th width="10%" class="ds-text-center">Descuento %</th>
                    <th width="15%" class="ds-text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $contador = 1; ?>
                <?php foreach ($presupuesto['detalles'] as $detalle): ?>
                <tr>
                    <td class="ds-text-center"><?= $contador++ ?></td>
                    <td><?= $detalle['servicio_nombre'] ?></td>
                    <td><?= $detalle['descripcion'] ?></td>
                    <td class="ds-text-center"><?= $detalle['cantidad'] ?></td>
                    <td class="ds-text-right">$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                    <td class="ds-text-center"><?= $detalle['descuento_porcentaje'] ?>%</td>
                    <td class="ds-text-right">$<?= number_format($detalle['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="ds-print-section">
        <table class="ds-print-table ds-print-table--narrow">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="ds-text-right">$<?= number_format($presupuesto['subtotal'], 2) ?></td>
            </tr>
            <tr>
                <td><strong>IVA (16%):</strong></td>
                <td class="ds-text-right">$<?= number_format($presupuesto['iva'], 2) ?></td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td class="ds-text-right"><strong>$<?= number_format($presupuesto['total'], 2) ?></strong></td>
            </tr>
        </table>
    </div>
    
    <div class="ds-print-section ds-print-muted">
        <div class="ds-print-divider"></div>
        <p>Este presupuesto tiene validez hasta el <?= date('d/m/Y', strtotime($presupuesto['fecha_vigencia'])) ?></p>
        <p>Documento generado automáticamente - Sistema de Gestión Dental</p>
    </div>
</body>
</html>
