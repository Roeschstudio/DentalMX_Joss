<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('title') ?>Mi Perfil<?= $this->endSection() ?>

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
                    <h3 class="ds-card__title">Mi Perfil</h3>
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
                    
                    <?= form_open_multipart('/ajustes/actualizar-perfil') ?>
                        <div class="ds-row">
                            <!-- Foto de perfil -->
                            <div class="ds-col-md-4">
                                <div class="ds-text-center">
                                    <h5 class="ds-text-base ds-font-semibold">Foto de Perfil</h5>
                                    <div class="ds-mb-3 ds-max-w-sm ds-mx-auto">
                                        <?php if (isset($usuario['foto_perfil']) && $usuario['foto_perfil']): ?>
                                            <img src="<?= base_url('uploads/perfiles/' . $usuario['foto_perfil']) ?>" 
                                                 class="ds-w-full ds-rounded ds-border ds-shadow-xs ds-profile-photo" 
                                                 alt="Foto de perfil">
                                        <?php else: ?>
                                            <img src="<?= base_url('assets/dist/img/default-avatar.png') ?>" 
                                                 class="ds-w-full ds-rounded ds-border ds-shadow-xs ds-profile-photo" 
                                                 alt="Foto de perfil">
                                        <?php endif; ?>
                                    </div>
                                    <div class="ds-file-input ds-mb-2">
                                        <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">
                                        <label class="ds-file-input__label" for="foto_perfil">Elegir foto</label>
                                    </div>
                                    <small class="ds-form-help">
                                        Formatos permitidos: JPEG, PNG, GIF. Máximo 2MB.
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Datos personales -->
                            <div class="ds-col-md-8">
                                <h5 class="ds-text-base ds-font-semibold">Datos Personales</h5>
                                <div class="ds-row">
                                    <div class="ds-col-md-6">
                                        <div class="ds-form-group">
                                            <label for="nombre" class="ds-label">Nombre</label>
                                            <input type="text" name="nombre" class="ds-input" 
                                                   id="nombre" value="<?= $usuario['nombre'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="ds-col-md-6">
                                        <div class="ds-form-group">
                                            <label for="email" class="ds-label">Email</label>
                                            <input type="email" name="email" class="ds-input" 
                                                   id="email" value="<?= $usuario['email'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ds-row">
                                    <div class="ds-col-md-6">
                                        <div class="ds-form-group">
                                            <label for="telefono" class="ds-label">Teléfono</label>
                                            <input type="tel" name="telefono" class="ds-input" 
                                                   id="telefono" value="<?= $usuario['telefono'] ?? '' ?>">
                                        </div>
                                    </div>
                                    <div class="ds-col-md-6">
                                        <div class="ds-form-group">
                                            <label for="direccion" class="ds-label">Dirección</label>
                                            <textarea name="direccion" class="ds-input ds-textarea" 
                                                      id="direccion" rows="3"><?= $usuario['direccion'] ?? '' ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-divider"></div>
                        
                        <!-- Preferencias básicas -->
                        <div class="ds-row">
                            <div class="ds-col-12">
                                <h5 class="ds-text-base ds-font-semibold">Preferencias Básicas</h5>
                                <div class="ds-row">
                                    <div class="ds-col-md-4">
                                        <div class="ds-form-group">
                                            <label for="tema" class="ds-label">Tema</label>
                                            <select name="tema" class="ds-input ds-select" id="tema">
                                                <option value="light" <?= ($preferencias['tema'] == 'light') ? 'selected' : '' ?>>
                                                    Claro
                                                </option>
                                                <option value="dark" <?= ($preferencias['tema'] == 'dark') ? 'selected' : '' ?>>
                                                    Oscuro
                                                </option>
                                                <option value="auto" <?= ($preferencias['tema'] == 'auto') ? 'selected' : '' ?>>
                                                    Automático
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ds-col-md-4">
                                        <div class="ds-form-group">
                                            <label for="idioma" class="ds-label">Idioma</label>
                                            <select name="idioma" class="ds-input ds-select" id="idioma">
                                                <option value="es" <?= ($preferencias['idioma'] == 'es') ? 'selected' : '' ?>>
                                                    Español
                                                </option>
                                                <option value="en" <?= ($preferencias['idioma'] == 'en') ? 'selected' : '' ?>>
                                                    English
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ds-col-md-4">
                                        <div class="ds-form-group">
                                            <label for="formato_fecha" class="ds-label">Formato de Fecha</label>
                                            <select name="formato_fecha" class="ds-input ds-select" id="formato_fecha">
                                                <option value="d/m/Y" <?= ($preferencias['formato_fecha'] == 'd/m/Y') ? 'selected' : '' ?>>
                                                    DD/MM/YYYY
                                                </option>
                                                <option value="m/d/Y" <?= ($preferencias['formato_fecha'] == 'm/d/Y') ? 'selected' : '' ?>>
                                                    MM/DD/YYYY
                                                </option>
                                                <option value="Y-m-d" <?= ($preferencias['formato_fecha'] == 'Y-m-d') ? 'selected' : '' ?>>
                                                    YYYY-MM-DD
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-divider"></div>
                        
                        <!-- Notificaciones -->
                        <div class="ds-row">
                            <div class="ds-col-12">
                                <h5 class="ds-text-base ds-font-semibold">Notificaciones</h5>
                                <div class="ds-row">
                                    <div class="ds-col-md-6">
                                        <label class="ds-switch">
                                            <input type="checkbox" name="notificaciones_email" id="notificaciones_email" <?= ($preferencias['notificaciones_email']) ? 'checked' : '' ?>>
                                            <span class="ds-switch__track"></span>
                                            <span class="ds-switch__label">Recibir notificaciones por email</span>
                                        </label>
                                    </div>
                                    <div class="ds-col-md-6">
                                        <label class="ds-switch">
                                            <input type="checkbox" name="notificaciones_sistema" id="notificaciones_sistema" <?= ($preferencias['notificaciones_sistema']) ? 'checked' : '' ?>>
                                            <span class="ds-switch__track"></span>
                                            <span class="ds-switch__label">Recibir notificaciones del sistema</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-row ds-mt-4">
                            <div class="ds-col-12 ds-flex ds-gap-3">
                                <button type="submit" class="ds-btn ds-btn--primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <a href="<?= base_url('/ajustes') ?>" class="ds-btn ds-btn--secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <a href="<?= base_url('/ajustes/cambiar-contrasena') ?>" class="ds-btn ds-btn--warning">
                                    <i class="fas fa-key"></i> Cambiar Contraseña
                                </a>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('foto_perfil').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const img = document.querySelector('.ds-profile-photo');
            if (img) img.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
    const fileName = file?.name || 'Elegir foto';
    const label = document.querySelector('label[for="foto_perfil"]');
    if (label) label.textContent = fileName;
});
</script>
<?= $this->endSection() ?>
