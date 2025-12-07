<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('title') ?>Ajustes<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ds-container ds-container--fluid">
    <div class="ds-row">
        <div class="ds-col-12">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">Configuración del Sistema</h3>
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
                    
                    <div class="ds-row">
                        <!-- Perfil de Usuario -->
                        <div class="ds-col-md-4">
                            <div class="ds-card ds-card--info">
                                <div class="ds-card__body">
                                    <div class="ds-flex ds-items-center ds-gap-3">
                                        <span class="ds-bg-info ds-text-white ds-rounded ds-p-3"><i class="fas fa-user"></i></span>
                                        <div class="ds-flex ds-flex-col">
                                            <span class="ds-text-sm ds-text-gray-600">Mi Perfil</span>
                                            <span class="ds-text-base ds-font-medium">Configuración Personal</span>
                                        </div>
                                    </div>
                                    <div class="ds-mt-3">
                                        <div class="ds-progress">
                                            <div class="ds-progress__bar ds-w-full"></div>
                                        </div>
                                    </div>
                                    <span class="ds-text-sm ds-text-gray-600 ds-mt-2 ds-d-block">
                                        Gestiona tu información personal y foto de perfil
                                    </span>
                                    <div class="ds-mt-3">
                                        <a href="<?= base_url('/ajustes/perfil') ?>" class="ds-btn ds-btn--info ds-btn--sm">
                                            <i class="fas fa-edit"></i> Configurar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Configuración de Clínica -->
                        <div class="ds-col-md-4">
                            <div class="ds-card ds-card--success">
                                <div class="ds-card__body">
                                    <div class="ds-flex ds-items-center ds-gap-3">
                                        <span class="ds-bg-success ds-text-white ds-rounded ds-p-3"><i class="fas fa-clinic-medical"></i></span>
                                        <div class="ds-flex ds-flex-col">
                                            <span class="ds-text-sm ds-text-gray-600">Clínica</span>
                                            <span class="ds-text-base ds-font-medium">Datos Generales</span>
                                        </div>
                                    </div>
                                    <div class="ds-mt-3">
                                        <div class="ds-progress">
                                            <div class="ds-progress__bar ds-progress__bar--success ds-w-full"></div>
                                        </div>
                                    </div>
                                    <span class="ds-text-sm ds-text-gray-600 ds-mt-2 ds-d-block">
                                        Configura los datos de tu clínica y logo
                                    </span>
                                    <div class="ds-mt-3">
                                        <a href="<?= base_url('/ajustes/clinica') ?>" class="ds-btn ds-btn--success ds-btn--sm">
                                            <i class="fas fa-cog"></i> Configurar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Preferencias -->
                        <div class="ds-col-md-4">
                            <div class="ds-card ds-card--warning">
                                <div class="ds-card__body">
                                    <div class="ds-flex ds-items-center ds-gap-3">
                                        <span class="ds-bg-warning ds-text-dark ds-rounded ds-p-3"><i class="fas fa-palette"></i></span>
                                        <div class="ds-flex ds-flex-col">
                                            <span class="ds-text-sm ds-text-gray-600">Preferencias</span>
                                            <span class="ds-text-base ds-font-medium">Personalización</span>
                                        </div>
                                    </div>
                                    <div class="ds-mt-3">
                                        <div class="ds-progress">
                                            <div class="ds-progress__bar ds-progress__bar--warning ds-w-full"></div>
                                        </div>
                                    </div>
                                    <span class="ds-text-sm ds-text-gray-600 ds-mt-2 ds-d-block">
                                        Personaliza tema, idioma y notificaciones
                                    </span>
                                    <div class="ds-mt-3">
                                        <a href="<?= base_url('/ajustes/preferencias') ?>" class="ds-btn ds-btn--warning ds-btn--sm">
                                            <i class="fas fa-sliders-h"></i> Configurar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
