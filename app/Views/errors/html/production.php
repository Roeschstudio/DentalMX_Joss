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
    <meta name="robots" content="noindex">
    <title>Error | <?= esc($nombreClinica) ?></title>
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
            max-width: 500px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }
        .ds-error-icon {
            font-size: 4rem;
            margin-bottom: var(--spacing-md);
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
        .ds-error-actions {
            display: flex;
            gap: var(--spacing-sm);
            justify-content: center;
            flex-wrap: wrap;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .ds-error-container { padding: var(--spacing-lg); }
            .ds-error-title { font-size: var(--font-size-xl); }
        }
    </style>
</head>
<body>
    <div class="ds-error-container">
        <div class="ds-error-icon">😕</div>
        <h1 class="ds-error-title">¡Vaya!</h1>
        <p class="ds-error-description">
            Algo salió mal. Por favor, inténtalo de nuevo más tarde.
        </p>
        
        <div class="ds-error-actions">
            <a href="<?= base_url() ?>" class="ds-btn ds-btn--primary">
                🏠 Ir al Inicio
            </a>
        </div>
    </div>
</body>
</html>
