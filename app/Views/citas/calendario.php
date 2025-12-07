<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('styles'); ?>
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.min.css" rel="stylesheet">
<style>
    /* Estilos del calendario */
    #calendar {
        max-width: 100%;
        margin: 0 auto;
    }
    
    .fc {
        font-family: inherit;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--ds-text-primary);
    }
    
    .fc .fc-button {
        background-color: var(--ds-primary);
        border-color: var(--ds-primary);
        font-weight: 500;
    }
    
    .fc .fc-button:hover {
        background-color: var(--ds-primary-dark);
        border-color: var(--ds-primary-dark);
    }
    
    .fc .fc-button-primary:not(:disabled):active,
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background-color: var(--ds-primary-dark);
        border-color: var(--ds-primary-dark);
    }
    
    .fc .fc-event {
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-size: 0.85rem;
        padding: 2px 4px;
    }
    
    .fc .fc-event:hover {
        opacity: 0.85;
    }
    
    .fc .fc-daygrid-day.fc-day-today {
        background-color: rgba(92, 205, 222, 0.1);
    }
    
    .fc .fc-timegrid-slot {
        height: 2em;
    }
    
    /* Leyenda de colores */
    .ds-calendar-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem;
        background: var(--ds-bg-secondary);
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .ds-legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }
    
    .ds-legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }
    
    /* Modal de cita */
    .ds-modal-cita .ds-form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    @media (max-width: 768px) {
        .ds-modal-cita .ds-form-row {
            grid-template-columns: 1fr;
        }
    }
    
    /* Indicador de carga */
    .fc-loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    /* Tooltip de evento */
    .fc-event-tooltip {
        position: absolute;
        background: white;
        border: 1px solid var(--ds-border);
        border-radius: 8px;
        padding: 0.75rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        max-width: 250px;
        font-size: 0.875rem;
    }
    
    .fc-event-tooltip h4 {
        margin: 0 0 0.5rem 0;
        font-size: 0.9rem;
    }
    
    .fc-event-tooltip p {
        margin: 0.25rem 0;
        color: var(--ds-text-secondary);
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">📅 Calendario de Citas</h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/citas'); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">📋</span>
                Ver Lista
            </a>
            <button type="button" class="ds-btn ds-btn--primary" onclick="abrirModalNuevaCita()">
                <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                Nueva Cita
            </button>
        </div>
    </div>

    <!-- Alertas -->
    <div id="alert-container">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="ds-alert ds-alert--success">
                <span class="ds-alert__icon">✅</span>
                <?= session()->getFlashdata('success'); ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="ds-alert ds-alert--danger">
                <span class="ds-alert__icon">❌</span>
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Leyenda de colores -->
    <div class="ds-calendar-legend">
        <span class="ds-text-muted" style="font-weight: 600;">Por tipo:</span>
        <?php foreach ($coloresTipo ?? [] as $tipo => $color): ?>
        <div class="ds-legend-item">
            <span class="ds-legend-color" style="background-color: <?= $color; ?>"></span>
            <span><?= ucfirst($tipo); ?></span>
        </div>
        <?php endforeach; ?>
        <span class="ds-text-muted" style="font-weight: 600; margin-left: 1rem;">Por estado:</span>
        <?php foreach ($coloresEstado ?? [] as $estado => $color): ?>
        <div class="ds-legend-item">
            <span class="ds-legend-color" style="background-color: <?= $color; ?>"></span>
            <span><?= ucfirst(str_replace('_', ' ', $estado)); ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Calendario -->
    <div class="ds-card">
        <div class="ds-card__body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Modal Nueva/Editar Cita -->
<div class="ds-modal-overlay" id="modal-cita">
    <div class="ds-modal ds-modal--lg">
        <div class="ds-modal__header">
            <h3 class="ds-modal__title" id="modal-cita-title">
                <span class="ds-modal__icon">📅</span>
                Nueva Cita
            </h3>
            <button type="button" class="ds-modal__close" onclick="cerrarModalCita()">×</button>
        </div>
        <form id="form-cita">
            <input type="hidden" id="cita-id" name="id">
            <div class="ds-modal__body">
                <div class="ds-form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="ds-form-group">
                        <label for="id_paciente" class="ds-label ds-label--required">Paciente</label>
                        <select class="ds-input" id="id_paciente" name="id_paciente" required>
                            <option value="">Seleccione un paciente...</option>
                            <?php foreach ($pacientes ?? [] as $paciente): ?>
                            <option value="<?= $paciente['id']; ?>">
                                <?= esc($paciente['nombre'] . ' ' . ($paciente['primer_apellido'] ?? '')); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="id_servicio" class="ds-label">Servicio</label>
                        <select class="ds-input" id="id_servicio" name="id_servicio">
                            <option value="">Seleccione un servicio...</option>
                            <?php foreach ($servicios ?? [] as $servicio): ?>
                            <option value="<?= $servicio['id']; ?>" data-duracion="<?= $servicio['duracion'] ?? 30; ?>">
                                <?= esc($servicio['nombre']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="fecha_inicio" class="ds-label ds-label--required">Fecha y Hora Inicio</label>
                        <input type="datetime-local" class="ds-input" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="fecha_fin" class="ds-label ds-label--required">Fecha y Hora Fin</label>
                        <input type="datetime-local" class="ds-input" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="tipo_cita" class="ds-label ds-label--required">Tipo de Cita</label>
                        <select class="ds-input" id="tipo_cita" name="tipo_cita" required>
                            <option value="consulta">Consulta</option>
                            <option value="tratamiento">Tratamiento</option>
                            <option value="revision">Revisión</option>
                            <option value="urgencia">Urgencia</option>
                        </select>
                    </div>
                    
                    <div class="ds-form-group">
                        <label for="id_usuario" class="ds-label">Doctor</label>
                        <select class="ds-input" id="id_usuario" name="id_usuario">
                            <?php foreach ($doctores ?? [] as $doctor): ?>
                            <option value="<?= $doctor['id']; ?>">
                                <?= esc($doctor['nombre']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="ds-form-group">
                    <label for="notas" class="ds-label">Notas</label>
                    <textarea class="ds-input" id="notas" name="notas" rows="3" 
                              placeholder="Notas adicionales sobre la cita..."></textarea>
                </div>
                
                <!-- Indicador de disponibilidad -->
                <div id="disponibilidad-info" style="display: none;" class="ds-alert ds-alert--warning">
                    <span class="ds-alert__icon">⚠️</span>
                    <span id="disponibilidad-mensaje"></span>
                </div>
            </div>
            <div class="ds-modal__footer">
                <button type="button" class="ds-btn ds-btn--secondary" onclick="cerrarModalCita()">
                    Cancelar
                </button>
                <button type="submit" class="ds-btn ds-btn--primary" id="btn-guardar-cita">
                    <span class="ds-btn__icon ds-btn__icon--left">💾</span>
                    Guardar Cita
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ver Cita -->
<div class="ds-modal-overlay" id="modal-ver-cita">
    <div class="ds-modal">
        <div class="ds-modal__header">
            <h3 class="ds-modal__title">
                <span class="ds-modal__icon">📋</span>
                Detalles de la Cita
            </h3>
            <button type="button" class="ds-modal__close" onclick="cerrarModalVerCita()">×</button>
        </div>
        <div class="ds-modal__body" id="modal-ver-cita-body">
            <!-- Contenido dinámico -->
        </div>
        <div class="ds-modal__footer" id="modal-ver-cita-footer">
            <!-- Botones dinámicos -->
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
    let calendar;
    const baseUrl = '<?= base_url(); ?>';
    
    // Colores
    const coloresTipo = <?= json_encode($coloresTipo ?? []); ?>;
    const coloresEstado = <?= json_encode($coloresEstado ?? []); ?>;
    
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista'
            },
            height: 'auto',
            editable: true,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            weekends: true,
            nowIndicator: true,
            slotMinTime: '07:00:00',
            slotMaxTime: '21:00:00',
            allDaySlot: false,
            
            // Fuente de eventos
            events: function(info, successCallback, failureCallback) {
                fetch(`${baseUrl}/citas/api/getCitas?start=${info.startStr}&end=${info.endStr}`)
                    .then(response => response.json())
                    .then(events => successCallback(events))
                    .catch(error => {
                        console.error('Error al cargar citas:', error);
                        failureCallback(error);
                    });
            },
            
            // Al seleccionar rango de fechas
            select: function(info) {
                abrirModalNuevaCita(info.start, info.end);
            },
            
            // Al hacer clic en un evento
            eventClick: function(info) {
                mostrarDetallesCita(info.event.id);
            },
            
            // Al arrastrar evento (drag & drop)
            eventDrop: function(info) {
                actualizarFechaCita(info.event.id, info.event.start, info.event.end, info);
            },
            
            // Al redimensionar evento
            eventResize: function(info) {
                actualizarFechaCita(info.event.id, info.event.start, info.event.end, info);
            },
            
            // Validar si se puede soltar
            eventAllow: function(dropInfo, draggedEvent) {
                // No permitir arrastrar a fechas pasadas
                return dropInfo.start >= new Date(new Date().setHours(0,0,0,0));
            },
            
            // Tooltip al pasar el mouse
            eventMouseEnter: function(info) {
                const props = info.event.extendedProps;
                const tooltip = document.createElement('div');
                tooltip.className = 'fc-event-tooltip';
                tooltip.innerHTML = `
                    <h4>${info.event.title}</h4>
                    <p><strong>Servicio:</strong> ${props.servicio || 'No especificado'}</p>
                    <p><strong>Estado:</strong> ${props.estado}</p>
                    <p><strong>Doctor:</strong> ${props.doctor || 'No asignado'}</p>
                `;
                tooltip.style.top = info.jsEvent.pageY + 10 + 'px';
                tooltip.style.left = info.jsEvent.pageX + 10 + 'px';
                tooltip.id = 'event-tooltip';
                document.body.appendChild(tooltip);
            },
            
            eventMouseLeave: function(info) {
                const tooltip = document.getElementById('event-tooltip');
                if (tooltip) tooltip.remove();
            },
            
            // Al cargar eventos
            loading: function(isLoading) {
                if (isLoading) {
                    calendarEl.classList.add('fc-loading');
                } else {
                    calendarEl.classList.remove('fc-loading');
                }
            }
        });
        
        calendar.render();
        
        // Listeners del formulario
        document.getElementById('form-cita').addEventListener('submit', guardarCita);
        
        // Auto-calcular hora fin al cambiar servicio
        document.getElementById('id_servicio').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const duracion = parseInt(option.dataset.duracion) || 30;
            const fechaInicio = document.getElementById('fecha_inicio').value;
            
            if (fechaInicio) {
                const inicio = new Date(fechaInicio);
                inicio.setMinutes(inicio.getMinutes() + duracion);
                document.getElementById('fecha_fin').value = formatDateTimeLocal(inicio);
            }
        });
        
        // Verificar disponibilidad al cambiar fechas
        document.getElementById('fecha_inicio').addEventListener('change', verificarDisponibilidadFormulario);
        document.getElementById('fecha_fin').addEventListener('change', verificarDisponibilidadFormulario);
    });
    
    // Formatear fecha para datetime-local
    function formatDateTimeLocal(date) {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }
    
    // Formatear fecha para mostrar
    function formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('es-MX', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Abrir modal de nueva cita
    function abrirModalNuevaCita(start = null, end = null) {
        document.getElementById('modal-cita-title').innerHTML = '<span class="ds-modal__icon">📅</span> Nueva Cita';
        document.getElementById('form-cita').reset();
        document.getElementById('cita-id').value = '';
        document.getElementById('disponibilidad-info').style.display = 'none';
        
        if (start) {
            document.getElementById('fecha_inicio').value = formatDateTimeLocal(start);
            
            if (!end || start.getTime() === end.getTime()) {
                const fin = new Date(start);
                fin.setMinutes(fin.getMinutes() + 30);
                document.getElementById('fecha_fin').value = formatDateTimeLocal(fin);
            } else {
                document.getElementById('fecha_fin').value = formatDateTimeLocal(end);
            }
        } else {
            const now = new Date();
            now.setMinutes(Math.ceil(now.getMinutes() / 15) * 15); // Redondear a 15 min
            document.getElementById('fecha_inicio').value = formatDateTimeLocal(now);
            
            const fin = new Date(now);
            fin.setMinutes(fin.getMinutes() + 30);
            document.getElementById('fecha_fin').value = formatDateTimeLocal(fin);
        }
        
        document.getElementById('modal-cita').classList.add('is-active');
    }
    
    // Cerrar modal
    function cerrarModalCita() {
        document.getElementById('modal-cita').classList.remove('is-active');
    }
    
    function cerrarModalVerCita() {
        document.getElementById('modal-ver-cita').classList.remove('is-active');
    }
    
    // Verificar disponibilidad en el formulario
    async function verificarDisponibilidadFormulario() {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        const idUsuario = document.getElementById('id_usuario').value;
        const citaId = document.getElementById('cita-id').value;
        
        if (!fechaInicio || !fechaFin) return;
        
        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            id_usuario: idUsuario
        });
        
        if (citaId) params.append('excluir_id', citaId);
        
        try {
            const response = await fetch(`${baseUrl}/citas/api/disponibilidad?${params}`);
            const data = await response.json();
            
            const infoDiv = document.getElementById('disponibilidad-info');
            const mensajeSpan = document.getElementById('disponibilidad-mensaje');
            
            if (!data.disponible) {
                infoDiv.style.display = 'flex';
                mensajeSpan.textContent = 'El horario seleccionado tiene conflictos con otras citas.';
                infoDiv.className = 'ds-alert ds-alert--warning';
            } else {
                infoDiv.style.display = 'flex';
                mensajeSpan.textContent = 'Horario disponible ✓';
                infoDiv.className = 'ds-alert ds-alert--success';
                setTimeout(() => {
                    infoDiv.style.display = 'none';
                }, 2000);
            }
        } catch (error) {
            console.error('Error al verificar disponibilidad:', error);
        }
    }
    
    // Guardar cita
    async function guardarCita(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const citaId = formData.get('id');
        
        const data = {
            id_paciente: formData.get('id_paciente'),
            id_servicio: formData.get('id_servicio'),
            id_usuario: formData.get('id_usuario'),
            fecha_inicio: formData.get('fecha_inicio'),
            fecha_fin: formData.get('fecha_fin'),
            tipo_cita: formData.get('tipo_cita'),
            notas: formData.get('notas')
        };
        
        const url = citaId 
            ? `${baseUrl}/citas/api/update/${citaId}`
            : `${baseUrl}/citas/api/store`;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                cerrarModalCita();
                calendar.refetchEvents();
                mostrarAlerta(result.message || 'Cita guardada correctamente', 'success');
            } else {
                mostrarAlerta(result.error || 'Error al guardar la cita', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta('Error al procesar la solicitud', 'danger');
        }
    }
    
    // Mostrar detalles de cita
    async function mostrarDetallesCita(id) {
        try {
            const response = await fetch(`${baseUrl}/citas/api/getCita/${id}`);
            const cita = await response.json();
            
            if (cita.error) {
                mostrarAlerta('Cita no encontrada', 'danger');
                return;
            }
            
            const pacienteNombre = `${cita.paciente_nombre} ${cita.paciente_apellido || ''}`.trim();
            
            const estadoBadge = {
                'programada': 'ds-badge--info',
                'confirmada': 'ds-badge--success',
                'en_progreso': 'ds-badge--warning',
                'completada': 'ds-badge--secondary',
                'cancelada': 'ds-badge--danger'
            };
            
            document.getElementById('modal-ver-cita-body').innerHTML = `
                <div class="ds-info-grid" style="display: grid; gap: 1rem;">
                    <div class="ds-info-item">
                        <span class="ds-text-muted">Paciente:</span>
                        <strong>${pacienteNombre}</strong>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-text-muted">Doctor:</span>
                        <strong>${cita.doctor_nombre || 'No asignado'}</strong>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-text-muted">Fecha:</span>
                        <strong>${formatDateTime(cita.fecha_inicio)}</strong>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-text-muted">Hasta:</span>
                        <strong>${formatDateTime(cita.fecha_fin)}</strong>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-text-muted">Servicio:</span>
                        <strong>${cita.servicio_nombre || 'No especificado'}</strong>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-text-muted">Estado:</span>
                        <span class="ds-badge ${estadoBadge[cita.estado] || 'ds-badge--secondary'}">${cita.estado}</span>
                    </div>
                    <div class="ds-info-item">
                        <span class="ds-text-muted">Tipo:</span>
                        <strong>${cita.tipo_cita}</strong>
                    </div>
                    ${cita.notas ? `
                    <div class="ds-info-item" style="grid-column: 1 / -1;">
                        <span class="ds-text-muted">Notas:</span>
                        <p>${cita.notas}</p>
                    </div>
                    ` : ''}
                </div>
            `;
            
            // Botones según estado
            let botonesHTML = `
                <button type="button" class="ds-btn ds-btn--secondary" onclick="cerrarModalVerCita()">
                    Cerrar
                </button>
            `;
            
            if (cita.estado !== 'completada' && cita.estado !== 'cancelada') {
                botonesHTML += `
                    <button type="button" class="ds-btn ds-btn--primary" onclick="editarCita(${cita.id})">
                        ✏️ Editar
                    </button>
                `;
                
                if (cita.estado === 'programada') {
                    botonesHTML += `
                        <button type="button" class="ds-btn ds-btn--success" onclick="cambiarEstadoCita(${cita.id}, 'confirmada')">
                            ✓ Confirmar
                        </button>
                    `;
                }
                
                if (cita.estado === 'confirmada') {
                    botonesHTML += `
                        <button type="button" class="ds-btn ds-btn--warning" onclick="cambiarEstadoCita(${cita.id}, 'en_progreso')">
                            ▶️ Iniciar
                        </button>
                    `;
                }
                
                if (cita.estado === 'en_progreso') {
                    botonesHTML += `
                        <button type="button" class="ds-btn ds-btn--success" onclick="cambiarEstadoCita(${cita.id}, 'completada')">
                            ✓ Completar
                        </button>
                    `;
                }
                
                botonesHTML += `
                    <button type="button" class="ds-btn ds-btn--danger" onclick="cambiarEstadoCita(${cita.id}, 'cancelada')">
                        ❌ Cancelar
                    </button>
                `;
            }
            
            document.getElementById('modal-ver-cita-footer').innerHTML = botonesHTML;
            document.getElementById('modal-ver-cita').classList.add('is-active');
            
        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta('Error al cargar los detalles de la cita', 'danger');
        }
    }
    
    // Editar cita
    async function editarCita(id) {
        cerrarModalVerCita();
        
        try {
            const response = await fetch(`${baseUrl}/citas/api/getCita/${id}`);
            const cita = await response.json();
            
            document.getElementById('modal-cita-title').innerHTML = '<span class="ds-modal__icon">✏️</span> Editar Cita';
            document.getElementById('cita-id').value = cita.id;
            document.getElementById('id_paciente').value = cita.id_paciente;
            document.getElementById('id_servicio').value = cita.id_servicio || '';
            document.getElementById('id_usuario').value = cita.id_usuario;
            document.getElementById('fecha_inicio').value = cita.fecha_inicio.replace(' ', 'T').slice(0, 16);
            document.getElementById('fecha_fin').value = cita.fecha_fin.replace(' ', 'T').slice(0, 16);
            document.getElementById('tipo_cita').value = cita.tipo_cita;
            document.getElementById('notas').value = cita.notas || '';
            
            document.getElementById('modal-cita').style.display = 'flex';
            
        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta('Error al cargar la cita para edición', 'danger');
        }
    }
    
    // Cambiar estado de cita
    async function cambiarEstadoCita(id, nuevoEstado) {
        if (!confirm(`¿Está seguro de cambiar el estado a "${nuevoEstado}"?`)) {
            return;
        }
        
        try {
            const response = await fetch(`${baseUrl}/citas/${id}/cambiar-estado`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `estado=${nuevoEstado}`
            });
            
            const result = await response.json();
            
            if (result.success) {
                cerrarModalVerCita();
                calendar.refetchEvents();
                mostrarAlerta('Estado actualizado correctamente', 'success');
            } else {
                mostrarAlerta(result.error || 'Error al cambiar el estado', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta('Error al procesar la solicitud', 'danger');
        }
    }
    
    // Actualizar fecha por drag & drop
    async function actualizarFechaCita(id, start, end, info) {
        const data = {
            fecha_inicio: start.toISOString().slice(0, 19).replace('T', ' '),
            fecha_fin: end ? end.toISOString().slice(0, 19).replace('T', ' ') : 
                        new Date(start.getTime() + 30 * 60000).toISOString().slice(0, 19).replace('T', ' ')
        };
        
        try {
            const response = await fetch(`${baseUrl}/citas/api/actualizarFecha/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                mostrarAlerta('Cita movida correctamente', 'success');
            } else {
                info.revert();
                mostrarAlerta(result.error || 'Error al mover la cita', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            info.revert();
            mostrarAlerta('Error al procesar la solicitud', 'danger');
        }
    }
    
    // Mostrar alerta
    function mostrarAlerta(mensaje, tipo = 'info') {
        const container = document.getElementById('alert-container');
        const alertId = 'alert-' + Date.now();
        
        const iconos = {
            'success': '✅',
            'danger': '❌',
            'warning': '⚠️',
            'info': 'ℹ️'
        };
        
        const alertHTML = `
            <div class="ds-alert ds-alert--${tipo}" id="${alertId}">
                <span class="ds-alert__icon">${iconos[tipo] || 'ℹ️'}</span>
                ${mensaje}
                <button type="button" class="ds-alert__close" onclick="document.getElementById('${alertId}').remove()">×</button>
            </div>
        `;
        
        container.innerHTML = alertHTML + container.innerHTML;
        
        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) alert.remove();
        }, 5000);
    }
</script>
<?= $this->endSection(); ?>
