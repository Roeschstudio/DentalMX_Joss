<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <div>
            <h1 class="ds-page__title">📋 Nueva Receta Médica</h1>
            <p class="ds-page__subtitle">Selecciona un paciente para crear una nueva receta</p>
        </div>
        <div class="ds-page__actions">
            <a href="<?= base_url('/recetas') ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon">←</span> Volver
            </a>
        </div>
    </div>

    <?php if (!empty($pacientes)): ?>
        <!-- Búsqueda rápida -->
        <div class="ds-card ds-mb-4">
            <div class="ds-card__body">
                <input type="text" id="searchPacientes" class="ds-input" placeholder="🔍 Buscar paciente por nombre, apellido o identificación..." onkeyup="filtrarPacientes()">
            </div>
        </div>

        <!-- Grilla de pacientes -->
        <div class="ds-grid" id="pacientesGrid" style="gap: 20px; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
            <?php foreach ($pacientes as $paciente): ?>
            <div class="paciente-card" data-search="<?= strtolower(esc($paciente['nombre'] . ' ' . ($paciente['primer_apellido'] ?? '') . ' ' . ($paciente['identificacion'] ?? ''))) ?>">
                <div class="ds-card" style="height: 100%; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='none'; this.style.boxShadow=''">
                    <div class="ds-card__header" style="display: flex; align-items: center; gap: 15px;">
                        <div class="ds-avatar ds-avatar--lg ds-avatar--primary">
                            <?= strtoupper(substr($paciente['nombre'] ?? 'P', 0, 1)) ?>
                        </div>
                        <div style="flex: 1;">
                            <h3 class="ds-card__title" style="margin: 0 0 5px 0;"><?= esc($paciente['nombre']) ?></h3>
                            <p class="ds-text-muted" style="margin: 0; font-size: 12px;"><?= esc($paciente['primer_apellido'] ?? '') ?> <?= esc($paciente['segundo_apellido'] ?? '') ?></p>
                        </div>
                    </div>
                    <div class="ds-card__body">
                        <div class="ds-grid" style="gap: 10px;">
                            <div>
                                <p class="ds-text-muted ds-text-sm">Identificación</p>
                                <p class="ds-font-bold"><?= esc($paciente['identificacion'] ?? 'N/A') ?></p>
                            </div>
                            <div>
                                <p class="ds-text-muted ds-text-sm">Teléfono</p>
                                <p class="ds-font-bold"><?= esc($paciente['telefono'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>
                    <div style="border-top: 1px solid #eee; padding: 15px; text-align: center;">
                        <a href="<?= base_url('/recetas/crear/' . $paciente['id']) ?>" class="ds-btn ds-btn--primary ds-btn--full">
                            <span class="ds-btn__icon">➕</span> Crear Receta
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="ds-empty-state" style="padding: 60px 20px;">
            <div class="ds-empty-state__icon" style="font-size: 64px; margin-bottom: 20px;">👥</div>
            <h3 class="ds-empty-state__text">No hay pacientes disponibles</h3>
            <p class="ds-text-muted">Primero debes crear pacientes antes de poder crear recetas.</p>
            <div class="ds-empty-state__action">
                <a href="<?= base_url('/pacientes/nuevo') ?>" class="ds-btn ds-btn--primary">
                    <span class="ds-btn__icon">➕</span> Crear Paciente
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function filtrarPacientes() {
        const searchText = document.getElementById('searchPacientes').value.toLowerCase();
        const cards = document.querySelectorAll('.paciente-card');
        
        cards.forEach(card => {
            const searchData = card.getAttribute('data-search');
            if (searchData.includes(searchText)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
<?= $this->endSection() ?>
