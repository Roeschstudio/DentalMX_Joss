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
    <title>Receta - <?= esc($receta['folio']) ?></title>
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
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
        }
        
        .clinic-name {
            font-size: 28px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 10px;
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
            border-left: 4px solid #0066cc;
        }
        
        .doctor-title {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .doctor-name {
            font-size: 14px;
            font-weight: bold;
            color: #0066cc;
        }
        
        .content {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: white;
            background-color: #0066cc;
            padding: 10px 15px;
            margin-top: 20px;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        .patient-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #66cc00;
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
            background-color: #0066cc;
            color: white;
        }
        
        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #0066cc;
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
            background-color: #f0f5ff;
        }
        
        .medicine-name {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        
        .medicine-substance {
            font-size: 11px;
            color: #666;
            font-style: italic;
        }
        
        .notes-section {
            background-color: #fffacd;
            padding: 15px;
            border-left: 4px solid #ffcc00;
            border-radius: 3px;
            margin: 20px 0;
            font-size: 12px;
            color: #333;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #cc8800;
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
            background-color: #e8f4f8;
            color: #0066cc;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
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
        
        <?php if ($medico): ?>
            <div class="doctor-section">
                <div class="doctor-title">PROFESIONAL MÉDICO RESPONSABLE</div>
                <div class="doctor-name">Dr./Dra. <?= esc($medico['nombre']) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contenido -->
    <div class="content">
        <!-- Información del Paciente y Receta -->
        <div class="section-title">📋 INFORMACIÓN DE LA RECETA</div>
        
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
                    <div class="info-label">📅 Fecha</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($receta['fecha'])) ?></div>
                </div>
                <div class="info-field">
                    <div class="info-label">⏰ Hora</div>
                    <div class="info-value"><?= date('H:i', strtotime($receta['fecha'])) ?></div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-field">
                    <div class="info-label">🔖 Folio</div>
                    <div class="info-value"><span class="badge"><?= esc($receta['folio']) ?></span></div>
                </div>
            </div>
        </div>
        
        <!-- Medicamentos Prescritos -->
        <div class="section-title">💊 MEDICAMENTOS PRESCRITOS</div>
        
        <?php if (!empty($detalles)): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 35%;">Medicamento</th>
                        <th style="width: 20%;">Dosis</th>
                        <th style="width: 20%;">Duración</th>
                        <th style="width: 15%; text-align: center;">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($detalles as $det): ?>
                    <tr>
                        <td>
                            <div class="medicine-name"><?= esc($det['nombre_comercial']) ?></div>
                            <div class="medicine-substance"><?= esc($det['sustancia_activa']) ?></div>
                        </td>
                        <td><?= esc($det['dosis']) ?></td>
                        <td><?= esc($det['duracion']) ?></td>
                        <td style="text-align: center;"><span class="badge"><?= esc($det['cantidad']) ?> unid.</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #999; font-style: italic;">No hay medicamentos registrados en esta receta.</p>
        <?php endif; ?>
        
        <!-- Notas Adicionales -->
        <?php if(!empty($receta['notas_adicionales'])): ?>
            <div class="section-title">📝 NOTAS ADICIONALES</div>
            <div class="notes-section">
                <div class="notes-title">Recomendaciones Especiales:</div>
                <?= nl2br(esc($receta['notas_adicionales'])) ?>
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
        <p>Folio: <?= esc($receta['folio']) ?> | Fecha de emisión: <?= date('d/m/Y H:i') ?></p>
    </div>
</body>
</html>
