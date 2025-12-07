<?php
/**
 * Componente Footer - Design System
 * 
 * Variables disponibles:
 * - $showVersion: bool - Mostrar versión del sistema
 */
$showVersion = $showVersion ?? true;

// Obtener configuración de la clínica para el nombre dinámico
$configModel = new \App\Models\ConfiguracionClinicaModel();
$clinicaConfig = $configModel->getConfiguracion();
$nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';
?>
<footer class="ds-footer">
    <div class="ds-footer__content">
        <div class="ds-footer__left">
            <p class="ds-footer__text">
                © <?= date('Y') ?> <strong><?= esc($nombreClinica) ?></strong> - Sistema de Gestión Dental
            </p>
        </div>
        <div class="ds-footer__center">
            <a href="https://roeschstudio.com" target="_blank" rel="noopener noreferrer" class="ds-footer__badge">
                <span class="ds-badge ds-badge--dark">by<strong>RoeschStudio</strong></span>
            </a>
        </div>
        <div class="ds-footer__right">
            <?php if ($showVersion): ?>
                <span class="ds-footer__version">v2.0.0</span>
            <?php endif; ?>
            <a href="<?= base_url('/ayuda') ?>" class="ds-footer__link">
                <span>❓</span> Ayuda
            </a>
        </div>
    </div>
</footer>
