<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#5ccdde">
    <title><?= $error_code ?> - <?= $error_title ?></title>
    
    <!-- Design System CSS -->
    <link rel="stylesheet" href="<?= base_url('css/design-system-v2.css') ?>">
    
    <style>
        .ds-error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-body-bg) 100%);
            padding: var(--space-4);
        }
        .ds-error-container {
            background: var(--color-white);
            border-radius: var(--radius-xl);
            padding: var(--space-10);
            box-shadow: var(--shadow-xl);
            text-align: center;
            max-width: 500px;
            animation: fadeInUp 0.6s ease-out;
        }
        .ds-error-code {
            font-size: 6rem;
            font-weight: var(--font-weight-bold);
            color: var(--color-danger);
            margin-bottom: var(--space-2);
        }
        .ds-error-icon {
            font-size: 4rem;
            margin-bottom: var(--space-4);
        }
        .ds-error-title {
            font-size: var(--font-size-2xl);
            font-weight: var(--font-weight-semibold);
            color: var(--color-gray-800);
            margin-bottom: var(--space-4);
        }
        .ds-error-message {
            color: var(--color-muted);
            margin-bottom: var(--space-6);
            line-height: var(--line-height-relaxed);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="ds-error-page">
        <div class="ds-error-container">
            <div class="ds-error-icon">⚠️</div>
            <div class="ds-error-code"><?= $error_code ?></div>
            <h1 class="ds-error-title"><?= $error_title ?></h1>
            <p class="ds-error-message"><?= $error_message ?></p>
            
            <?php if (isset($show_back_button) && $show_back_button): ?>
                <a href="<?= $back_url ?>" class="ds-btn ds-btn--primary ds-btn--lg">
                    <span class="ds-btn__icon ds-btn__icon--left">🏠</span>
                    Volver al Inicio
                </a>
            <?php endif; ?>
            
            <div class="ds-mt-6">
                <small class="ds-text-muted">
                    Si el problema persiste, contacta al administrador del sistema.
                </small>
            </div>
        </div>
    </div>
</body>
</html>
