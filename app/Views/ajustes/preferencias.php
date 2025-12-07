<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('title') ?>Preferencias<?= $this->endSection() ?>

<?php
// Valores por defecto para preferencias si no existe
$preferencias = $preferencias ?? [
    'tema' => 'light',
    'idioma' => 'es',
    'notificaciones_email' => true,
    'notificaciones_sistema' => true,
    'formato_fecha' => 'd/m/Y'
];
?>

<?= $this->section('content') ?>
<div class="ds-container ds-container--fluid">
    <div class="ds-row">
        <div class="ds-col-12">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">Preferencias de Usuario</h3>
                    <div class="ds-card__actions">
                        <a href="<?= base_url('/ajustes') ?>" class="ds-btn ds-btn--light ds-btn--sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="ds-card__body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="ds-alert ds-alert--success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="ds-alert ds-alert--danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?= form_open('/ajustes/actualizar-preferencias') ?>
                        <!-- Apariencia -->
                        <div class="ds-row">
                            <div class="ds-col-md-6">
                                <div class="ds-card">
                                    <div class="ds-card__header">
                                        <h5 class="ds-m-0 ds-text-base ds-font-semibold">
                                            <i class="fas fa-palette"></i> Apariencia
                                        </h5>
                                    </div>
                                    <div class="ds-card__body">
                                        <div class="ds-form-group">
                                            <label for="tema" class="ds-label">Tema Visual</label>
                                            <select name="tema" class="ds-input ds-select" id="tema">
                                                <option value="light" <?= ($preferencias['tema'] == 'light') ? 'selected' : '' ?>>
                                                    Claro
                                                </option>
                                                <option value="dark" <?= ($preferencias['tema'] == 'dark') ? 'selected' : '' ?>>
                                                    Oscuro
                                                </option>
                                                <option value="auto" <?= ($preferencias['tema'] == 'auto') ? 'selected' : '' ?>>
                                                    Automático (sistema)
                                                </option>
                                            </select>
                                            <small class="ds-form-help">
                                                El tema se aplicará automáticamente en tu próxima sesión.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Regional -->
                            <div class="ds-col-md-6">
                                <div class="ds-card">
                                    <div class="ds-card__header">
                                        <h5 class="ds-m-0 ds-text-base ds-font-semibold">
                                            <i class="fas fa-globe"></i> Regional
                                        </h5>
                                    </div>
                                    <div class="ds-card__body">
                                        <div class="ds-form-group">
                                            <label for="idioma" class="ds-label">Idioma</label>
                                            <select name="idioma" class="ds-input ds-select" id="idioma">
                                                <option value="es" <?= ($preferencias['idioma'] == 'es') ? 'selected' : '' ?>>
                                                    🇪🇸 Español
                                                </option>
                                                <option value="en" <?= ($preferencias['idioma'] == 'en') ? 'selected' : '' ?>>
                                                    🇺🇸 English
                                                </option>
                                            </select>
                                            <small class="ds-form-help">
                                                El idioma se aplicará en tu próxima sesión.
                                            </small>
                                        </div>
                                        
                                        <div class="ds-form-group">
                                            <label for="formato_fecha" class="ds-label">Formato de Fecha</label>
                                            <select name="formato_fecha" class="ds-input ds-select" id="formato_fecha">
                                                <option value="d/m/Y" <?= ($preferencias['formato_fecha'] == 'd/m/Y') ? 'selected' : '' ?>>
                                                    DD/MM/YYYY (31/12/2023)
                                                </option>
                                                <option value="m/d/Y" <?= ($preferencias['formato_fecha'] == 'm/d/Y') ? 'selected' : '' ?>>
                                                    MM/DD/YYYY (12/31/2023)
                                                </option>
                                                <option value="Y-m-d" <?= ($preferencias['formato_fecha'] == 'Y-m-d') ? 'selected' : '' ?>>
                                                    YYYY-MM-DD (2023-12-31)
                                                </option>
                                            </select>
                                            <small class="ds-form-help">
                                                Formato en que se mostrarán las fechas en todo el sistema.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notificaciones -->
                        <div class="ds-row ds-mt-4">
                            <div class="ds-col-12">
                                <div class="ds-card">
                                    <div class="ds-card__header">
                                        <h5 class="ds-m-0 ds-text-base ds-font-semibold">
                                            <i class="fas fa-bell"></i> Notificaciones
                                        </h5>
                                    </div>
                                    <div class="ds-card__body">
                                        <div class="ds-row">
                                            <div class="ds-col-md-6">
                                                <div class="ds-form-group">
                                                    <label class="ds-switch">
                                                        <input type="checkbox" name="notificaciones_email" id="notificaciones_email" <?= ($preferencias['notificaciones_email']) ? 'checked' : '' ?>>
                                                        <span class="ds-switch__track"></span>
                                                        <span class="ds-switch__label"><i class="fas fa-envelope"></i> Notificaciones por Email</span>
                                                    </label>
                                                    <p class="ds-text-gray-600 ds-ml-4">
                                                        Recibir notificaciones importantes en tu correo electrónico.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="ds-col-md-6">
                                                <div class="ds-form-group">
                                                    <label class="ds-switch">
                                                        <input type="checkbox" name="notificaciones_sistema" id="notificaciones_sistema" <?= ($preferencias['notificaciones_sistema']) ? 'checked' : '' ?>>
                                                        <span class="ds-switch__track"></span>
                                                        <span class="ds-switch__label"><i class="fas fa-desktop"></i> Notificaciones del Sistema</span>
                                                    </label>
                                                    <p class="ds-text-gray-600 ds-ml-4">
                                                        Mostrar notificaciones emergentes en el sistema.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-row ds-mt-4">
                            <div class="ds-col-12 ds-flex ds-gap-3">
                                <button type="submit" class="ds-btn ds-btn--primary">
                                    <i class="fas fa-save"></i> Guardar Preferencias
                                </button>
                                <a href="<?= base_url('/ajustes') ?>" class="ds-btn ds-btn--secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
