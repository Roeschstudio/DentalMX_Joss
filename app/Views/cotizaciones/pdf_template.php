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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización #<?= esc($cotizacion['id']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            background: white;
            padding: 40px;
            max-width: 850px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
        }
        
        .clinic-name {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .document-title {
            font-size: 18px;
            color: #333;
            background-color: #d4edda;
            padding: 8px 20px;
            display: inline-block;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .clinic-info {
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .clinic-info span {
            display: inline-block;
            margin: 0 10px;
        }
        
        .doctor-section {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border-left: 4px solid #28a745;
        }
        
        .doctor-title {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .doctor-name {
            font-size: 14px;
            font-weight: bold;
            color: #28a745;
        }
        
        .content {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: white;
            background-color: #28a745;
            padding: 10px 15px;
            margin-top: 20px;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        .patient-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #17a2b8;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        .info-row {
            display: flex;
            gap: 30px;
            margin-bottom: 12px;
        }
        
        .info-field {
            flex: 1;
        }
        
        .info-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background-color: white;
        }
        
        thead {
            background-color: #28a745;
            color: white;
        }
        
        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #28a745;
        }
        
        td {
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tbody tr:hover {
            background-color: #d4edda;
        }
        
        .service-name {
            font-weight: bold;
            color: #28a745;
        }
        
        .total-row {
            background-color: #d4edda !important;
        }
        
        .total-row td {
            font-weight: bold;
            font-size: 16px;
        }
        
        .notes-section {
            background-color: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            border-radius: 3px;
            margin: 20px 0;
            font-size: 12px;
            color: #333;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #856404;
        }
        
        .validity-box {
            background-color: #d1ecf1;
            padding: 15px;
            border-left: 4px solid #17a2b8;
            border-radius: 3px;
            margin: 20px 0;
            text-align: center;
        }
        
        .validity-title {
            font-size: 12px;
            color: #0c5460;
            margin-bottom: 5px;
        }
        
        .validity-date {
            font-size: 18px;
            font-weight: bold;
            color: #17a2b8;
        }
        
        .signature-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin-bottom: 5px;
            height: 60px;
            display: flex;
            align-items: flex-end;
        }
        
        .signature-label {
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }
        
        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #d4edda;
            color: #28a745;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .header {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <div class="clinic-name"><?= esc($nombreClinica) ?></div>
        <?php if (!empty($direccionClinica) || !empty($telefonoClinica) || !empty($emailClinica)): ?>
            <div class="clinic-info">
                <?php if (!empty($direccionClinica)): ?>
                    <span>📍 <?= esc($direccionClinica) ?></span>
                <?php endif; ?>
                <?php if (!empty($telefonoClinica)): ?>
                    <span>| 📞 <?= esc($telefonoClinica) ?></span>
                <?php endif; ?>
                <?php if (!empty($emailClinica)): ?>
                    <span>| ✉️ <?= esc($emailClinica) ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="document-title">💵 COTIZACIÓN / PRESUPUESTO</div>
        
        <?php if ($medico): ?>
            <div class="doctor-section">
                <div class="doctor-title">PROFESIONAL MÉDICO RESPONSABLE</div>
                <div class="doctor-name">Dr./Dra. <?= esc($medico['nombre']) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contenido -->
    <div class="content">
        <!-- Información del Paciente y Cotización -->
        <div class="section-title">📋 INFORMACIÓN DE LA COTIZACIÓN</div>
        
        <div class="patient-box">
            <div class="info-row">
                <div class="info-field">
                    <div class="info-label">👤 Paciente</div>
                    <div class="info-value">
                        <?php if ($paciente): ?>
                            <?= esc($paciente['nombre'] . ' ' . ($paciente['primer_apellido'] ?? '') . ' ' . ($paciente['segundo_apellido'] ?? '')) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-field">
                    <div class="info-label">📅 Fecha Emisión</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($cotizacion['fecha_emision'])) ?></div>
                </div>
                <div class="info-field">
                    <div class="info-label">🔖 Cotización #</div>
                    <div class="info-value"><span class="badge"><?= esc($cotizacion['id']) ?></span></div>
                </div>
            </div>
        </div>
        
        <!-- Vigencia -->
        <div class="validity-box">
            <div class="validity-title">⏰ Esta cotización es válida hasta:</div>
            <div class="validity-date"><?= date('d/m/Y', strtotime($cotizacion['fecha_vigencia'])) ?></div>
        </div>
        
        <!-- Servicios -->
        <div class="section-title">🦷 SERVICIOS COTIZADOS</div>
        
        <?php if (!empty($detalles)): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 45%;">Servicio</th>
                        <th style="width: 18%;" class="text-right">Precio Unit.</th>
                        <th style="width: 12%;" class="text-center">Cant.</th>
                        <th style="width: 20%;" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($detalles as $det): ?>
                    <tr>
                        <td>
                            <span class="service-name"><?= esc($det['nombre']) ?></span>
                        </td>
                        <td class="text-right">$<?= number_format($det['precio_unitario'], 2) ?></td>
                        <td class="text-center"><span class="badge"><?= esc($det['cantidad']) ?></span></td>
                        <td class="text-right"><strong>$<?= number_format($det['subtotal'], 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3" class="text-right">TOTAL:</td>
                        <td class="text-right" style="font-size: 18px; color: #28a745;">$<?= number_format($cotizacion['total'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #999; font-style: italic;">No hay servicios registrados en esta cotización.</p>
        <?php endif; ?>
        
        <!-- Observaciones -->
        <?php if(!empty($cotizacion['observaciones'])): ?>
            <div class="section-title">📝 OBSERVACIONES</div>
            <div class="notes-section">
                <div class="notes-title">Notas y Condiciones:</div>
                <?= nl2br(esc($cotizacion['observaciones'])) ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Firma -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Firma del Profesional Médico</div>
        </div>
    </div>
    
    <!-- Pie de página -->
    <div class="footer">
        <p>Documento generado automáticamente por el sistema de gestión clínica Dental MX</p>
        <p>Cotización #<?= esc($cotizacion['id']) ?> | Fecha de emisión: <?= date('d/m/Y H:i') ?></p>
        <p style="margin-top: 10px; color: #666;">* Los precios pueden estar sujetos a cambios. Consulte con su médico para más detalles.</p>
    </div>
</body>
</html>
