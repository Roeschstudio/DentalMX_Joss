<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">⚙️ Configurar Horario</h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('/agenda'); ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon ds-btn__icon--left">⬅️</span>
                Volver
            </a>
        </div>
    </div>

    <form action="<?= $formAction ?? base_url('/agenda/guardar'); ?>" method="POST">
        <?= csrf_field(); ?>
        
        <!-- Preferencias Generales -->
        <div class="ds-card ds-mb-6">
            <div class="ds-card__header">
                <h2 class="ds-card__title">⏱️ Preferencias Generales</h2>
            </div>
            <div class="ds-card__body">
                <div class="ds-grid ds-grid--3">
                    <div class="ds-form-group">
                        <label class="ds-label">Duración de Cita (minutos)</label>
                        <input type="number" class="ds-input" 
                               name="preferencias[duracion_cita]" 
                               value="<?= esc($preferencias['duracion_cita'] ?? 30); ?>"
                               min="15" max="120" step="5" required>
                        <span class="ds-form-hint">Tiempo estimado para cada cita</span>
                    </div>
                    
                    <div class="ds-form-group">
                        <label class="ds-label">Descanso entre Citas (minutos)</label>
                        <input type="number" class="ds-input" 
                               name="preferencias[tiempo_descanso]" 
                               value="<?= esc($preferencias['tiempo_descanso'] ?? 15); ?>"
                               min="0" max="60" step="5" required>
                        <span class="ds-form-hint">Tiempo de descanso entre citas</span>
                    </div>
                    
                    <div class="ds-form-group">
                        <label class="ds-label">Citas Simultáneas</label>
                        <input type="number" class="ds-input" 
                               name="preferencias[citas_simultaneas]" 
                               value="<?= esc($preferencias['citas_simultaneas'] ?? 1); ?>"
                               min="1" max="5" step="1" required>
                        <span class="ds-form-hint">Máximo de citas al mismo tiempo</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horario Semanal -->
        <div class="ds-card">
            <div class="ds-card__header">
                <h2 class="ds-card__title">📅 Horario Semanal</h2>
            </div>
            <div class="ds-card__body">
                <div class="ds-table-responsive">
                    <table class="ds-table ds-table--hover">
                        <thead>
                            <tr>
                                <th>Día</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th class="ds-text-center">Activo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($diasSemana as $diaNum => $diaNombre): ?>
                            <?php 
                            $horario = $horarios[$diaNum] ?? ['hora_inicio' => '', 'hora_fin' => '', 'activo' => false];
                            ?>
                            <tr>
                                <td><strong><?= esc($diaNombre); ?></strong></td>
                                <td>
                                    <input type="time" 
                                           class="ds-input" 
                                           name="horario[<?= $diaNum; ?>][hora_inicio]" 
                                           value="<?= esc($horario['hora_inicio'] ?? ''); ?>"
                                           id="inicio_<?= $diaNum; ?>">
                                </td>
                                <td>
                                    <input type="time" 
                                           class="ds-input" 
                                           name="horario[<?= $diaNum; ?>][hora_fin]" 
                                           value="<?= esc($horario['hora_fin'] ?? ''); ?>"
                                           id="fin_<?= $diaNum; ?>">
                                </td>
                                <td class="ds-text-center">
                                    <label class="ds-switch">
                                        <input type="checkbox" 
                                               name="horario[<?= $diaNum; ?>][activo]" 
                                               value="1"
                                               <?= ($horario['activo'] ?? false) ? 'checked' : ''; ?>
                                               id="activo_<?= $diaNum; ?>"
                                               onchange="toggleDay(<?= $diaNum; ?>)">
                                        <span class="ds-switch__track"></span>
                                    </label>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="ds-card__footer ds-mt-6">
                    <div class="ds-flex ds-justify-between ds-align-center">
                        <div>
                            <button type="button" class="ds-btn ds-btn--sm ds-btn--secondary" onclick="aplicarHorarioGeneral()">
                                🔄 Aplicar horario a todos los días activos
                            </button>
                        </div>
                        <div class="ds-flex ds-gap-3">
                            <a href="<?= base_url('/agenda'); ?>" class="ds-btn ds-btn--secondary">
                                <span class="ds-btn__icon ds-btn__icon--left">❌</span>
                                Cancelar
                            </a>
                            <button type="submit" class="ds-btn ds-btn--primary">
                                <span class="ds-btn__icon ds-btn__icon--left">💾</span>
                                Guardar Horario
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleDay(diaNum) {
    const activo = document.getElementById('activo_' + diaNum).checked;
    const inicio = document.getElementById('inicio_' + diaNum);
    const fin = document.getElementById('fin_' + diaNum);
    
    if (!activo) {
        inicio.value = '';
        fin.value = '';
    }
}

function aplicarHorarioGeneral() {
    const horaInicio = prompt('Hora de inicio (formato HH:MM):', '09:00');
    if (!horaInicio) return;
    
    const horaFin = prompt('Hora de fin (formato HH:MM):', '18:00');
    if (!horaFin) return;
    
    // Aplicar a todos los días que estén activos
    for (let i = 1; i <= 7; i++) {
        const activo = document.getElementById('activo_' + i);
        if (activo && activo.checked) {
            document.getElementById('inicio_' + i).value = horaInicio;
            document.getElementById('fin_' + i).value = horaFin;
        }
    }
}
</script>
<?= $this->endSection(); ?>
