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
    <title><?= $error_title ?> | <?= esc($nombreClinica) ?></title>
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
        .ds-validation-container {
            background: var(--color-white);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-xl);
            max-width: 500px;
            width: 100%;
        }
        .ds-validation-icon {
            font-size: 3rem;
            text-align: center;
            margin-bottom: var(--spacing-md);
        }
        .ds-validation-title {
            font-size: var(--font-size-xl);
            font-weight: var(--font-weight-bold);
            color: var(--color-danger);
            text-align: center;
            margin-bottom: var(--spacing-sm);
        }
        .ds-validation-message {
            color: var(--color-gray-600);
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }
        .ds-validation-list {
            background: var(--color-danger-light, #fff5f5);
            border-left: 4px solid var(--color-danger);
            border-radius: var(--border-radius-md);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }
        .ds-validation-list ul {
            margin: 0;
            padding-left: var(--spacing-lg);
        }
        .ds-validation-list li {
            color: var(--color-gray-700);
            margin-bottom: var(--spacing-xs);
        }
        .ds-validation-actions {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="ds-validation-container">
        <div class="ds-validation-icon">⚠️</div>
        <h1 class="ds-validation-title"><?= $error_title ?></h1>
        <p class="ds-validation-message"><?= $error_message ?></p>
        
        <div class="ds-validation-list">
            <ul>
                <?php if (is_array($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><?= esc($errors) ?></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="ds-validation-actions">
            <button type="button" class="ds-btn ds-btn--secondary" onclick="history.back()">
                ← Volver
            </button>
        </div>
    </div>
</body>
</html>
