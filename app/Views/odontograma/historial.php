<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/components/odontograma.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <!-- Encabezado -->
    <div class="ds-page__header">
        <div>
            <a href="<?= base_url('odontograma/' . $paciente['id']) ?>" class="ds-btn ds-btn--outline ds-btn--sm">
                ⬅️ Volver al Odontograma
            </a>
        </div>
        <h1 class="ds-page__title">
            🕒 Historial de Odontograma
        </h1>
    </div>

    <!-- Información del Paciente -->
    <div class="ds-card ds-mb-4">
        <div class="ds-card__body">
            <div class="ds-grid ds-grid--3">
                <div>
                    <span class="ds-text-muted ds-text-sm">Paciente</span>
                    <p class="ds-font-semibold ds-mb-0"><?= esc($paciente['nombre']) ?> <?= esc($paciente['primer_apellido']) ?></p>
                </div>
                <div>
                    <span class="ds-text-muted ds-text-sm">Total de cambios</span>
                    <p class="ds-mb-0">
                        <span class="ds-badge ds-badge--primary ds-badge--lg"><?= $estadisticas['total_cambios'] ?></span>
                    </p>
                </div>
                <div>
                    <span class="ds-text-muted ds-text-sm">Fechas con actividad</span>
                    <p class="ds-font-semibold ds-mb-0"><?= count($fechasDisponibles) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="ds-grid ds-grid--2 ds-mb-4">
        <!-- Por tipo de acción -->
        <div class="ds-card">
            <div class="ds-card__header">
                <h3 class="ds-card__title">Cambios por tipo</h3>
            </div>
            <div class="ds-card__body">
                <?php if (!empty($estadisticas['por_tipo'])): ?>
                    <?php foreach ($estadisticas['por_tipo'] as $tipo): ?>
                    <div class="ds-flex ds-justify-between ds-items-center ds-py-2 ds-border-bottom">
                        <span class="ds-text-capitalize"><?= esc($tipo['tipo_accion']) ?></span>
                        <span class="ds-badge ds-badge--secondary"><?= $tipo['cantidad'] ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="ds-text-muted ds-text-center">Sin datos</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Dientes más modificados -->
        <div class="ds-card">
            <div class="ds-card__header">
                <h3 class="ds-card__title">Dientes más modificados</h3>
            </div>
            <div class="ds-card__body">
                <?php if (!empty($estadisticas['dientes_mas_modificados'])): ?>
                    <?php foreach ($estadisticas['dientes_mas_modificados'] as $diente): ?>
                    <div class="ds-flex ds-justify-between ds-items-center ds-py-2 ds-border-bottom">
                        <span>Diente <strong><?= esc($diente['numero_diente']) ?></strong></span>
                        <span class="ds-badge ds-badge--info"><?= $diente['cantidad'] ?> cambios</span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="ds-text-muted ds-text-center">Sin datos</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabla de historial -->
    <div class="ds-card">
        <div class="ds-card__header">
            <h3 class="ds-card__title">Historial de cambios</h3>
            <div class="ds-flex ds-gap-2">
                <select id="filtro-diente" class="ds-form__select ds-form__select--sm" style="width: auto;">
                    <option value="">Todos los dientes</option>
                    <?php for ($i = 11; $i <= 18; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                    <?php for ($i = 21; $i <= 28; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                    <?php for ($i = 31; $i <= 38; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                    <?php for ($i = 41; $i <= 48; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="ds-card__body">
            <?php if (!empty($historial)): ?>
            <div class="ds-table-responsive">
                <table class="ds-table" id="tabla-historial">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Diente</th>
                            <th>Acción</th>
                            <th>Campo</th>
                            <th>Valor Anterior</th>
                            <th>Valor Nuevo</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $item): ?>
                        <tr data-diente="<?= esc($item['diente']) ?>">
                            <td><?= esc($item['fecha']) ?></td>
                            <td>
                                <span class="ds-badge ds-badge--outline"><?= esc($item['diente']) ?></span>
                            </td>
                            <td><?= esc($item['accion']) ?></td>
                            <td><?= esc($item['campo']) ?></td>
                            <td>
                                <span class="ds-badge ds-badge--secondary ds-badge--sm"><?= esc($item['anterior'] ?? 'N/A') ?></span>
                            </td>
                            <td>
                                <span class="ds-badge ds-badge--primary ds-badge--sm"><?= esc($item['nuevo']) ?></span>
                            </td>
                            <td><?= esc($item['usuario']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="ds-empty-state">
                <div class="ds-empty-state__icon">
                    <i class="fas fa-history"></i>
                </div>
                <h4 class="ds-empty-state__title">Sin historial</h4>
                <p class="ds-empty-state__description">
                    Aún no hay cambios registrados en el odontograma de este paciente.
                </p>
                <a href="<?= base_url('odontograma/' . $paciente['id']) ?>" class="ds-btn ds-btn--primary">
                    Ir al Odontograma
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filtro = document.getElementById('filtro-diente');
        const tabla = document.getElementById('tabla-historial');
        
        if (filtro && tabla) {
            filtro.addEventListener('change', function() {
                const diente = this.value;
                const filas = tabla.querySelectorAll('tbody tr');
                
                filas.forEach(fila => {
                    if (!diente || fila.dataset.diente === diente) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
