<?= $this->extend('layout/ds_layout'); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url('css/components/timeline.css'); ?>">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="ds-page">
    <div class="ds-page__header">
        <div class="ds-page__header-content">
            <a href="<?= base_url('/historial/' . $paciente['id']); ?>" class="ds-btn ds-btn--outline ds-btn--sm mb-2">
                ← Volver al historial
            </a>
            <h1 class="ds-page__title">Detalle de Actividad</h1>
        </div>
    </div>

    <div class="ds-grid ds-grid--2">
        <!-- Información Principal -->
        <div class="ds-grid__col--lg-8">
            <div class="ds-card">
                <div class="ds-card__header">
                    <div class="ds-card__header-content">
                        <span class="ds-badge <?= $tipo_config['class']; ?> ds-badge--lg">
                            <?= $tipo_config['icon']; ?> <?= $tipo_config['label']; ?>
                        </span>
                        <h2 class="ds-card__title mt-2">
                            Actividad #<?= $actividad['id']; ?>
                        </h2>
                    </div>
                </div>
                <div class="ds-card__body">
                    <!-- Descripción -->
                    <div class="ds-detail-section">
                        <h3 class="ds-detail-section__title">📝 Descripción</h3>
                        <p class="ds-detail-section__content">
                            <?= esc($actividad['descripcion'] ?? 'Sin descripción registrada'); ?>
                        </p>
                    </div>

                    <!-- Información de la Actividad -->
                    <div class="ds-detail-section">
                        <h3 class="ds-detail-section__title">📋 Información General</h3>
                        <div class="ds-info-grid">
                            <div class="ds-info-grid__item">
                                <span class="ds-info-grid__label">Fecha y Hora:</span>
                                <span class="ds-info-grid__value">
                                    <?= date('d/m/Y H:i', strtotime($actividad['fecha_actividad'])); ?>
                                </span>
                            </div>
                            <div class="ds-info-grid__item">
                                <span class="ds-info-grid__label">Tipo:</span>
                                <span class="ds-info-grid__value">
                                    <?= $tipo_config['icon']; ?> <?= $tipo_config['label']; ?>
                                </span>
                            </div>
                            <div class="ds-info-grid__item">
                                <span class="ds-info-grid__label">Médico:</span>
                                <span class="ds-info-grid__value">
                                    👨‍⚕️ <?= esc(($actividad['medico_nombre'] ?? '') . ' ' . ($actividad['medico_apellido'] ?? '')); ?>
                                </span>
                            </div>
                            <div class="ds-info-grid__item">
                                <span class="ds-info-grid__label">Registrado:</span>
                                <span class="ds-info-grid__value">
                                    <?= date('d/m/Y H:i', strtotime($actividad['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Datos Adicionales según Tipo -->
                    <?php if (!empty($datos_adicionales)): ?>
                    <div class="ds-detail-section">
                        <h3 class="ds-detail-section__title">📑 Información Detallada</h3>
                        <div class="ds-data-card">
                            <?php 
                            switch($actividad['tipo_actividad']):
                                case 'cita':
                            ?>
                                <div class="ds-info-grid">
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Servicio:</span>
                                        <span class="ds-info-grid__value"><?= esc($datos_adicionales['servicio_nombre'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Estado:</span>
                                        <span class="ds-info-grid__value"><?= esc($datos_adicionales['estado'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Hora Inicio:</span>
                                        <span class="ds-info-grid__value"><?= esc($datos_adicionales['hora_inicio'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Duración:</span>
                                        <span class="ds-info-grid__value"><?= esc($datos_adicionales['duracion'] ?? 'N/A'); ?> min</span>
                                    </div>
                                </div>
                                <?php if (!empty($datos_adicionales['notas'])): ?>
                                <div class="ds-info-grid__full">
                                    <span class="ds-info-grid__label">Notas:</span>
                                    <p><?= esc($datos_adicionales['notas']); ?></p>
                                </div>
                                <?php endif; ?>
                            <?php break; case 'tratamiento': ?>
                                <div class="ds-info-grid">
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Servicio:</span>
                                        <span class="ds-info-grid__value"><?= esc($datos_adicionales['servicio_nombre'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Estado:</span>
                                        <span class="ds-badge ds-badge--<?= ($datos_adicionales['estado'] ?? 'iniciado') === 'completado' ? 'success' : 'warning'; ?>">
                                            <?= ucfirst(str_replace('_', ' ', $datos_adicionales['estado'] ?? 'N/A')); ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($datos_adicionales['diente'])): ?>
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Diente:</span>
                                        <span class="ds-info-grid__value">🦷 <?= esc($datos_adicionales['diente']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($datos_adicionales['superficie'])): ?>
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Superficie:</span>
                                        <span class="ds-info-grid__value"><?= ucfirst(esc($datos_adicionales['superficie'])); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Costo:</span>
                                        <span class="ds-info-grid__value">$<?= number_format($datos_adicionales['costo'] ?? 0, 2); ?></span>
                                    </div>
                                    <div class="ds-info-grid__item">
                                        <span class="ds-info-grid__label">Pagado:</span>
                                        <span class="ds-info-grid__value">$<?= number_format($datos_adicionales['pagado'] ?? 0, 2); ?></span>
                                    </div>
                                </div>
                            <?php break; case 'receta': ?>
                                <p>Ver receta completa: <a href="<?= base_url('/recetas/' . $actividad['id_referencia']); ?>" class="ds-link">Receta #<?= $actividad['id_referencia']; ?></a></p>
                            <?php break; case 'presupuesto': case 'cotizacion': ?>
                                <p>Ver presupuesto completo: <a href="<?= base_url('/presupuestos/show/' . $actividad['id_referencia']); ?>" class="ds-link">Presupuesto #<?= $actividad['id_referencia']; ?></a></p>
                            <?php break; case 'nota_evolucion': ?>
                                <div class="ds-info-grid__full">
                                    <span class="ds-info-grid__label">Contenido de la nota:</span>
                                    <p><?= nl2br(esc($datos_adicionales['contenido'] ?? $datos_adicionales['descripcion'] ?? 'N/A')); ?></p>
                                </div>
                            <?php break; default: ?>
                                <pre class="ds-code"><?= json_encode($datos_adicionales, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                            <?php endswitch; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Archivos Adjuntos -->
                    <div class="ds-detail-section">
                        <h3 class="ds-detail-section__title">📎 Archivos Adjuntos</h3>
                        <?php if (empty($adjuntos)): ?>
                        <p class="ds-text-muted">No hay archivos adjuntos para esta actividad.</p>
                        <?php else: ?>
                        <div class="ds-file-list">
                            <?php foreach ($adjuntos as $adjunto): ?>
                            <div class="ds-file-item">
                                <div class="ds-file-item__icon">
                                    <?= \App\Models\HistorialAdjuntosModel::getIconoTipo($adjunto['tipo_archivo']); ?>
                                </div>
                                <div class="ds-file-item__info">
                                    <span class="ds-file-item__name"><?= esc($adjunto['nombre_archivo']); ?></span>
                                    <span class="ds-file-item__meta">
                                        <?= (new \App\Models\HistorialAdjuntosModel())->formatearTamanio($adjunto['tamanio_archivo']); ?>
                                        • <?= date('d/m/Y', strtotime($adjunto['created_at'])); ?>
                                    </span>
                                </div>
                                <a href="<?= base_url('/historial/adjuntos/descargar/' . $adjunto['id']); ?>" class="ds-btn ds-btn--sm ds-btn--outline">
                                    📥 Descargar
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Formulario para subir adjuntos -->
                        <div class="ds-upload-form mt-3">
                            <form id="uploadForm" enctype="multipart/form-data">
                                <input type="hidden" name="id_actividad" value="<?= $actividad['id']; ?>">
                                <div class="ds-form-group">
                                    <label class="ds-form-label">Agregar archivo</label>
                                    <div class="ds-file-upload">
                                        <input type="file" id="archivo" name="archivo" class="ds-file-upload__input">
                                        <label for="archivo" class="ds-file-upload__label">
                                            📁 Seleccionar archivo...
                                        </label>
                                    </div>
                                </div>
                                <div class="ds-form-group">
                                    <input type="text" name="descripcion" class="ds-form-input" placeholder="Descripción del archivo (opcional)">
                                </div>
                                <button type="submit" class="ds-btn ds-btn--primary ds-btn--sm">
                                    📤 Subir Archivo
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="ds-grid__col--lg-4">
            <!-- Información del Paciente -->
            <div class="ds-card mb-4">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">👤 Paciente</h3>
                </div>
                <div class="ds-card__body">
                    <div class="ds-patient-mini">
                        <div class="ds-avatar ds-avatar--md">
                            <?= strtoupper(substr($paciente['nombre'] ?? 'P', 0, 1)); ?>
                        </div>
                        <div class="ds-patient-mini__info">
                            <strong><?= esc($paciente['nombre'] . ' ' . $paciente['primer_apellido']); ?></strong>
                            <span>ID: <?= $paciente['id']; ?></span>
                        </div>
                    </div>
                    <div class="ds-action-buttons mt-3">
                        <a href="<?= base_url('/pacientes/' . $paciente['id']); ?>" class="ds-btn ds-btn--outline ds-btn--sm ds-btn--block">
                            Ver ficha completa
                        </a>
                        <a href="<?= base_url('/historial/' . $paciente['id']); ?>" class="ds-btn ds-btn--outline ds-btn--sm ds-btn--block">
                            Ver historial
                        </a>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">⚙️ Acciones</h3>
                </div>
                <div class="ds-card__body">
                    <div class="ds-action-list">
                        <?php if ($actividad['tipo_actividad'] === 'cita'): ?>
                        <a href="<?= base_url('/citas/' . $actividad['id_referencia']); ?>" class="ds-action-list__item">
                            📅 Ver cita original
                        </a>
                        <?php endif; ?>
                        <?php if ($actividad['tipo_actividad'] === 'receta'): ?>
                        <a href="<?= base_url('/recetas/imprimir/' . $actividad['id_referencia']); ?>" class="ds-action-list__item" target="_blank">
                            🖨️ Imprimir receta
                        </a>
                        <?php endif; ?>
                        <?php if (in_array($actividad['tipo_actividad'], ['presupuesto', 'cotizacion'])): ?>
                        <a href="<?= base_url('/presupuestos/pdf/' . $actividad['id_referencia']); ?>" class="ds-action-list__item" target="_blank">
                            📄 Ver PDF del presupuesto
                        </a>
                        <?php endif; ?>
                        <button class="ds-action-list__item ds-action-list__item--danger" onclick="confirmarEliminacion()">
                            🗑️ Eliminar actividad
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="ds-modal" id="deleteModal">
    <div class="ds-modal__dialog">
        <div class="ds-modal__content">
            <div class="ds-modal__header">
                <h2 class="ds-modal__title">⚠️ Confirmar Eliminación</h2>
                <button type="button" class="ds-modal__close" onclick="cerrarModal()">✕</button>
            </div>
            <div class="ds-modal__body">
                <p>¿Está seguro de que desea eliminar esta actividad del historial?</p>
                <p class="ds-text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="ds-modal__footer">
                <button class="ds-btn ds-btn--outline" onclick="cerrarModal()">Cancelar</button>
                <button class="ds-btn ds-btn--danger" onclick="eliminarActividad()">Eliminar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
const actividadId = <?= $actividad['id']; ?>;
const pacienteId = <?= $paciente['id']; ?>;

// Subir archivo
document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('<?= base_url('/historial/adjuntos/subir/' . $actividad['id']); ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al subir el archivo');
    }
});

// Mostrar nombre de archivo seleccionado
document.getElementById('archivo').addEventListener('change', function() {
    const label = this.nextElementSibling;
    if (this.files.length > 0) {
        label.textContent = '📁 ' + this.files[0].name;
    }
});

// Modal de eliminación
function confirmarEliminacion() {
    document.getElementById('deleteModal').classList.add('ds-modal--show');
    document.getElementById('deleteModal').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('deleteModal').classList.remove('ds-modal--show');
    document.getElementById('deleteModal').style.display = 'none';
}

async function eliminarActividad() {
    try {
        const response = await fetch('<?= base_url('/historial/eliminar/' . $actividad['id']); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = '<?= base_url('/historial/' . $paciente['id']); ?>';
        } else {
            alert('Error: ' + (data.error || 'Error desconocido'));
            cerrarModal();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar la actividad');
        cerrarModal();
    }
}
</script>
<?= $this->endSection(); ?>
