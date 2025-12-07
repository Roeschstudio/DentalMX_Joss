<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha del Paciente - <?= esc($patient['nombre'] . ' ' . $patient['primer_apellido']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #5ccdde;
            padding-bottom: 15px;
        }
        .header-logo {
            display: table-cell;
            width: 60px;
            vertical-align: middle;
        }
        .header-logo img {
            width: 50px;
            height: 50px;
        }
        .header-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #5ccdde;
            margin-bottom: 3px;
        }
        .header-subtitle {
            font-size: 10px;
            color: #666;
        }
        .header-date {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        
        /* Patient Info Box */
        .patient-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .patient-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .patient-id {
            font-size: 12px;
            color: #666;
            font-weight: normal;
        }
        
        /* Info Grid */
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 5px 10px 5px 0;
            width: 50%;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            font-size: 10px;
            text-transform: uppercase;
        }
        .info-value {
            color: #333;
            margin-top: 2px;
        }
        
        /* Section */
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #5ccdde;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #5ccdde;
        }
        
        /* Stats Box */
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-box {
            display: table-cell;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            width: 25%;
        }
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #5ccdde;
        }
        .stat-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-top: 3px;
        }
        
        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }
        .table th {
            background: #5ccdde;
            color: white;
            font-weight: bold;
        }
        .table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        
        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-secondary {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            <div style="width: 50px; height: 50px; background: #5ccdde; border-radius: 50%; text-align: center; line-height: 50px; font-size: 24px;">🦷</div>
        </div>
        <div class="header-info">
            <div class="header-title">Dental MX</div>
            <div class="header-subtitle">Ficha del Paciente</div>
        </div>
        <div class="header-date">
            Generado: <?= esc($fecha_generacion); ?>
        </div>
    </div>
    
    <!-- Patient Info Box -->
    <div class="patient-box">
        <div class="patient-name">
            <?= esc($patient['nombre'] . ' ' . $patient['primer_apellido'] . ' ' . ($patient['segundo_apellido'] ?? '')); ?>
            <span class="patient-id">ID: #<?= $patient['id']; ?></span>
        </div>
        
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= esc($patient['email'] ?? 'No registrado'); ?></div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value"><?= esc($patient['telefono'] ?? $patient['celular'] ?? 'No registrado'); ?></div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Fecha de Nacimiento</div>
                    <div class="info-value">
                        <?php
                        if (!empty($patient['fecha_nacimiento'])) {
                            $fecha = new DateTime($patient['fecha_nacimiento']);
                            $hoy = new DateTime();
                            $edad = $hoy->diff($fecha)->y;
                            echo $fecha->format('d/m/Y') . ' (' . $edad . ' años)';
                        } else {
                            echo 'No registrada';
                        }
                        ?>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Nacionalidad</div>
                    <div class="info-value"><?= esc($patient['nacionalidad'] ?? 'No registrada'); ?></div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell" colspan="2">
                    <div class="info-label">Domicilio</div>
                    <div class="info-value"><?= esc($patient['domicilio'] ?? 'No registrado'); ?></div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Estado</div>
                    <div class="info-value">
                        <?php if (is_null($patient['deleted_at'] ?? null)): ?>
                            <span class="badge badge-success">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactivo</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Registrado</div>
                    <div class="info-value">
                        <?php
                        if (!empty($patient['created_at'])) {
                            $fechaRegistro = new DateTime($patient['created_at']);
                            echo $fechaRegistro->format('d/m/Y');
                        } else {
                            echo '-';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas -->
    <?php if (!empty($estadisticas)): ?>
    <div class="section">
        <div class="section-title">📊 Resumen de Actividad</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-number"><?= $estadisticas['total_actividades'] ?? 0; ?></div>
                <div class="stat-label">Actividades</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $estadisticas['total_citas'] ?? 0; ?></div>
                <div class="stat-label">Citas</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $estadisticas['total_tratamientos'] ?? 0; ?></div>
                <div class="stat-label">Tratamientos</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $estadisticas['total_recetas'] ?? 0; ?></div>
                <div class="stat-label">Recetas</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Últimas Actividades -->
    <?php if (!empty($actividades)): ?>
    <div class="section">
        <div class="section-title">📋 Últimas Actividades</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 20%;">Fecha</th>
                    <th style="width: 20%;">Tipo</th>
                    <th style="width: 60%;">Descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actividades as $actividad): ?>
                <tr>
                    <td>
                        <?php
                        $fecha = new DateTime($actividad['fecha_actividad'] ?? $actividad['created_at']);
                        echo $fecha->format('d/m/Y H:i');
                        ?>
                    </td>
                    <td><?= esc(ucfirst($actividad['tipo_actividad'] ?? 'Actividad')); ?></td>
                    <td><?= esc($actividad['descripcion'] ?? '-'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="section">
        <div class="section-title">📋 Historial de Actividades</div>
        <p style="color: #666; text-align: center; padding: 20px;">No hay actividades registradas para este paciente.</p>
    </div>
    <?php endif; ?>
    
    <!-- Footer -->
    <div class="footer">
        <p>Este documento fue generado automáticamente por Dental MX</p>
        <p>Fecha de generación: <?= esc($fecha_generacion); ?></p>
    </div>
</body>
</html>
