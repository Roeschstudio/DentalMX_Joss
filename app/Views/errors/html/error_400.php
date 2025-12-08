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
    <title>400 - Solicitud Incorrecta | <?= esc($nombreClinica) ?></title>
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
            color: var(--color-warning);
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
        .ds-error-message {
            background: var(--color-gray-50);
            border-left: 4px solid var(--color-warning);
            border-radius: var(--border-radius-md);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
            text-align: left;
        }
        .ds-error-message code {
            background: var(--color-gray-100);
            padding: var(--spacing-2xs) var(--spacing-xs);
            border-radius: var(--border-radius-sm);
            font-family: monospace;
            font-size: var(--font-size-sm);
            display: block;
            white-space: normal;
            word-break: break-word;
        }
        .ds-error-actions {
            display: flex;
            gap: var(--spacing-sm);
            justify-content: center;
            flex-wrap: wrap;
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
        <div class="ds-error-icon">❌</div>
        <div class="ds-error-code">400</div>
        <h1 class="ds-error-title">Solicitud Incorrecta</h1>
        <p class="ds-error-description">
            La solicitud enviada no pudo ser procesada debido a un error en los datos.
        </p>
        
        <?php if (ENVIRONMENT !== 'production' && isset($message)): ?>
        <div class="ds-error-message">
            <code><?= nl2br(esc($message)) ?></code>
        </div>
        <?php else: ?>
        <div class="ds-error-message">
            <code>Lo sentimos, la solicitud contiene datos inválidos o malformados.</code>
        </div>
        <?php endif; ?>
        
        <div class="ds-error-actions">
            <a href="<?= base_url() ?>" class="ds-btn ds-btn--primary">
                🏠 Ir al Inicio
            </a>
            <a href="javascript:history.back()" class="ds-btn ds-btn--secondary">
                ← Volver Atrás
            </a>
        </div>
        
        <div class="ds-error-footer">
            Si el problema persiste, por favor contacta al administrador del sistema.
        </div>
    </div>
</body>
</html>
