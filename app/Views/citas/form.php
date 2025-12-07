<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title"><?= isset($cita) && !empty($cita['id']) ? '✏️ Editar Cita' : '📅 Nueva Cita'; ?></h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/citas'); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">⬅️</span>
                Volver a Citas
            </a>
            <a href="<?= base_url('/citas/calendario'); ?>" class="ds-btn ds-btn--info">
                <span class="ds-btn__icon ds-btn__icon--left">📅</span>
                Ver Calendario
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="ds-alert ds-alert--danger">
            <span class="ds-alert__icon">❌</span>
            <ul class="ds-list ds-list--no-style">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert--danger">
            <span class="ds-alert__icon">❌</span>
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div class="ds-card">
        <div class="ds-card__header">
            <h2 class="ds-card__title"><?= isset($cita) && !empty($cita['id']) ? 'Modificar Cita' : 'Programar Nueva Cita'; ?></h2>
        </div>
        <div class="ds-card__body">
            <?php 
            $isEdit = isset($cita) && !empty($cita['id']);
            $formAction = $isEdit 
                ? base_url('/citas/' . $cita['id'] . '/actualizar') 
                : base_url('/citas/guardar');
            ?>
            <form action="<?= $formAction; ?>" method="POST" id="citaForm">
                <?= csrf_field(); ?>
                
                <div class="ds-grid ds-grid--2">
                    <!-- Título -->
                    <div class="ds-form-group ds-form-group--full">
                        <label for="titulo" class="ds-label ds-label--required">Título de la Cita</label>
                        <input type="text" class="ds-input" id="titulo" name="titulo" 
                               placeholder="Ej: Limpieza dental, Revisión mensual..."
                               maxlength="200" required
                               value="<?= old('titulo', $cita['titulo'] ?? ''); ?>">
                    </div>

                    <!-- Paciente -->
                    <div class="ds-form-group">
                        <label for="id_paciente" class="ds-label ds-label--required">Paciente</label>
                        <select class="ds-input" id="id_paciente" name="id_paciente" required>
                            <option value="">Seleccione un paciente...</option>
                            <?php if (!empty($pacientes)): ?>
                                <?php foreach ($pacientes as $paciente): ?>
                                    <option value="<?= $paciente['id']; ?>" 
                                        <?= old('id_paciente', $cita['id_paciente'] ?? '') == $paciente['id'] ? 'selected' : ''; ?>>
                                        <?= esc(trim($paciente['nombre'] . ' ' . ($paciente['apellido'] ?? ''))); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Doctor/Usuario -->
                    <div class="ds-form-group">
                        <label for="id_usuario" class="ds-label ds-label--required">Doctor</label>
                        <select class="ds-input" id="id_usuario" name="id_usuario" required>
                            <option value="">Seleccione un doctor...</option>
                            <?php if (!empty($doctores)): ?>
                                <?php foreach ($doctores as $doctor): ?>
                                    <option value="<?= $doctor['id']; ?>"
                                        <?= old('id_usuario', $cita['id_usuario'] ?? '') == $doctor['id'] ? 'selected' : ''; ?>>
                                        <?= esc($doctor['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Servicio -->
                    <div class="ds-form-group">
                        <label for="id_servicio" class="ds-label">Servicio</label>
                        <select class="ds-input" id="id_servicio" name="id_servicio">
                            <option value="">Seleccione un servicio (opcional)...</option>
                            <?php if (!empty($servicios)): ?>
                                <?php foreach ($servicios as $servicio): ?>
                                    <option value="<?= $servicio['id']; ?>"
                                        <?= old('id_servicio', $cita['id_servicio'] ?? '') == $servicio['id'] ? 'selected' : ''; ?>>
                                        <?= esc($servicio['nombre']); ?> - $<?= number_format($servicio['precio_base'] ?? 0, 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Tipo de Cita -->
                    <div class="ds-form-group">
                        <label for="tipo_cita" class="ds-label ds-label--required">Tipo de Cita</label>
                        <select class="ds-input" id="tipo_cita" name="tipo_cita" required>
                            <?php 
                            $tipos = ['consulta' => 'Consulta', 'tratamiento' => 'Tratamiento', 'revision' => 'Revisión', 'urgencia' => 'Urgencia'];
                            $tipoActual = old('tipo_cita', $cita['tipo_cita'] ?? 'consulta');
                            foreach ($tipos as $valor => $etiqueta): 
                            ?>
                                <option value="<?= $valor; ?>" <?= $tipoActual == $valor ? 'selected' : ''; ?>>
                                    <?= $etiqueta; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Fecha Inicio -->
                    <div class="ds-form-group">
                        <label for="fecha_inicio" class="ds-label ds-label--required">
                            Fecha y Hora de Inicio
                            <span class="ds-label__hint" id="hintFechaInicio"></span>
                        </label>
                        <input type="datetime-local" class="ds-input" id="fecha_inicio" name="fecha_inicio" required
                               min="<?= date('Y-m-d\TH:i'); ?>"
                               value="<?= old('fecha_inicio', isset($cita['fecha_inicio']) ? date('Y-m-d\TH:i', strtotime($cita['fecha_inicio'])) : ''); ?>">
                        <small class="ds-form__help" id="errorFechaInicio"></small>
                    </div>
                    
                    <!-- Fecha Fin -->
                    <div class="ds-form-group">
                        <label for="fecha_fin" class="ds-label ds-label--required">
                            Fecha y Hora de Fin
                            <span class="ds-label__hint" id="hintFechaFin"></span>
                        </label>
                        <input type="datetime-local" class="ds-input" id="fecha_fin" name="fecha_fin" required
                               value="<?= old('fecha_fin', isset($cita['fecha_fin']) ? date('Y-m-d\TH:i', strtotime($cita['fecha_fin'])) : ''); ?>">
                        <small class="ds-form__help" id="errorFechaFin"></small>
                    </div>

                    <!-- Estado (solo para edición) -->
                    <?php if ($isEdit): ?>
                    <div class="ds-form-group">
                        <label for="estado" class="ds-label">Estado</label>
                        <select class="ds-input" id="estado" name="estado">
                            <?php 
                            $estados = [
                                'programada' => 'Programada', 
                                'confirmada' => 'Confirmada', 
                                'en_progreso' => 'En Progreso',
                                'completada' => 'Completada', 
                                'cancelada' => 'Cancelada'
                            ];
                            $estadoActual = old('estado', $cita['estado'] ?? 'programada');
                            foreach ($estados as $valor => $etiqueta): 
                            ?>
                                <option value="<?= $valor; ?>" <?= $estadoActual == $valor ? 'selected' : ''; ?>>
                                    <?= $etiqueta; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Color -->
                    <div class="ds-form-group">
                        <label for="color" class="ds-label">Color</label>
                        <input type="color" class="ds-input" id="color" name="color" 
                               value="<?= old('color', $cita['color'] ?? '#3788d8'); ?>"
                               style="height: 40px; padding: 4px;">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Notas -->
                    <div class="ds-form-group ds-form-group--full">
                        <label for="notas" class="ds-label">Notas</label>
                        <textarea class="ds-input" id="notas" name="notas" rows="3" 
                                  placeholder="Notas adicionales sobre la cita..."
                                  maxlength="1000"><?= old('notas', $cita['notas'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <!-- Verificación de disponibilidad -->
                <div id="disponibilidadCheck" class="ds-alert ds-alert--info" style="display: none;">
                    <span class="ds-alert__icon">ℹ️</span>
                    <span id="disponibilidadMensaje"></span>
                </div>
                
                <div class="ds-card__footer">
                    <div class="ds-flex ds-justify-end ds-gap-3">
                        <button type="button" class="ds-btn ds-btn--secondary" id="btnVerificarDisponibilidad">
                            <span class="ds-btn__icon ds-btn__icon--left">🔍</span>
                            Verificar Disponibilidad
                        </button>
                        <button type="submit" class="ds-btn ds-btn--primary">
                            <span class="ds-btn__icon ds-btn__icon--left">💾</span>
                            <?= $isEdit ? 'Actualizar Cita' : 'Programar Cita'; ?>
                        </button>
                        <a href="<?= base_url('/citas'); ?>" class="ds-btn ds-btn--secondary">
                            <span class="ds-btn__icon ds-btn__icon--left">❌</span>
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const tipoSelect = document.getElementById('tipo_cita');
    const tituloInput = document.getElementById('titulo');
    const formulario = document.getElementById('citaForm');
    
    // Validar fechas en tiempo real
    function validarFechas() {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        const ahora = new Date();
        
        let valido = true;
        let mensajeInicio = '';
        let mensajeFin = '';
        
        // Validación fecha inicio
        if (fechaInicio.value) {
            if (inicio <= ahora) {
                mensajeInicio = '❌ La fecha debe ser en el futuro';
                valido = false;
            } else {
                const formatoInicio = inicio.toLocaleString('es-ES', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                document.getElementById('hintFechaInicio').textContent = `(${formatoInicio})`;
            }
        }
        
        // Validación fecha fin
        if (fechaFin.value) {
            if (fin <= inicio) {
                mensajeFin = '❌ La hora de fin debe ser posterior a la de inicio';
                valido = false;
            } else {
                const diferenciasMinutos = Math.round((fin - inicio) / (1000 * 60));
                if (diferenciasMinutos < 15) {
                    mensajeFin = '⚠️ Mínimo 15 minutos de duración';
                    valido = false;
                } else if (diferenciasMinutos > 480) {
                    mensajeFin = '⚠️ Máximo 8 horas de duración';
                    valido = false;
                } else {
                    const horas = Math.floor(diferenciasMinutos / 60);
                    const minutos = diferenciasMinutos % 60;
                    const duracion = horas > 0 
                        ? `${horas}h ${minutos}m`
                        : `${minutos}m`;
                    const formatoFin = fin.toLocaleString('es-ES', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    document.getElementById('hintFechaFin').textContent = `(${formatoFin} - ${duracion})`;
                }
            }
        }
        
        document.getElementById('errorFechaInicio').textContent = mensajeInicio;
        document.getElementById('errorFechaFin').textContent = mensajeFin;
        
        return valido;
    }
    
    // Auto-calcular fecha fin cuando cambia fecha inicio (30 min por defecto)
    fechaInicio.addEventListener('change', function() {
        if (this.value && !fechaFin.value) {
            const start = new Date(this.value);
            start.setMinutes(start.getMinutes() + 30);
            fechaFin.value = start.toISOString().slice(0, 16);
        }
        validarFechas();
    });
    
    fechaFin.addEventListener('change', validarFechas);
    
    // Auto-generar título si está vacío cuando se selecciona tipo
    tipoSelect.addEventListener('change', function() {
        if (!tituloInput.value) {
            const tipos = {
                'consulta': 'Consulta general',
                'tratamiento': 'Tratamiento dental',
                'revision': 'Revisión de rutina',
                'urgencia': 'Atención de urgencia'
            };
            tituloInput.value = tipos[this.value] || '';
        }
    });
    
    // Verificar disponibilidad
    document.getElementById('btnVerificarDisponibilidad').addEventListener('click', function() {
        if (!validarFechas()) {
            alert('Por favor corrija los errores en las fechas');
            return;
        }
        
        const inicio = fechaInicio.value;
        const fin = fechaFin.value;
        const doctorId = document.getElementById('id_usuario').value;
        
        if (!inicio || !fin || !doctorId) {
            alert('Por favor complete fecha, hora y doctor para verificar disponibilidad');
            return;
        }
        
        const checkDiv = document.getElementById('disponibilidadCheck');
        const msgSpan = document.getElementById('disponibilidadMensaje');
        
        checkDiv.style.display = 'block';
        checkDiv.className = 'ds-alert ds-alert--info';
        msgSpan.textContent = '⏳ Verificando disponibilidad...';
        
        fetch('<?= base_url('/citas/api/disponibilidad'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                fecha_inicio: inicio.replace('T', ' ') + ':00',
                fecha_fin: fin.replace('T', ' ') + ':00',
                id_usuario: doctorId,
                id_cita: '<?= $cita['id'] ?? ''; ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.disponible) {
                checkDiv.className = 'ds-alert ds-alert--success';
                msgSpan.textContent = '✅ ' + data.message;
            } else {
                checkDiv.className = 'ds-alert ds-alert--danger';
                msgSpan.textContent = '❌ ' + data.message;
                
                if (data.conflictos && data.conflictos.length > 0) {
                    msgSpan.innerHTML += '<br><small>Conflictos encontrados:</small><ul style="margin: 5px 0 0 20px;">';
                    data.conflictos.forEach(conflicto => {
                        msgSpan.innerHTML += `<li>${conflicto}</li>`;
                    });
                    msgSpan.innerHTML += '</ul>';
                }
            }
        })
        .catch(error => {
            checkDiv.className = 'ds-alert ds-alert--danger';
            msgSpan.textContent = '❌ Error al verificar disponibilidad';
            console.error('Error:', error);
        });
    });
    
    // Validar formulario antes de enviar
    formulario.addEventListener('submit', function(e) {
        if (!validarFechas()) {
            e.preventDefault();
            alert('Por favor corrija los errores en las fechas');
        }
    });
});
</script>
<?= $this->endSection(); ?>
