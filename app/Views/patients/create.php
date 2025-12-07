<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-content">
    <!-- Page Header -->
    <div class="ds-page-header">
        <div>
            <h1 class="ds-page-title"><?= esc($pageTitle); ?></h1>
            <p class="ds-page-subtitle">Complete el formulario para registrar un nuevo paciente</p>
        </div>
        <div class="ds-page-actions">
            <a href="<?= base_url('/pacientes'); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">⬅️</span>
                Volver al Listado
            </a>
        </div>
    </div>

    <!-- Mensajes Flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert--success ds-fade-in">
            <span class="ds-alert__icon">✅</span>
            <div class="ds-alert__content">
                <p class="ds-alert__text"><?= session()->getFlashdata('success'); ?></p>
            </div>
            <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert--danger ds-fade-in">
            <span class="ds-alert__icon">❌</span>
            <div class="ds-alert__content">
                <p class="ds-alert__text"><?= session()->getFlashdata('error'); ?></p>
            </div>
            <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
        </div>
    <?php endif; ?>

    <!-- Formulario de Nuevo Paciente -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h3 class="ds-card__title">Información del Paciente</h3>
        </div>
        <div class="ds-card__body">
            <?= form_open('/pacientes', ['id' => 'patientForm', 'class' => 'needs-validation', 'novalidate']); ?>
                 
                <!-- Mensajes de error de validación -->
                <?php if (session()->getFlashdata('validation')): ?>
                    <div class="ds-alert ds-alert--danger ds-fade-in">
                        <span class="ds-alert__icon">❌</span>
                        <div class="ds-alert__content">
                            <?= session()->getFlashdata('validation')->listErrors(); ?>
                        </div>
                        <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
                    </div>
                <?php endif; ?>

                <div class="ds-grid ds-grid--2">
                    <!-- Columna Izquierda -->
                    <div>
                        <div class="ds-form-group">
                            <label for="nombre" class="ds-label ds-label--required">Nombre</label>
                            <input type="text" class="ds-input" id="nombre" name="nombre"
                                   value="<?= old('nombre'); ?>" required maxlength="100"
                                   placeholder="Ingrese el nombre del paciente">
                            <div class="ds-form-error">
                                Por favor, ingrese un nombre válido (mínimo 2 caracteres).
                            </div>
                        </div>

                        <div class="ds-form-group">
                            <label for="primer_apellido" class="ds-label ds-label--required">Primer Apellido</label>
                            <input type="text" class="ds-input" id="primer_apellido" name="primer_apellido"
                                   value="<?= old('primer_apellido'); ?>" required maxlength="100"
                                   placeholder="Ingrese el primer apellido del paciente">
                            <div class="ds-form-error">
                                Por favor, ingrese un apellido válido (mínimo 2 caracteres).
                            </div>
                        </div>

                        <div class="ds-form-group">
                            <label for="segundo_apellido" class="ds-label">Segundo Apellido</label>
                            <input type="text" class="ds-input" id="segundo_apellido" name="segundo_apellido"
                                   value="<?= old('segundo_apellido'); ?>" maxlength="100"
                                   placeholder="Ingrese el segundo apellido del paciente (opcional)">
                        </div>

                        <div class="ds-form-group">
                            <label for="email" class="ds-label">Email</label>
                            <input type="email" class="ds-input" id="email" name="email"
                                   value="<?= old('email'); ?>" maxlength="100"
                                   placeholder="correo@ejemplo.com">
                            <div class="ds-form-error">
                                Por favor, ingrese un email válido.
                            </div>
                        </div>

                        <div class="ds-form-group">
                            <label for="telefono" class="ds-label">Teléfono</label>
                            <input type="tel" class="ds-input" id="telefono" name="telefono"
                                   value="<?= old('telefono'); ?>" maxlength="20"
                                   placeholder="+52 123 456 7890">
                            <div class="ds-form-error">
                                Por favor, ingrese un teléfono válido.
                            </div>
                        </div>

                        <div class="ds-form-group">
                            <label for="celular" class="ds-label">Celular</label>
                            <input type="tel" class="ds-input" id="celular" name="celular"
                                   value="<?= old('celular'); ?>" maxlength="20"
                                   placeholder="+52 123 456 7890">
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div>
                        <div class="ds-form-group">
                            <label for="fecha_nacimiento" class="ds-label ds-label--required">Fecha de Nacimiento</label>
                            <input type="date" class="ds-input" id="fecha_nacimiento" name="fecha_nacimiento"
                                   value="<?= old('fecha_nacimiento'); ?>" required
                                   max="<?= date('Y-m-d'); ?>">
                            <div class="ds-form-error">
                                Por favor, ingrese una fecha válida.
                            </div>
                        </div>

                        <div class="ds-form-group">
                            <label for="nacionalidad" class="ds-label">Nacionalidad</label>
                            <input type="text" class="ds-input" id="nacionalidad" name="nacionalidad"
                                   value="<?= old('nacionalidad', 'Mexicana'); ?>" maxlength="50"
                                   placeholder="Ej: Mexicana">
                        </div>

                        <div class="ds-form-group">
                            <label for="domicilio" class="ds-label">Domicilio</label>
                            <textarea class="ds-input" id="domicilio" name="domicilio" rows="3"
                                      maxlength="255" placeholder="Ingrese la dirección completa"><?= old('domicilio'); ?></textarea>
                            <div class="ds-form-error">
                                El domicilio es demasiado largo (máximo 255 caracteres).
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="ds-card__footer">
                    <div class="ds-flex ds-justify-end ds-gap-3">
                        <button type="submit" class="ds-btn ds-btn--primary">
                            <span class="ds-btn__icon ds-btn__icon--left">💾</span>
                            Guardar Paciente
                        </button>
                        <a href="<?= base_url('/pacientes'); ?>" class="ds-btn ds-btn--secondary">
                            <span class="ds-btn__icon ds-btn__icon--left">❌</span>
                            Cancelar
                        </a>
                    </div>
                </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    var form = document.getElementById('patientForm');
    form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    // Formateo de teléfono
    ['telefono', 'celular'].forEach(function(id) {
        var input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function(e) {
                var value = e.target.value.replace(/\D/g, '');
                var formattedValue = '';
                
                if (value.length > 0) {
                    if (value.length <= 3) {
                        formattedValue = value;
                    } else if (value.length <= 6) {
                        formattedValue = value.slice(0, 3) + ' ' + value.slice(3);
                    } else if (value.length <= 10) {
                        formattedValue = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
                    } else {
                        formattedValue = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6, 10);
                    }
                }
                
                e.target.value = formattedValue;
            });
        }
    });

    // Validación de email en tiempo real
    document.getElementById('email').addEventListener('blur', function() {
        var email = this.value;
        if (email && !validateEmail(email)) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (email) {
            this.classList.add('is-valid');
            this.classList.remove('is-invalid');
        }
    });

    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
</script>
<?= $this->endSection(); ?>