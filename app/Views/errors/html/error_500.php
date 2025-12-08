<?php
// No intentar conectar a la base de datos en página de error
// ya que el error podría ser de conexión a BD
$nombreClinica = 'Dental MX';
try {
    if (class_exists('\App\Models\ConfiguracionClinicaModel')) {
        $configModel = new \App\Models\ConfiguracionClinicaModel();
        $clinicaConfig = $configModel->getConfiguracion();
        $nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';
    }
} catch (\Throwable $e) {
    // Silenciosamente usar el nombre por defecto
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del Servidor | <?= esc($nombreClinica) ?></title>
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
            animation: pulse 2s infinite;
        }
        .ds-error-code {
            font-size: 6rem;
            font-weight: var(--font-weight-bold);
            color: var(--color-danger);
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
        .ds-error-details {
            background: var(--color-danger-light, #fff5f5);
            border-left: 4px solid var(--color-danger);
            border-radius: var(--border-radius-md);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
            text-align: left;
        }
        .ds-error-details p {
            margin: 0 0 var(--spacing-xs);
            color: var(--color-gray-700);
        }
        .ds-error-details code {
            background: var(--color-gray-100);
            padding: var(--spacing-2xs) var(--spacing-xs);
            border-radius: var(--border-radius-sm);
            font-family: monospace;
            font-size: var(--font-size-sm);
        }
        .ds-error-actions {
            display: flex;
            gap: var(--spacing-sm);
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: var(--spacing-lg);
        }
        .ds-error-info {
            background: var(--color-info-light, #e8f7fa);
            border-left: 4px solid var(--color-info, #17a2b8);
            border-radius: var(--border-radius-md);
            padding: var(--spacing-md);
            text-align: left;
        }
        .ds-error-info h5 {
            color: var(--color-info-dark, #0c5460);
            margin: 0 0 var(--spacing-xs);
            font-size: var(--font-size-md);
        }
        .ds-error-info p {
            color: var(--color-gray-700);
            margin: 0 0 var(--spacing-xs);
            font-size: var(--font-size-sm);
        }
        .ds-error-info p:last-child { margin-bottom: 0; }
        .ds-error-footer {
            margin-top: var(--spacing-lg);
            color: var(--color-gray-500);
            font-size: var(--font-size-sm);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
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
        <div class="ds-error-icon">🔧</div>
        <div class="ds-error-code">500</div>
        <h1 class="ds-error-title">Error del Servidor</h1>
        <p class="ds-error-description">
            Lo sentimos, ha ocurrido un error inesperado en nuestro servidor.
            Nuestro equipo técnico ha sido notificado y está trabajando en solucionarlo.
        </p>
        
        <div class="ds-error-details">
            <p><strong>Fecha y hora:</strong><br><?= date('d/m/Y H:i:s') ?></p>
            <p><strong>ID de error:</strong><br><code><?= uniqid('ERR_') ?></code></p>
        </div>
        
        <div class="ds-error-actions">
            <a href="javascript:location.reload()" class="ds-btn ds-btn--success">
                🔄 Reintentar
            </a>
            <a href="<?= base_url() ?>" class="ds-btn ds-btn--primary">
                🏠 Ir al Inicio
            </a>
        </div>
        
        <div class="ds-error-info">
            <h5>ℹ️ Información Adicional</h5>
            <p>Este error ha sido registrado en nuestro sistema.</p>
            <p>Si el problema persiste después de recargar la página, por favor contacta al soporte técnico.</p>
        </div>
        
        <div class="ds-error-footer">
            Referencia de error: <?= date('Y-m-d_H-i-s') ?>
        </div>
    </div>
    
    <?php if (ENVIRONMENT === 'development'): ?>
    <script>
        console.error('Error 500 - Información de depuración:');
        console.error('URL:', '<?= current_url() ?>');
        console.error('Timestamp:', '<?= date('Y-m-d H:i:s') ?>');
        <?php if (isset($_SERVER['HTTP_REFERER'])): ?>
        console.error('Referer:', '<?= $_SERVER['HTTP_REFERER'] ?>');
        <?php endif; ?>
    </script>
    <?php endif; ?>
</body>
</html>
