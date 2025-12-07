<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">💊 Catálogo de Medicamentos</h1>
        <button class="ds-btn ds-btn--primary" onclick="abrirModal()">
            ➕ Nuevo Medicamento
        </button>
    </div>

    <div class="ds-card">
        <div class="ds-card__body">
            <?php if (empty($medicamentos)): ?>
                <div class="ds-empty-state">
                    <div class="ds-empty-state__icon">💊</div>
                    <h3 class="ds-empty-state__text">No hay medicamentos registrados</h3>
                    <p class="ds-text-muted">Haz clic en "Nuevo Medicamento" para agregar el primero.</p>
                </div>
            <?php else: ?>
                <div class="ds-table-container">
                    <table class="ds-table ds-table--striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Sustancia</th>
                                <th>Presentación</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($medicamentos as $med): ?>
                            <tr>
                                <td><?= esc($med['nombre_comercial']) ?></td>
                                <td><?= esc($med['sustancia_activa'] ?? '-') ?></td>
                                <td><?= esc($med['presentacion'] ?? '-') ?></td>
                                <td>
                                    <span class="ds-badge <?= ($med['stock'] ?? 0) > 0 ? 'ds-badge--success' : 'ds-badge--danger' ?>">
                                        <?= $med['stock'] ?? 0 ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="ds-btn-group">
                                        <button class="ds-btn ds-btn--warning ds-btn--sm" onclick='editar(<?= json_encode($med) ?>)'>
                                            ✏️ Editar
                                        </button>
                                        <button class="ds-btn ds-btn--danger ds-btn--sm" onclick="borrar(<?= $med['id'] ?>)">
                                            🗑️ Borrar
                                        </button>
                                    </div>
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

<!-- Modal usando Design System -->
<div class="ds-modal-overlay" id="modalMedicamento" onclick="if(event.target === this) cerrarModal()">
    <div class="ds-modal ds-modal--lg">
        <div class="ds-modal__header">
            <h2 class="ds-modal__title" id="modalTitle">💊 Nuevo Medicamento</h2>
            <button type="button" class="ds-modal__close" onclick="cerrarModal()">✕</button>
        </div>
        <div class="ds-modal__body">
            <form id="formMedicamento">
                <input type="hidden" id="id" name="id">
                <div class="ds-grid ds-grid--2">
                    <div class="ds-form-group">
                        <label for="nombre_comercial" class="ds-label ds-label--required">Nombre Comercial</label>
                        <input type="text" class="ds-input" name="nombre_comercial" id="nombre_comercial" required placeholder="Ej: Amoxicilina 500mg">
                    </div>
                    <div class="ds-form-group">
                        <label for="sustancia_activa" class="ds-label">Sustancia Activa</label>
                        <input type="text" class="ds-input" name="sustancia_activa" id="sustancia_activa" placeholder="Ej: Amoxicilina">
                    </div>
                </div>
                <div class="ds-grid ds-grid--2">
                    <div class="ds-form-group">
                        <label for="presentacion" class="ds-label">Presentación</label>
                        <input type="text" class="ds-input" name="presentacion" id="presentacion" placeholder="Ej: Caja con 21 cápsulas">
                    </div>
                    <div class="ds-form-group">
                        <label for="stock" class="ds-label">Stock</label>
                        <input type="number" class="ds-input" name="stock" id="stock" value="0" min="0">
                    </div>
                </div>
                <div class="ds-form-group">
                    <label for="indicaciones_base" class="ds-label">Indicaciones Base</label>
                    <textarea class="ds-input" name="indicaciones_base" id="indicaciones_base" rows="3" placeholder="Indicaciones generales para este medicamento"></textarea>
                </div>
            </form>
        </div>
        <div class="ds-modal__footer">
            <button type="button" class="ds-btn ds-btn--secondary" onclick="cerrarModal()">❌ Cancelar</button>
            <button type="button" class="ds-btn ds-btn--primary" onclick="guardar()">💾 Guardar</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Funciones del modal
function abrirModal() {
    document.getElementById('formMedicamento').reset();
    document.getElementById('id').value = '';
    document.getElementById('modalTitle').textContent = '💊 Nuevo Medicamento';
    document.getElementById('modalMedicamento').classList.add('is-active');
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    document.getElementById('modalMedicamento').classList.remove('is-active');
    document.body.style.overflow = '';
}

function editar(data) {
    document.getElementById('id').value = data.id;
    document.getElementById('nombre_comercial').value = data.nombre_comercial || '';
    document.getElementById('sustancia_activa').value = data.sustancia_activa || '';
    document.getElementById('presentacion').value = data.presentacion || '';
    document.getElementById('stock').value = data.stock || 0;
    document.getElementById('indicaciones_base').value = data.indicaciones_base || '';
    document.getElementById('modalTitle').textContent = '✏️ Editar Medicamento';
    document.getElementById('modalMedicamento').classList.add('is-active');
    document.body.style.overflow = 'hidden';
}

function guardar() {
    const form = document.getElementById('formMedicamento');
    if(!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    const plainFormData = Object.fromEntries(formData.entries());
    
    fetch('<?= base_url('/medicamentos/save') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(plainFormData)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showToast('success', 'Éxito', 'Medicamento guardado correctamente');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('danger', 'Error', 'Error al guardar: ' + (JSON.stringify(data.errors) || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('danger', 'Error', 'Error de conexión');
    });
}

function borrar(id) {
    if(confirm('¿Seguro que deseas eliminar este medicamento?')) {
        fetch('<?= base_url('/medicamentos/delete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({id: id})
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showToast('success', 'Éxito', 'Medicamento eliminado');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('danger', 'Error', 'Error al eliminar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('danger', 'Error', 'Error de conexión');
        });
    }
}

// Cerrar con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});
</script>
<?= $this->endSection() ?>
