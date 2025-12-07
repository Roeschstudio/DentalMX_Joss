<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/components/odontograma.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <!-- Encabezado -->
    <div class="ds-page__header">
        <div>
            <a href="<?= base_url('pacientes/' . $paciente['id']) ?>" class="ds-btn ds-btn--outline ds-btn--sm">
                ⬅️ Volver
            </a>
        </div>
        <h1 class="ds-page__title">
            <i class="fas fa-tooth"></i> Odontograma
        </h1>
        <div class="ds-page__actions">
            <a href="<?= base_url('odontograma/' . $paciente['id'] . '/historial') ?>" class="ds-btn ds-btn--outline ds-btn--sm">
                <i class="fas fa-history"></i> Historial
            </a>
        </div>
    </div>

    <!-- Información del Paciente -->
    <div class="ds-card ds-mb-4">
        <div class="ds-card__body">
            <div class="ds-grid ds-grid--4">
                <div>
                    <span class="ds-text-muted ds-text-sm">Paciente</span>
                    <p class="ds-font-semibold ds-mb-0"><?= esc($paciente['nombre']) ?> <?= esc($paciente['primer_apellido']) ?> <?= esc($paciente['segundo_apellido'] ?? '') ?></p>
                </div>
                <div>
                    <span class="ds-text-muted ds-text-sm">Tipo de dentadura</span>
                    <p class="ds-font-semibold ds-mb-0 ds-text-capitalize"><?= esc($odontograma['tipo_dentadura']) ?></p>
                </div>
                <div>
                    <span class="ds-text-muted ds-text-sm">Estado general</span>
                    <p class="ds-mb-0">
                        <span class="ds-badge ds-badge--<?= $odontograma['estado_general'] === 'bueno' ? 'success' : ($odontograma['estado_general'] === 'regular' ? 'warning' : 'danger') ?>">
                            <?= ucfirst(esc($odontograma['estado_general'])) ?>
                        </span>
                    </p>
                </div>
                <div>
                    <span class="ds-text-muted ds-text-sm">Última actualización</span>
                    <p class="ds-font-semibold ds-mb-0"><?= date('d/m/Y', strtotime($odontograma['updated_at'] ?? $odontograma['created_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    <div class="odontograma-resumen ds-mb-4">
        <div class="odontograma-resumen__item">
            <div class="odontograma-resumen__valor"><?= $resumen['presentes'] ?></div>
            <div class="odontograma-resumen__label">Presentes</div>
        </div>
        <div class="odontograma-resumen__item">
            <div class="odontograma-resumen__valor"><?= $resumen['ausentes'] ?></div>
            <div class="odontograma-resumen__label">Ausentes</div>
        </div>
        <div class="odontograma-resumen__item">
            <div class="odontograma-resumen__valor" style="color: #F44336;"><?= $resumen['con_caries'] ?></div>
            <div class="odontograma-resumen__label">Con Caries</div>
        </div>
        <div class="odontograma-resumen__item">
            <div class="odontograma-resumen__valor" style="color: #2196F3;"><?= $resumen['obturados'] ?></div>
            <div class="odontograma-resumen__label">Obturados</div>
        </div>
        <div class="odontograma-resumen__item">
            <div class="odontograma-resumen__valor" style="color: #FF9800;"><?= $resumen['con_tratamiento_pendiente'] ?></div>
            <div class="odontograma-resumen__label">Trat. Pendientes</div>
        </div>
    </div>

    <!-- Odontograma Principal -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h3 class="ds-card__title">
                <i class="fas fa-teeth"></i> Odontograma Interactivo
            </h3>
            <p class="ds-text-sm ds-text-muted ds-mb-0">Click en una superficie para cambiar su estado • Doble click en un diente para editar</p>
        </div>
        <div class="ds-card__body">
            <div id="odontograma-container" class="odontograma-container" data-paciente="<?= $paciente['id'] ?>">
                <!-- Toolbar -->
                <div class="odontograma-toolbar">
                    <div class="odontograma-toolbar__grupo">
                        <span class="odontograma-toolbar__label">Vista:</span>
                        <select id="tipo-dentadura" class="ds-form__select ds-form__select--sm" style="width: auto;">
                            <option value="adultos" <?= $odontograma['tipo_dentadura'] === 'permanente' ? 'selected' : '' ?>>Adultos (Permanente)</option>
                            <option value="infantiles" <?= $odontograma['tipo_dentadura'] === 'decidua' ? 'selected' : '' ?>>Infantiles (Decidua)</option>
                            <option value="mixta" <?= $odontograma['tipo_dentadura'] === 'mixta' ? 'selected' : '' ?>>Mixta</option>
                        </select>
                    </div>
                </div>
                
                <!-- Odontograma SVG -->
                <div class="odontograma-wrapper">
                    <?= view('odontograma/_partial_dientes', [
                        'dientes' => $dientes,
                        'estructuraAdultos' => $estructuraAdultos,
                        'estructuraInfantiles' => $estructuraInfantiles,
                        'colores' => $colores,
                        'tipoDentadura' => $odontograma['tipo_dentadura']
                    ]) ?>
                </div>
                
                <!-- Leyenda -->
                <div class="odontograma-leyenda">
                    <?php foreach ($estados as $estado): ?>
                    <div class="odontograma-leyenda__item">
                        <span class="odontograma-leyenda__color" style="background-color: <?= esc($estado['color_hex']) ?>"></span>
                        <span class="odontograma-leyenda__label"><?= esc($estado['nombre']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial Reciente -->
    <?php if (!empty($historial)): ?>
    <div class="ds-card ds-mt-4">
        <div class="ds-card__header">
            <h3 class="ds-card__title">
                <i class="fas fa-history"></i> Cambios Recientes
            </h3>
            <a href="<?= base_url('odontograma/' . $paciente['id'] . '/historial') ?>" class="ds-btn ds-btn--outline ds-btn--sm">
                Ver todo
            </a>
        </div>
        <div class="ds-card__body">
            <div class="odontograma-historial">
                <?php foreach ($historial as $item): ?>
                <div class="odontograma-historial__item">
                    <span class="odontograma-historial__fecha"><?= esc($item['fecha']) ?></span>
                    <span class="odontograma-historial__descripcion">
                        Diente <?= esc($item['diente']) ?> - <?= esc($item['campo']) ?>:
                        <span class="odontograma-historial__valor odontograma-historial__valor--anterior"><?= esc($item['anterior'] ?? 'N/A') ?></span>
                        →
                        <span class="odontograma-historial__valor odontograma-historial__valor--nuevo"><?= esc($item['nuevo']) ?></span>
                    </span>
                    <span class="ds-text-sm ds-text-muted"><?= esc($item['usuario']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/odontograma.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cambio de tipo de dentadura
        document.getElementById('tipo-dentadura').addEventListener('change', function() {
            const tipo = this.value;
            // Mostrar/ocultar filas según el tipo
            document.querySelectorAll('.odontograma-fila--adultos-superior, .odontograma-fila--adultos-inferior').forEach(el => {
                el.style.display = (tipo === 'adultos' || tipo === 'mixta') ? 'flex' : 'none';
            });
            document.querySelectorAll('.odontograma-fila--infantiles-superior, .odontograma-fila--infantiles-inferior').forEach(el => {
                el.style.display = (tipo === 'infantiles' || tipo === 'mixta') ? 'flex' : 'none';
            });
        });
        
        // Disparar evento inicial
        document.getElementById('tipo-dentadura').dispatchEvent(new Event('change'));
    });
</script>
<?= $this->endSection() ?>
