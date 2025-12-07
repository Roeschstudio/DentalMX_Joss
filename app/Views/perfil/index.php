<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">👤 Mi Perfil</h1>
        <p class="ds-page__subtitle">Administra la información de tu cuenta</p>
    </div>

    <div class="ds-grid ds-grid--2">
        <!-- Información Personal -->
        <div class="ds-card">
            <div class="ds-card__header">
                <h2 class="ds-card__title">📋 Información Personal</h2>
            </div>
            <div class="ds-card__body">
                <?= form_open('/perfil/actualizar', ['class' => 'ds-form']) ?>
                    <div class="ds-form-group">
                        <label for="nombre" class="ds-label ds-label--required">Nombre</label>
                        <input type="text" class="ds-input" id="nombre" name="nombre" 
                               value="<?= esc($usuario['nombre'] ?? '') ?>" required>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="apellido" class="ds-label ds-label--required">Apellido</label>
                        <input type="text" class="ds-input" id="apellido" name="apellido" 
                               value="<?= esc($usuario['apellido'] ?? '') ?>" required>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="email" class="ds-label ds-label--required">Email</label>
                        <input type="email" class="ds-input" id="email" name="email" 
                               value="<?= esc($usuario['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="telefono" class="ds-label">Teléfono</label>
                        <input type="tel" class="ds-input" id="telefono" name="telefono" 
                               value="<?= esc($usuario['telefono'] ?? '') ?>">
                    </div>
                    
                    <div class="ds-form-group">
                        <label class="ds-label">Rol</label>
                        <input type="text" class="ds-input" value="<?= esc($usuario['rol'] ?? 'Doctor') ?>" disabled>
                    </div>
                    
                    <button type="submit" class="ds-btn ds-btn--primary">
                        💾 Guardar Cambios
                    </button>
                <?= form_close() ?>
            </div>
        </div>

        <!-- Cambiar Contraseña -->
        <div class="ds-card">
            <div class="ds-card__header">
                <h2 class="ds-card__title">🔐 Cambiar Contraseña</h2>
            </div>
            <div class="ds-card__body">
                <?= form_open('/perfil/cambiar-password', ['class' => 'ds-form']) ?>
                    <div class="ds-form-group">
                        <label for="password_actual" class="ds-label ds-label--required">Contraseña Actual</label>
                        <input type="password" class="ds-input" id="password_actual" name="password_actual" required>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="password_nuevo" class="ds-label ds-label--required">Nueva Contraseña</label>
                        <input type="password" class="ds-input" id="password_nuevo" name="password_nuevo" 
                               required minlength="6">
                        <small class="ds-text-muted">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="password_confirmar" class="ds-label ds-label--required">Confirmar Nueva Contraseña</label>
                        <input type="password" class="ds-input" id="password_confirmar" name="password_confirmar" required>
                    </div>
                    
                    <button type="submit" class="ds-btn ds-btn--warning">
                        🔑 Cambiar Contraseña
                    </button>
                <?= form_close() ?>
            </div>
        </div>
    </div>

    <!-- Información de la Cuenta -->
    <div class="ds-card ds-mt-4">
        <div class="ds-card__header">
            <h2 class="ds-card__title">ℹ️ Información de la Cuenta</h2>
        </div>
        <div class="ds-card__body">
            <div class="ds-grid ds-grid--3">
                <div class="ds-stat-card ds-stat-card--info">
                    <div class="ds-stat-card__content">
                        <div class="ds-stat-card__info">
                            <h3 class="ds-stat-card__title">Miembro desde</h3>
                            <p class="ds-stat-card__value"><?= date('d/m/Y', strtotime($usuario['created_at'] ?? 'now')) ?></p>
                        </div>
                        <div class="ds-stat-card__icon">📅</div>
                    </div>
                </div>
                
                <div class="ds-stat-card ds-stat-card--success">
                    <div class="ds-stat-card__content">
                        <div class="ds-stat-card__info">
                            <h3 class="ds-stat-card__title">Estado</h3>
                            <p class="ds-stat-card__value">Activo</p>
                        </div>
                        <div class="ds-stat-card__icon">✅</div>
                    </div>
                </div>
                
                <div class="ds-stat-card ds-stat-card--primary">
                    <div class="ds-stat-card__content">
                        <div class="ds-stat-card__info">
                            <h3 class="ds-stat-card__title">Último acceso</h3>
                            <p class="ds-stat-card__value"><?= date('d/m/Y H:i') ?></p>
                        </div>
                        <div class="ds-stat-card__icon">🕐</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
