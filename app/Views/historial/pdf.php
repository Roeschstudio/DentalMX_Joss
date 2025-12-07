<?php
// Obtener configuración de la clínica
$configModel = new \App\Models\ConfiguracionClinicaModel();
$clinicaConfig = $configModel->getConfiguracion();
$nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial del Paciente - <?= esc($paciente['nombre'] . ' ' . $paciente['primer_apellido']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #5ccdde;
        }
        
        .header h1 {
            font-size: 24px;
            color: #5ccdde;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
        }
        
        .patient-info {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .patient-info h2 {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .patient-info-grid {
            display: table;
            width: 100%;
        }
        
        .patient-info-row {
            display: table-row;
        }
        
        .patient-info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px;
            width: 120px;
        }
        
        .patient-info-value {
            display: table-cell;
            padding: 5px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            color: #5ccdde;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .activity-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .activity-table th,
        .activity-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .activity-table th {
            background-color: #5ccdde;
            color: white;
            font-weight: bold;
        }
        
        .activity-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-cita { background-color: #e3f2fd; color: #1565c0; }
        .badge-receta { background-color: #e8f5e9; color: #2e7d32; }
        .badge-presupuesto { background-color: #fff3e0; color: #ef6c00; }
        .badge-cotizacion { background-color: #f3e5f5; color: #7b1fa2; }
        .badge-nota_evolucion { background-color: #e0f7fa; color: #00838f; }
        .badge-tratamiento { background-color: #fce4ec; color: #c2185b; }
        .badge-pago { background-color: #e8f5e9; color: #2e7d32; }
        .badge-odontograma { background-color: #e3f2fd; color: #1565c0; }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        
        .summary-box {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .summary-box h3 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #1565c0;
        }
        
        .summary-stats {
            display: table;
            width: 100%;
        }
        
        .summary-stat {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }
        
        .summary-stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #1565c0;
        }
        
        .summary-stat-label {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🦷 <?= esc($nombreClinica) ?></h1>
        <p>Historial de Actividades del Paciente</p>
        <p>Exportado el: <?= $fecha_exportacion; ?></p>
    </div>

    <div class="patient-info">
        <h2>👤 Información del Paciente</h2>
        <div class="patient-info-grid">
            <div class="patient-info-row">
                <span class="patient-info-label">Nombre:</span>
                <span class="patient-info-value"><?= esc($paciente['nombre'] . ' ' . $paciente['primer_apellido'] . ' ' . ($paciente['segundo_apellido'] ?? '')); ?></span>
            </div>
            <div class="patient-info-row">
                <span class="patient-info-label">ID:</span>
                <span class="patient-info-value"><?= $paciente['id']; ?></span>
            </div>
            <?php if (!empty($paciente['telefono'])): ?>
            <div class="patient-info-row">
                <span class="patient-info-label">Teléfono:</span>
                <span class="patient-info-value"><?= esc($paciente['telefono']); ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($paciente['email'])): ?>
            <div class="patient-info-row">
                <span class="patient-info-label">Email:</span>
                <span class="patient-info-value"><?= esc($paciente['email']); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="summary-box">
        <h3>📊 Resumen</h3>
        <div class="summary-stats">
            <div class="summary-stat">
                <div class="summary-stat-value"><?= count($actividades); ?></div>
                <div class="summary-stat-label">Total Actividades</div>
            </div>
            <?php
            $porTipo = [];
            foreach ($actividades as $act) {
                $tipo = $act['tipo_actividad'];
                if (!isset($porTipo[$tipo])) {
                    $porTipo[$tipo] = 0;
                }
                $porTipo[$tipo]++;
            }
            ?>
            <?php foreach (array_slice($porTipo, 0, 4, true) as $tipo => $count): ?>
            <div class="summary-stat">
                <div class="summary-stat-value"><?= $count; ?></div>
                <div class="summary-stat-label"><?= $tipos_actividad[$tipo]['label'] ?? ucfirst($tipo); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">📅 Historial de Actividades</h2>
        
        <?php if (empty($actividades)): ?>
        <p class="empty-message">No hay actividades registradas para este paciente.</p>
        <?php else: ?>
        <table class="activity-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Fecha</th>
                    <th style="width: 100px;">Tipo</th>
                    <th>Descripción</th>
                    <th style="width: 120px;">Médico</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actividades as $actividad): 
                    $tipoConfig = $tipos_actividad[$actividad['tipo_actividad']] ?? ['label' => 'Otro', 'icon' => '📌'];
                ?>
                <tr>
                    <td>
                        <?= date('d/m/Y', strtotime($actividad['fecha_actividad'])); ?><br>
                        <small><?= date('H:i', strtotime($actividad['fecha_actividad'])); ?></small>
                    </td>
                    <td>
                        <span class="badge badge-<?= $actividad['tipo_actividad']; ?>">
                            <?= $tipoConfig['label']; ?>
                        </span>
                    </td>
                    <td><?= esc($actividad['descripcion'] ?? 'Sin descripción'); ?></td>
                    <td><?= esc(($actividad['medico_nombre'] ?? '') . ' ' . ($actividad['medico_apellido'] ?? '')); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Documento generado automáticamente por <?= esc($nombreClinica) ?></p>
        <p>Este documento es para uso interno y confidencial</p>
        <p>© <?= date('Y'); ?> <?= esc($nombreClinica) ?> - Todos los derechos reservados</p>
    </div>
</body>
</html>
