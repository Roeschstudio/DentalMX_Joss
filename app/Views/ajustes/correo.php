<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('title') ?>Configuración de Correo<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ds-container ds-container--fluid">
    <div class="ds-row">
        <div class="ds-col-12">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">Configuración de Correo</h3>
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
                    
                    <?= form_open('/ajustes/actualizar-correo') ?>
                        <div class="ds-row">
                            <!-- Configuración SMTP -->
                            <div class="ds-col-md-6">
                                <div class="ds-card">
                                    <div class="ds-card__header">
                                        <h5 class="ds-m-0 ds-text-base ds-font-semibold">
                                            <i class="fas fa-server"></i> Configuración SMTP
                                        </h5>
                                    </div>
                                    <div class="ds-card__body">
                                        <div class="ds-form-group">
                                            <label for="mail_host" class="ds-label">Servidor SMTP</label>
                                            <input type="text" name="mail_host" class="ds-input" 
                                                   id="mail_host" value="<?= $configuracion['mail_host'] ?? '' ?>" required>
                                            <small class="ds-form-help">
                                                Ej: smtp.gmail.com, mail.proveedor.com
                                            </small>
                                        </div>
                                        
                                        <div class="ds-form-group">
                                            <label for="mail_port" class="ds-label">Puerto SMTP</label>
                                            <input type="number" name="mail_port" class="ds-input" 
                                                   id="mail_port" value="<?= $configuracion['mail_port'] ?? '587' ?>" required>
                                            <small class="ds-form-help">
                                                Común: 587 (TLS), 465 (SSL), 25 (sin cifrado)
                                            </small>
                                        </div>
                                        
                                        <div class="ds-form-group">
                                            <label for="mail_encryption" class="ds-label">Cifrado</label>
                                            <select name="mail_encryption" class="ds-input ds-select" id="mail_encryption">
                                                <option value="">Sin cifrado</option>
                                                <option value="tls" <?= ($configuracion['mail_encryption'] == 'tls') ? 'selected' : '' ?>>
                                                    TLS
                                                </option>
                                                <option value="ssl" <?= ($configuracion['mail_encryption'] == 'ssl') ? 'selected' : '' ?>>
                                                    SSL
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Autenticación -->
                            <div class="ds-col-md-6">
                                <div class="ds-card">
                                    <div class="ds-card__header">
                                        <h5 class="ds-m-0 ds-text-base ds-font-semibold">
                                            <i class="fas fa-key"></i> Autenticación
                                        </h5>
                                    </div>
                                    <div class="ds-card__body">
                                        <div class="ds-form-group">
                                            <label for="mail_username" class="ds-label">Usuario SMTP</label>
                                            <input type="text" name="mail_username" class="ds-input" 
                                                   id="mail_username" value="<?= $configuracion['mail_username'] ?? '' ?>" required>
                                            <small class="ds-form-help">
                                                Generalmente tu correo electrónico completo
                                            </small>
                                        </div>
                                        
                                        <div class="ds-form-group">
                                            <label for="mail_password" class="ds-label">Contraseña SMTP</label>
                                            <input type="password" name="mail_password" class="ds-input" 
                                                   id="mail_password" placeholder="Dejar en blanco para mantener actual">
                                            <small class="ds-form-help">
                                                Deja en blanco si no deseas cambiar la contraseña actual
                                            </small>
                                        </div>
                                        
                                        <div class="ds-form-group">
                                            <label for="mail_from_email" class="ds-label">Correo de Envío</label>
                                            <input type="email" name="mail_from_email" class="ds-input" 
                                                   id="mail_from_email" value="<?= $configuracion['mail_from_email'] ?? '' ?>" required>
                                            <small class="ds-form-help">
                                                Correo que aparecerá como remitente
                                            </small>
                                        </div>
                                        
                                        <div class="ds-form-group">
                                            <label for="mail_from_name" class="ds-label">Nombre de Envío</label>
                                            <input type="text" name="mail_from_name" class="ds-input" 
                                                   id="mail_from_name" value="<?= $configuracion['mail_from_name'] ?? '' ?>" required>
                                            <small class="ds-form-help">
                                                Nombre que aparecerá como remitente
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-row ds-mt-4">
                            <div class="ds-col-12 ds-flex ds-gap-3">
                                <button type="submit" class="ds-btn ds-btn--primary">
                                    <i class="fas fa-save"></i> Guardar Configuración
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
