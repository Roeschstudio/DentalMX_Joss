<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">📅 Excepciones de Horario</h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/agenda'); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">⬅️</span>
                Volver
            </a>
            <button type="button" class="ds-btn ds-btn--primary" onclick="abrirModalExcepcion()">
                <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                Agregar Excepción
            </button>
        </div>
    </div>

    <div class="ds-card">
        <div class="ds-card__header">
            <h2 class="ds-card__title">Días No Disponibles</h2>
        </div>
        <div class="ds-card__body">
            <?php if (empty($excepciones)): ?>
                <div class="ds-empty-state">
                    <div class="ds-empty-state__icon">📋</div>
                    <h3 class="ds-empty-state__text">No hay excepciones configuradas</h3>
                    <p class="ds-text-muted">Las excepciones le permiten marcar días específicos como no disponibles.</p>
                    <button type="button" class="ds-btn ds-btn--primary ds-mt-4" onclick="abrirModalExcepcion()">
                        <span class="ds-btn__icon ds-btn__icon--left">➕</span>
                        Agregar Excepción
                    </button>
                </div>
            <?php else: ?>
                <div class="ds-table-responsive">
                    <table class="ds-table ds-table--hover ds-table--striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Motivo</th>
                                <th>Tipo</th>
                                <th>Horario</th>
                                <th class="ds-text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($excepciones as $excepcion): ?>
                            <?php 
                            $fechaExcepcion = strtotime($excepcion['fecha']);
                            $esPasada = $fechaExcepcion < strtotime('today');
                            ?>
                            <tr class="<?= $esPasada ? 'ds-opacity-50' : ''; ?>">
                                <td>
                                    <strong><?= date('d/m/Y', $fechaExcepcion); ?></strong>
                                    <br>
                                    <small class="ds-text-muted">
                                        <?php
                                        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                                        echo $diasSemana[date('w', $fechaExcepcion)];
                                        ?>
                                    </small>
                                </td>
                                <td><?= esc($excepcion['motivo'] ?? 'Sin motivo especificado'); ?></td>
                                <td>
                                    <?php if ($excepcion['todo_el_dia']): ?>
                                        <span class="ds-badge ds-badge--danger">Todo el día</span>
                                    <?php else: ?>
                                        <span class="ds-badge ds-badge--warning">Parcial</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($excepcion['todo_el_dia']): ?>
                                        <span class="ds-text-muted">No disponible</span>
                                    <?php else: ?>
                                        <?= substr($excepcion['hora_inicio'], 0, 5); ?> - <?= substr($excepcion['hora_fin'], 0, 5); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="ds-text-center">
                                    <?php if (!$esPasada): ?>
                                    <a href="<?= base_url('/agenda/eliminar-excepcion/' . $excepcion['id']); ?>" 
                                       class="ds-btn ds-btn--sm ds-btn--danger"
                                       onclick="return confirm('¿Está seguro de eliminar esta excepción?');">
                                        🗑️
                                    </a>
                                    <?php else: ?>
                                    <span class="ds-text-muted">Pasada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para agregar excepción -->
<div class="ds-modal-overlay" id="modalExcepcion">
    <div class="ds-modal">
        <div class="ds-modal__header">
            <h3 class="ds-modal__title">➕ Agregar Excepción</h3>
            <button type="button" class="ds-modal__close" onclick="cerrarModalExcepcion()">×</button>
        </div>
        <form action="<?= base_url('/agenda/guardar-excepcion'); ?>" method="POST">
            <?= csrf_field(); ?>
            <div class="ds-modal__body">
                <div class="ds-form-group">
                    <label class="ds-label ds-label--required">Fecha</label>
                    <input type="date" class="ds-input" name="fecha" required 
                           min="<?= date('Y-m-d'); ?>">
                </div>
                
                <div class="ds-form-group">
                    <label class="ds-label">Motivo</label>
                    <input type="text" class="ds-input" name="motivo" 
                           placeholder="Ej: Vacaciones, Capacitación, Cita personal...">
                </div>
                
                <div class="ds-form-group">
                    <label class="ds-switch">
                        <input type="checkbox" name="todo_el_dia" value="1" checked 
                               id="todoElDia" onchange="toggleHorasParciales()">
                        <span class="ds-switch__track"></span>
                    </label>
                    <span class="ds-ml-2">Todo el día</span>
                </div>
                
                <div id="horasParciales" class="ds-d-none">
                    <div class="ds-grid ds-grid--2">
                        <div class="ds-form-group">
                            <label class="ds-label">Hora Inicio</label>
                            <input type="time" class="ds-input" name="hora_inicio" id="horaInicio">
                        </div>
                        <div class="ds-form-group">
                            <label class="ds-label">Hora Fin</label>
                            <input type="time" class="ds-input" name="hora_fin" id="horaFin">
                        </div>
                    </div>
                </div>
            </div>
            <div class="ds-modal__footer">
                <button type="button" class="ds-btn ds-btn--secondary" onclick="cerrarModalExcepcion()">
                    Cancelar
                </button>
                <button type="submit" class="ds-btn ds-btn--primary">
                    <span class="ds-btn__icon ds-btn__icon--left">💾</span>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.ds-opacity-50 {
    opacity: 0.5;
}
</style>

<script>
function abrirModalExcepcion() {
    document.getElementById('modalExcepcion').classList.add('is-active');
    document.body.style.overflow = 'hidden';
}

function cerrarModalExcepcion() {
    document.getElementById('modalExcepcion').classList.remove('is-active');
    document.body.style.overflow = '';
}

function toggleHorasParciales() {
    const todoElDia = document.getElementById('todoElDia').checked;
    const horasParciales = document.getElementById('horasParciales');
    const horaInicio = document.getElementById('horaInicio');
    const horaFin = document.getElementById('horaFin');
    
    if (todoElDia) {
        horasParciales.classList.add('ds-d-none');
        horaInicio.removeAttribute('required');
        horaFin.removeAttribute('required');
    } else {
        horasParciales.classList.remove('ds-d-none');
        horaInicio.setAttribute('required', 'required');
        horaFin.setAttribute('required', 'required');
    }
}

// Cerrar modal con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalExcepcion();
    }
});
</script>
<?= $this->endSection(); ?>
