<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('title') ?>Cambiar Contraseña<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ds-container ds-container--fluid">
    <div class="ds-row">
        <div class="ds-col-12">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">Cambiar Contraseña</h3>
                    <div class="ds-card__actions">
                        <a href="<?= base_url('/ajustes/perfil') ?>" class="ds-btn ds-btn--light ds-btn--sm">
                            <i class="fas fa-arrow-left"></i> Volver a Perfil
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
                    
                    <?= form_open('/ajustes/actualizar-contrasena') ?>
                        <div class="ds-row">
                            <div class="ds-col-md-6">
                                <div class="ds-form-group">
                                    <label for="contrasena_actual" class="ds-label">Contraseña Actual</label>
                                    <div class="ds-input-group">
                                        <input type="password" name="contrasena_actual" class="ds-input" id="contrasena_actual" required>
                                        <button type="button" class="ds-btn ds-btn--outline-secondary ds-btn--sm" onclick="togglePassword('contrasena_actual')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-row">
                            <div class="ds-col-md-6">
                                <div class="ds-form-group">
                                    <label for="contrasena_nueva" class="ds-label">Nueva Contraseña</label>
                                    <div class="ds-input-group">
                                        <input type="password" name="contrasena_nueva" class="ds-input" id="contrasena_nueva" required minlength="8">
                                        <button type="button" class="ds-btn ds-btn--outline-secondary ds-btn--sm" onclick="togglePassword('contrasena_nueva')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="ds-form-help">
                                        Mínimo 8 caracteres. Incluye letras, números y símbolos.
                                    </small>
                                </div>
                            </div>
                            <div class="ds-col-md-6">
                                <div class="ds-form-group">
                                    <label for="confirmar_contrasena" class="ds-label">Confirmar Nueva Contraseña</label>
                                    <div class="ds-input-group">
                                        <input type="password" name="confirmar_contrasena" class="ds-input" id="confirmar_contrasena" required>
                                        <button type="button" class="ds-btn ds-btn--outline-secondary ds-btn--sm" onclick="togglePassword('confirmar_contrasena')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Requisitos de contraseña -->
                        <div class="ds-row">
                            <div class="ds-col-md-12">
                                <div class="ds-alert ds-alert--info ds-alert--bordered">
                                    <h6 class="ds-m-0"><i class="fas fa-info-circle"></i> Requisitos de Contraseña:</h6>
                                    <ul class="ds-m-0 ds-mt-2">
                                        <li>Mínimo 8 caracteres</li>
                                        <li>Al menos una letra mayúscula</li>
                                        <li>Al menos una letra minúscula</li>
                                        <li>Al menos un número</li>
                                        <li>Al menos un carácter especial</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-row ds-mt-4">
                            <div class="ds-col-12 ds-flex ds-gap-3">
                                <button type="submit" class="ds-btn ds-btn--primary">
                                    <i class="fas fa-save"></i> Cambiar Contraseña
                                </button>
                                <a href="<?= base_url('/ajustes/perfil') ?>" class="ds-btn ds-btn--secondary">
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

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
<?= $this->endSection() ?>
