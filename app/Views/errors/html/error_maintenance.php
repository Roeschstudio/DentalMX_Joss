<?php
$configModel = new \App\Models\ConfiguracionClinicaModel();
$clinicaConfig = $configModel->getConfiguracion();
$nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento | <?= esc($nombreClinica) ?></title>
    <link rel="stylesheet" href="<?= base_url('css/design-system-v2.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-gray-100) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
            margin: 0;
        }
        .ds-maintenance-container {
            background: var(--color-white);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-xl);
            text-align: center;
            max-width: 600px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }
        .ds-maintenance-icon {
            font-size: 5rem;
            margin-bottom: var(--spacing-md);
            animation: rotate 3s linear infinite;
            display: inline-block;
        }
        .ds-maintenance-title {
            font-size: var(--font-size-2xl);
            font-weight: var(--font-weight-semibold);
            color: var(--color-gray-800);
            margin-bottom: var(--spacing-sm);
        }
        .ds-maintenance-description {
            color: var(--color-gray-600);
            font-size: var(--font-size-lg);
            line-height: 1.6;
            margin-bottom: var(--spacing-lg);
        }
        .ds-progress-container {
            background: var(--color-gray-100);
            border-radius: var(--border-radius-md);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }
        .ds-progress-label {
            margin-bottom: var(--spacing-sm);
            font-weight: var(--font-weight-medium);
            color: var(--color-gray-700);
        }
        .ds-progress-bar {
            height: 10px;
            background: var(--color-gray-200);
            border-radius: var(--border-radius-full);
            overflow: hidden;
        }
        .ds-progress-fill {
            height: 100%;
            background: linear-gradient(45deg, var(--color-primary), var(--color-primary-dark));
            border-radius: var(--border-radius-full);
            animation: progressAnimation 2s ease-in-out infinite;
            width: 75%;
        }
        .ds-progress-time {
            margin-top: var(--spacing-sm);
            color: var(--color-gray-500);
            font-size: var(--font-size-sm);
        }
        .ds-info-box {
            background: var(--color-info-light, #e8f7fa);
            border-left: 4px solid var(--color-info, #17a2b8);
            border-radius: var(--border-radius-md);
            padding: var(--spacing-md);
            text-align: left;
        }
        .ds-info-box p {
            margin: 0;
            color: var(--color-info-dark, #0c5460);
        }
        .ds-maintenance-footer {
            margin-top: var(--spacing-lg);
            color: var(--color-gray-500);
            font-size: var(--font-size-sm);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes progressAnimation {
            0% { width: 0%; }
            50% { width: 75%; }
            100% { width: 100%; }
        }
        @media (max-width: 768px) {
            .ds-maintenance-container { padding: var(--spacing-lg); }
            .ds-maintenance-title { font-size: var(--font-size-xl); }
            .ds-maintenance-icon { font-size: 4rem; }
        }
    </style>
</head>
<body>
    <div class="ds-maintenance-container">
        <div class="ds-maintenance-icon">⚙️</div>
        <h1 class="ds-maintenance-title">Mantenimiento Programado</h1>
        <p class="ds-maintenance-description">
            Estamos realizando mejoras en nuestro sistema para ofrecerte un mejor servicio.
        </p>
        
        <div class="ds-progress-container">
            <p class="ds-progress-label">Progreso del mantenimiento:</p>
            <div class="ds-progress-bar">
                <div class="ds-progress-fill"></div>
            </div>
            <p class="ds-progress-time">Tiempo estimado: 30 minutos</p>
        </div>
        
        <div class="ds-info-box">
            <p>
                ℹ️ <strong>Información importante:</strong><br>
                El sistema estará disponible nuevamente aproximadamente a las <?= date('H:i', strtotime('+30 minutes')) ?>.
                Agradecemos tu paciencia y comprensión.
            </p>
        </div>
        
        <div class="ds-maintenance-footer">
            Para asistencia urgente, contacta al soporte técnico.
        </div>
    </div>
    
    <!-- Auto-refresh cada 5 minutos -->
    <script>
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
