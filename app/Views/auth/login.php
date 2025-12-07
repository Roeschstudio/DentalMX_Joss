<?php
// Obtener configuración de la clínica
$configModel = new \App\Models\ConfiguracionClinicaModel();
$clinicaConfig = $configModel->getConfiguracion();
$nombreClinica = $clinicaConfig['nombre_clinica'] ?? 'Dental MX';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($nombreClinica) ?> - Sistema de Gestión Dental - Iniciar Sesión">
    <meta name="theme-color" content="#5ccdde">
    <title>Iniciar Sesión - <?= esc($nombreClinica) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    
    <!-- Design System CSS v2 -->
    <link rel="stylesheet" href="<?= base_url('css/design-system-v2.css') ?>">
    
    <style>
        /* Estilos específicos para la página de login */
        .ds-login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-body-bg) 100%);
            padding: var(--space-4);
        }
        
        .ds-login-container {
            width: 100%;
            max-width: 420px;
        }
        
        .ds-login-card {
            background: var(--color-white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
        }
        
        .ds-login-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            padding: var(--space-8) var(--space-6);
            text-align: center;
            color: var(--color-white);
        }
        
        .ds-login-logo {
            font-size: 3rem;
            margin-bottom: var(--space-2);
        }
        
        .ds-login-title {
            font-size: var(--font-size-2xl);
            font-weight: var(--font-weight-bold);
            margin: 0;
            color: var(--color-white);
        }
        
        .ds-login-subtitle {
            font-size: var(--font-size-sm);
            opacity: 0.9;
            margin-top: var(--space-1);
        }
        
        .ds-login-body {
            padding: var(--space-8) var(--space-6);
        }
        
        .ds-login-footer {
            padding: var(--space-4) var(--space-6);
            background: var(--color-gray-50);
            text-align: center;
            border-top: 1px solid var(--color-border);
        }
        
        .ds-login-footer p {
            margin: 0;
            font-size: var(--font-size-xs);
            color: var(--color-muted);
        }
    </style>
</head>
<body>
    <div class="ds-login-page">
        <div class="ds-login-container">
            <div class="ds-login-card">
                <!-- Header con Logo -->
                <div class="ds-login-header">
                    <div class="ds-login-logo">🦷</div>
                    <h1 class="ds-login-title"><?= esc($nombreClinica) ?></h1>
                    <p class="ds-login-subtitle">Sistema de Gestión Dental</p>
                </div>
                
                <!-- Cuerpo del formulario -->
                <div class="ds-login-body">
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="ds-alert ds-alert--danger ds-mb-4">
                            <span class="ds-alert__icon">❌</span>
                            <div class="ds-alert__content">
                                <p class="ds-alert__text"><?= session()->getFlashdata('error') ?></p>
                            </div>
                            <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="ds-alert ds-alert--success ds-mb-4">
                            <span class="ds-alert__icon">✅</span>
                            <div class="ds-alert__content">
                                <p class="ds-alert__text"><?= session()->getFlashdata('success') ?></p>
                            </div>
                            <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('/auth/login') ?>" method="post" id="loginForm">
                        <?= csrf_field() ?>
                        
                        <div class="ds-form-group">
                            <label for="email" class="ds-label ds-label--required">Correo Electrónico</label>
                            <div class="ds-input-wrapper">
                                <span class="ds-input-icon">📧</span>
                                <input type="email" 
                                       class="ds-input" 
                                       id="email" 
                                       name="email" 
                                       placeholder="correo@ejemplo.com"
                                       value="<?= old('email') ?>"
                                       required 
                                       autofocus>
                            </div>
                        </div>
                        
                        <div class="ds-form-group">
                            <label for="password" class="ds-label ds-label--required">Contraseña</label>
                            <div class="ds-input-wrapper">
                                <span class="ds-input-icon">🔒</span>
                                <input type="password" 
                                       class="ds-input" 
                                       id="password" 
                                       name="password" 
                                       placeholder="••••••••"
                                       required>
                            </div>
                        </div>
                        
                        <div class="ds-form-group ds-mt-6">
                            <button type="submit" class="ds-btn ds-btn--primary ds-btn--block ds-btn--lg">
                                <span class="ds-btn__icon ds-btn__icon--left">🔑</span>
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="ds-login-footer">
                    <p>© <?= date('Y') ?> <?= esc($nombreClinica) ?>. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Validación simple del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            
            if (!email.value.trim()) {
                e.preventDefault();
                email.focus();
                email.classList.add('ds-input--error');
                return;
            }
            
            if (!password.value.trim()) {
                e.preventDefault();
                password.focus();
                password.classList.add('ds-input--error');
                return;
            }
        });
        
        // Quitar error al escribir
        document.querySelectorAll('.ds-input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('ds-input--error');
            });
        });
    </script>
</body>
</html>
