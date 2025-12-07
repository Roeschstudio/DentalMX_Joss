<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('title') ?>Configuración de Clínica<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ds-container ds-container--fluid">
    <div class="ds-row">
        <div class="ds-col-12">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">Configuración de Clínica</h3>
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
                    
                    <?= form_open_multipart('/ajustes/actualizar-clinica') ?>
                        <div class="ds-row">
                            <!-- Logo de clínica -->
                            <div class="ds-col-md-4">
                                <div class="ds-text-center">
                                    <h5 class="ds-text-base ds-font-semibold">Logo de la Clínica</h5>
                                    <div class="ds-mb-3 ds-max-w-sm ds-mx-auto">
                                        <?php if (isset($configuracion['logo']) && $configuracion['logo']): ?>
                                            <img src="<?= base_url('uploads/logos/' . $configuracion['logo']) ?>" 
                                                 class="ds-w-full ds-rounded ds-border ds-shadow-xs ds-clinic-logo" 
                                                 alt="Logo de clínica">
                                        <?php else: ?>
                                            <img src="<?= base_url('assets/dist/img/default-logo.png') ?>" 
                                                 class="ds-w-full ds-rounded ds-border ds-shadow-xs ds-clinic-logo" 
                                                 alt="Logo de clínica">
                                        <?php endif; ?>
                                    </div>
                                    <div class="ds-file-input ds-mb-2">
                                        <input type="file" name="logo" id="logo" accept="image/*">
                                        <label class="ds-file-input__label" for="logo">Elegir logo</label>
                                    </div>
                                    <small class="ds-form-help">
                                        Formatos permitidos: JPEG, PNG, GIF. Tamaño recomendado: 250x150px.
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Datos de la clínica -->
                            <div class="ds-col-md-8">
                                <h5 class="ds-text-base ds-font-semibold">Información de la Clínica</h5>
                                <div class="ds-row">
                                    <div class="ds-col-md-12">
                                        <div class="ds-form-group">
                                            <label for="nombre_clinica" class="ds-label">Nombre de la Clínica</label>
                                            <input type="text" name="nombre_clinica" class="ds-input" 
                                                   id="nombre_clinica" value="<?= $configuracion['nombre_clinica'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ds-row">
                                    <div class="ds-col-md-6">
                                        <div class="ds-form-group">
                                            <label for="telefono" class="ds-label">Teléfono</label>
                                            <input type="tel" name="telefono" class="ds-input" 
                                                   id="telefono" value="<?= $configuracion['telefono'] ?? '' ?>">
                                        </div>
                                    </div>
                                    <div class="ds-col-md-6">
                                        <div class="ds-form-group">
                                            <label for="email" class="ds-label">Email</label>
                                            <input type="email" name="email" class="ds-input" 
                                                   id="email" value="<?= $configuracion['email'] ?? '' ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ds-row">
                                    <div class="ds-col-md-12">
                                        <div class="ds-form-group">
                                            <label for="direccion" class="ds-label">Dirección</label>
                                            <textarea name="direccion" class="ds-input ds-textarea" 
                                                      id="direccion" rows="3"><?= $configuracion['direccion'] ?? '' ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ds-row">
                                    <div class="ds-col-md-12">
                                        <div class="ds-form-group">
                                            <label for="horario_atencion" class="ds-label">Horario de Atención</label>
                                            <input type="text" name="horario_atencion" class="ds-input" 
                                                   id="horario_atencion" value="<?= $configuracion['horario_atencion'] ?? '' ?>"
                                                   placeholder="Ej: Lunes a Viernes 9:00-18:00, Sábados 9:00-14:00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-divider"></div>
                        
                        <!-- Configuración de presupuestos -->
                        <div class="ds-row">
                            <div class="ds-col-12">
                                <h5 class="ds-text-base ds-font-semibold">Configuración de Presupuestos</h5>
                                <div class="ds-row">
                                    <div class="ds-col-md-6">
                                        <div class="ds-form-group">
                                            <label for="vigencia_presupuestos" class="ds-label">Vigencia de Presupuestos (días)</label>
                                            <input type="number" name="vigencia_presupuestos" class="ds-input" 
                                                   id="vigencia_presupuestos" value="<?= $configuracion['vigencia_presupuestos'] ?>" 
                                                   min="1" max="365" required>
                                            <small class="ds-form-help">
                                                Días de vigencia por defecto para nuevos presupuestos.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="ds-col-md-6">
                                        <div class="ds-form-group">
                                            <label for="mensaje_bienvenida" class="ds-label">Mensaje de Bienvenida</label>
                                            <textarea name="mensaje_bienvenida" class="ds-input ds-textarea" 
                                                      id="mensaje_bienvenida" rows="3" 
                                                      placeholder="Mensaje que aparecerá en el dashboard"><?= $configuracion['mensaje_bienvenida'] ?? '' ?></textarea>
                                            <small class="ds-form-help">
                                                Mensaje personalizado para los usuarios al iniciar sesión.
                                            </small>
                                        </div>
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
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const img = document.querySelector('.ds-clinic-logo');
            if (img) img.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
    const fileName = file?.name || 'Elegir logo';
    const label = document.querySelector('label[for="logo"]');
    if (label) label.textContent = fileName;
});
</script>
<?= $this->endSection() ?>
