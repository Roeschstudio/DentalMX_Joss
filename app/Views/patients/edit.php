<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-content">
    <!-- Page Header -->
    <div class="ds-page-header">
        <div>
            <h1 class="ds-page-title"><?= esc($pageTitle ?? 'Editar Paciente'); ?></h1>
            <p class="ds-page-subtitle">Modificar información del paciente</p>
        </div>
        <div class="ds-page-actions">
            <a href="<?= base_url('/pacientes/' . $patient['id']); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">👁️</span>
                Ver Detalles
            </a>
            <a href="<?= base_url('/pacientes'); ?>" class="ds-btn ds-btn--outline">
                <span class="ds-btn__icon ds-btn__icon--left">⬅️</span>
                Volver al Listado
            </a>
        </div>
    </div>

    <!-- Formulario de Edición de Paciente -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h2 class="ds-card__title">Editar Información del Paciente</h2>
        </div>
        <div class="ds-card__body">
            <?= form_open('/pacientes/' . $patient['id'] . '/actualizar', ['id' => 'patientForm', 'class' => 'ds-form', 'novalidate']); ?>
                <input type="hidden" name="_method" value="PUT">
                
                <!-- Mensajes de error generales -->
                <?php if (isset($validation) && $validation->getErrors()): ?>
                    <div class="ds-alert ds-alert--danger ds-fade-in">
                        <span class="ds-alert__icon">❌</span>
                        <div class="ds-alert__content">
                            <p class="ds-alert__text"><?= $validation->listErrors(); ?></p>
                        </div>
                        <button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>
                    </div>
                <?php endif; ?>

                <div class="ds-grid ds-grid--2">
                    <!-- Nombre -->
                    <div class="ds-form-group">
                        <label for="nombre" class="ds-label ds-label--required">Nombre</label>
                        <input type="text" class="ds-input" id="nombre" name="nombre"
                               value="<?= old('nombre', $patient['nombre']); ?>" required maxlength="100"
                               placeholder="Ingrese el nombre del paciente">
                        <div class="ds-form-error">
                            Por favor, ingrese un nombre válido (mínimo 2 caracteres).
                        </div>
                    </div>

                    <!-- Primer Apellido -->
                    <div class="ds-form-group">
                        <label for="primer_apellido" class="ds-label ds-label--required">Primer Apellido</label>
                        <input type="text" class="ds-input" id="primer_apellido" name="primer_apellido"
                               value="<?= old('primer_apellido', $patient['primer_apellido']); ?>" required maxlength="100"
                               placeholder="Ingrese el primer apellido del paciente">
                        <div class="ds-form-error">
                            Por favor, ingrese un apellido válido (mínimo 2 caracteres).
                        </div>
                    </div>

                    <!-- Segundo Apellido -->
                    <div class="ds-form-group">
                        <label for="segundo_apellido" class="ds-label">Segundo Apellido</label>
                        <input type="text" class="ds-input" id="segundo_apellido" name="segundo_apellido"
                               value="<?= old('segundo_apellido', $patient['segundo_apellido']); ?>" maxlength="100"
                               placeholder="Ingrese el segundo apellido del paciente (opcional)">
                    </div>

                    <!-- Email -->
                    <div class="ds-form-group">
                        <label for="email" class="ds-label">Email</label>
                        <input type="email" class="ds-input" id="email" name="email"
                               value="<?= old('email', $patient['email']); ?>" maxlength="150"
                               placeholder="correo@ejemplo.com">
                        <small class="ds-form-help">
                            Email actual: <?= esc($patient['email'] ?? 'No registrado'); ?>
                        </small>
                        <div class="ds-form-error">
                            Por favor, ingrese un email válido.
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="ds-form-group">
                        <label for="telefono" class="ds-label">Teléfono</label>
                        <input type="tel" class="ds-input" id="telefono" name="telefono"
                               value="<?= old('telefono', $patient['telefono']); ?>" maxlength="20"
                               placeholder="+52 123 456 7890">
                        <small class="ds-form-help">
                            Teléfono actual: <?= esc($patient['telefono'] ?? 'No registrado'); ?>
                        </small>
                        <div class="ds-form-error">
                            Por favor, ingrese un teléfono válido.
                        </div>
                    </div>

                    <!-- Celular -->
                    <div class="ds-form-group">
                        <label for="celular" class="ds-label">Celular</label>
                        <input type="tel" class="ds-input" id="celular" name="celular"
                               value="<?= old('celular', $patient['celular']); ?>" maxlength="20"
                               placeholder="+52 123 456 7890">
                        <small class="ds-form-help">
                            Celular actual: <?= esc($patient['celular'] ?? 'No registrado'); ?>
                        </small>
                        <div class="ds-form-error">
                            Por favor, ingrese un celular válido.
                        </div>
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="ds-form-group">
                        <label for="fecha_nacimiento" class="ds-label">Fecha de Nacimiento</label>
                        <input type="date" class="ds-input" id="fecha_nacimiento" name="fecha_nacimiento"
                               value="<?= old('fecha_nacimiento', $patient['fecha_nacimiento']); ?>"
                               max="<?= date('Y-m-d'); ?>">
                        <small class="ds-form-help">
                            <?php
                                if ($patient['fecha_nacimiento']) {
                                    $fecha = new DateTime($patient['fecha_nacimiento']);
                                    $hoy = new DateTime();
                                    $edad = $hoy->diff($fecha)->y;
                                    echo 'Edad actual: ' . $edad . ' años';
                                } else {
                                    echo 'Fecha no registrada';
                                }
                            ?>
                        </small>
                        <div class="ds-form-error">
                            Por favor, ingrese una fecha válida.
                        </div>
                    </div>
                </div>

                <!-- Domicilio (campo completo) -->
                <div class="ds-form-group">
                    <label for="domicilio" class="ds-label">Domicilio</label>
                    <textarea class="ds-input" id="domicilio" name="domicilio" rows="3"
                              maxlength="255" placeholder="Ingrese el domicilio completo"><?= old('domicilio', $patient['domicilio']); ?></textarea>
                    <small class="ds-form-help">
                        Domicilio actual: <?= esc($patient['domicilio'] ?? 'No registrado'); ?>
                    </small>
                    <div class="ds-form-error">
                        El domicilio es demasiado largo (máximo 255 caracteres).
                    </div>
                </div>

                <!-- Información de Registro -->
                <div class="ds-card ds-card--light ds-mt-6">
                    <div class="ds-card__body">
                        <h3 class="ds-card__subtitle">Información de Registro</h3>
                        <div class="ds-grid ds-grid--2">
                            <div>
                                <small class="ds-text-muted">ID:</small><br>
                                <strong><?= $patient['id']; ?></strong>
                            </div>
                            <div>
                                <small class="ds-text-muted">Fecha de Registro:</small><br>
                                <strong>
                                    <?php
                                        $fechaRegistro = new DateTime($patient['created_at']);
                                        echo $fechaRegistro->format('d/m/Y H:i');
                                    ?>
                                </strong>
                            </div>
                        </div>
                        <?php if (!empty($patient['updated_at']) && $patient['updated_at'] != $patient['created_at']): ?>
                        <div class="ds-grid ds-grid--1 ds-mt-4">
                            <div>
                                <small class="ds-text-muted">Última Actualización:</small><br>
                                <strong>
                                    <?php
                                        $fechaActualizacion = new DateTime($patient['updated_at']);
                                        echo $fechaActualizacion->format('d/m/Y H:i');
                                    ?>
                                </strong>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Botones -->
                <div class="ds-form-actions ds-mt-6">
                    <button type="submit" class="ds-btn ds-btn--primary">
                        <span class="ds-btn__icon ds-btn__icon--left">💾</span>
                        Actualizar Paciente
                    </button>
                    <a href="<?= base_url('/pacientes/' . $patient['id']); ?>" class="ds-btn ds-btn--secondary">
                        <span class="ds-btn__icon ds-btn__icon--left">❌</span>
                        Cancelar
                    </a>
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
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Formateo de teléfono
    var telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            var value = e.target.value.replace(/\D/g, '');
            var formattedValue = '';
            
            if (value.length > 0) {
                if (value.length <= 3) {
                    formattedValue = value;
                } else if (value.length <= 6) {
                    formattedValue = value.slice(0, 3) + ' ' + value.slice(3);
                } else if (value.length <= 9) {
                    formattedValue = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
                } else {
                    formattedValue = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6, 9) + ' ' + value.slice(9, 13);
                }
            }
            
            e.target.value = formattedValue;
        });
    }

    // Validación de email en tiempo real
    var emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            var email = this.value;
            if (email && !validateEmail(email)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (email) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
    }

    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
</script>
<?= $this->endSection(); ?>
