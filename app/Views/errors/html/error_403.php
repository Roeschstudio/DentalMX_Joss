<?php
$nombreClinica = 'Dental MX';
try {
    if (class_exists('\App\Models\ConfiguracionClinicaModel')) {
        $configModel = new \App\Models\ConfiguracionClinicaModel();
        $clinicaConfig = $configModel->getConfiguracion();
        $nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';
    }
} catch (\Throwable $e) {
    // Usar nombre por defecto
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Prohibido | <?= esc($nombreClinica) ?></title>
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
        .ds-error-container {
            background: var(--color-white);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-xl);
            text-align: center;
            max-width: 600px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }
        .ds-error-icon {
            font-size: 4rem;
            margin-bottom: var(--spacing-md);
        }
        .ds-error-code {
            font-size: 6rem;
            font-weight: var(--font-weight-bold);
            color: #fd7e14;
            line-height: 1;
            margin-bottom: var(--spacing-sm);
        }
        .ds-error-title {
            font-size: var(--font-size-2xl);
            font-weight: var(--font-weight-semibold);
            color: var(--color-gray-800);
            margin-bottom: var(--spacing-sm);
        }
        .ds-error-description {
            color: var(--color-gray-600);
            font-size: var(--font-size-md);
            line-height: 1.6;
            margin-bottom: var(--spacing-lg);
        }
        .ds-warning-box {
            background: #fff3cd;
            border-left: 4px solid #fd7e14;
            border-radius: var(--border-radius-md);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
            text-align: left;
        }
        .ds-warning-box h5 {
            color: #856404;
            margin: 0 0 var(--spacing-sm);
            font-size: var(--font-size-md);
        }
        .ds-warning-box p {
            margin: 0 0 var(--spacing-xs);
            color: var(--color-gray-700);
        }
        .ds-warning-box code {
            background: var(--color-gray-100);
            padding: var(--spacing-2xs) var(--spacing-xs);
            border-radius: var(--border-radius-sm);
            font-family: monospace;
            font-size: var(--font-size-sm);
            word-break: break-all;
        }
        .ds-error-actions {
            display: flex;
            gap: var(--spacing-sm);
            justify-content: center;
            flex-wrap: wrap;
        }
        .ds-btn--orange {
            background: #fd7e14;
            color: white;
        }
        .ds-btn--orange:hover {
            background: #e8590c;
        }
        .ds-error-footer {
            margin-top: var(--spacing-lg);
            color: var(--color-gray-500);
            font-size: var(--font-size-sm);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .ds-error-container { padding: var(--spacing-lg); }
            .ds-error-code { font-size: 4rem; }
            .ds-error-title { font-size: var(--font-size-xl); }
        }
    </style>
</head>
<body>
    <div class="ds-error-container">
        <div class="ds-error-icon">🛡️</div>
        <div class="ds-error-code">403</div>
        <h1 class="ds-error-title">Acceso Prohibido</h1>
        <p class="ds-error-description">
            Lo sentimos, no tienes permisos para acceder a esta página o recurso.
        </p>
        
        <div class="ds-warning-box">
            <h5>⚠️ Información de Acceso</h5>
            <p><strong>Fecha y hora:</strong><br><?= date('d/m/Y H:i:s') ?></p>
            <p><strong>URL solicitada:</strong><br><code><?= esc(current_url()) ?></code></p>
        </div>
        
        <div class="ds-error-actions">
            <a href="<?= base_url() ?>" class="ds-btn ds-btn--primary">
                🏠 Ir al Inicio
            </a>
            <a href="<?= base_url('/login') ?>" class="ds-btn ds-btn--orange">
                🔐 Iniciar Sesión
            </a>
        </div>
        
        <div class="ds-error-footer">
            Si crees que esto es un error, contacta al administrador del sistema.
        </div>
    </div>
</body>
</html>
