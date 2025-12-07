<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">⚙️ Catálogo de Servicios</h1>
        <button class="ds-btn ds-btn--primary" onclick="abrirModal()">
            ➕ Nuevo Servicio
        </button>
    </div>

    <div class="ds-card">
        <div class="ds-card__body">
            <?php if (empty($servicios)): ?>
                <div class="ds-empty-state">
                    <div class="ds-empty-state__icon">⚙️</div>
                    <h3 class="ds-empty-state__text">No hay servicios registrados</h3>
                    <p class="ds-text-muted">Haz clic en "Nuevo Servicio" para agregar el primero.</p>
                </div>
            <?php else: ?>
                <div class="ds-table-container">
                    <table class="ds-table ds-table--striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio Base</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($servicios as $ser): ?>
                            <tr>
                                <td><?= esc($ser['nombre']) ?></td>
                                <td><?= esc($ser['descripcion'] ?? '-') ?></td>
                                <td>
                                    <span class="ds-badge ds-badge--primary">
                                        $<?= number_format($ser['precio_base'] ?? 0, 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="ds-btn-group">
                                        <button class="ds-btn ds-btn--warning ds-btn--sm" onclick='editar(<?= json_encode($ser) ?>)'>
                                            ✏️ Editar
                                        </button>
                                        <button class="ds-btn ds-btn--danger ds-btn--sm" onclick="borrar(<?= $ser['id'] ?>)">
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
<div class="ds-modal-overlay" id="modalServicio" onclick="if(event.target === this) cerrarModal()">
    <div class="ds-modal">
        <div class="ds-modal__header">
            <h2 class="ds-modal__title" id="modalTitle">⚙️ Nuevo Servicio</h2>
            <button type="button" class="ds-modal__close" onclick="cerrarModal()">✕</button>
        </div>
        <div class="ds-modal__body">
            <form id="formServicio">
                <input type="hidden" id="id" name="id">
                <div class="ds-form-group">
                    <label for="nombre" class="ds-label ds-label--required">Nombre del Servicio</label>
                    <input type="text" class="ds-input" name="nombre" id="nombre" required placeholder="Ej: Limpieza dental">
                </div>
                <div class="ds-form-group">
                    <label for="descripcion" class="ds-label">Descripción</label>
                    <textarea class="ds-input" name="descripcion" id="descripcion" rows="3" placeholder="Descripción del servicio"></textarea>
                </div>
                <div class="ds-form-group">
                    <label for="precio_base" class="ds-label ds-label--required">Precio Base ($)</label>
                    <input type="number" step="0.01" class="ds-input" name="precio_base" id="precio_base" required min="0" placeholder="0.00">
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
    document.getElementById('formServicio').reset();
    document.getElementById('id').value = '';
    document.getElementById('modalTitle').textContent = '⚙️ Nuevo Servicio';
    document.getElementById('modalServicio').classList.add('is-active');
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    document.getElementById('modalServicio').classList.remove('is-active');
    document.body.style.overflow = '';
}

function editar(data) {
    document.getElementById('id').value = data.id;
    document.getElementById('nombre').value = data.nombre || '';
    document.getElementById('descripcion').value = data.descripcion || '';
    document.getElementById('precio_base').value = data.precio_base || 0;
    document.getElementById('modalTitle').textContent = '✏️ Editar Servicio';
    document.getElementById('modalServicio').classList.add('is-active');
    document.body.style.overflow = 'hidden';
}

function guardar() {
    const form = document.getElementById('formServicio');
    if(!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const plainFormData = Object.fromEntries(formData.entries());

    fetch('<?= base_url('/servicios/save') ?>', {
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
            showToast('success', 'Éxito', 'Servicio guardado correctamente');
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
    if(confirm('¿Eliminar servicio?')) {
        fetch('<?= base_url('/servicios/delete') ?>', {
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
                showToast('success', 'Éxito', 'Servicio eliminado');
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
