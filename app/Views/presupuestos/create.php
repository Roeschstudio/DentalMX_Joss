<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">➕ Crear Presupuesto</h1>
    </div>
    <div class="ds-container ds-container--fluid">
    <div class="ds-row">
        <div class="ds-col-12">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">Crear Nuevo Presupuesto</h3>
                    <div class="ds-card__actions">
                        <a href="<?= base_url('/presupuestos') ?>" class="ds-btn ds-btn--secondary ds-btn--sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="ds-card__body">
                    <?= form_open('/presupuestos/store', ['id' => 'presupuestoForm']) ?>
                        <div class="ds-row">
                            <!-- Datos principales -->
                            <div class="ds-col-md-6">
                                <div class="ds-form-group">
                                    <label for="folio" class="ds-label">Folio</label>
                                    <input type="text" name="folio" class="ds-input" 
                                           id="folio" value="<?= $folio ?>" readonly>
                                </div>
                            </div>
                            <div class="ds-col-md-6">
                                <div class="ds-form-group">
                                    <label for="fecha_emision" class="ds-label">Fecha Emisión</label>
                                    <input type="text" name="fecha_emision" class="ds-input" 
                                           id="fecha_emision" value="<?= date('d/m/Y H:i') ?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-row">
                            <div class="ds-col-md-6">
                                <div class="ds-form-group">
                                    <label for="id_paciente" class="ds-label">Paciente</label>
                                    <select name="id_paciente" class="ds-input ds-select" id="id_paciente" required>
                                        <option value="">Seleccionar Paciente</option>
                                        <?php foreach ($pacientes as $paciente): ?>
                                        <option value="<?= $paciente['id'] ?>">
                                            <?= $paciente['nombre'] . ' ' . $paciente['primer_apellido'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="ds-col-md-6">
                                <div class="ds-form-group">
                                    <label for="fecha_vigencia" class="ds-label">Fecha Vigencia</label>
                                    <input type="date" name="fecha_vigencia" class="ds-input" 
                                           id="fecha_vigencia" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-row">
                            <div class="ds-col-md-6">
                                <div class="ds-form-group">
                                    <label for="id_usuario" class="ds-label">Médico</label>
                                    <select name="id_usuario" class="ds-input ds-select" id="id_usuario" required>
                                        <option value="">Seleccionar Médico</option>
                                        <!-- Aquí se cargarían los médicos dinámicamente, por ahora hardcodeado o pasado desde controller -->
                                        <option value="<?= session()->get('id') ?? 1 ?>" selected>
                                            <?= session()->get('nombre') ?? 'Usuario Actual' ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="ds-col-md-6">
                                <div class="ds-form-group">
                                    <label for="observaciones" class="ds-label">Observaciones</label>
                                    <textarea name="observaciones" class="ds-input ds-textarea" 
                                              id="observaciones" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-divider"></div>
                        
                        <!-- Detalles del presupuesto -->
                        <div class="ds-row">
                            <div class="ds-col-12">
                                <h5 class="ds-text-base ds-font-semibold">Detalles del Presupuesto</h5>
                                <div class="ds-table-responsive">
                                    <table class="ds-table ds-table--bordered" id="detallesTable">
                                        <thead>
                                            <tr>
                                                <th>Servicio</th>
                                                <th>Descripción</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unitario</th>
                                                <th>Descuento %</th>
                                                <th>Subtotal</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select name="detalles[0][id_servicio]" class="ds-input ds-select servicio-select" required>
                                                        <option value="">Seleccionar</option>
                                                        <?php foreach ($servicios as $servicio): ?>
                                                        <option value="<?= $servicio['id'] ?>" 
                                                                data-precio="<?= $servicio['precio_base'] ?>">
                                                            <?= $servicio['nombre'] ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="detalles[0][descripcion]" 
                                                           class="ds-input descripcion-input">
                                                </td>
                                                <td>
                                                    <input type="number" name="detalles[0][cantidad]" 
                                                           class="ds-input cantidad-input" 
                                                           value="1" min="0.01" step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="detalles[0][precio_unitario]" 
                                                           class="ds-input precio-input" 
                                                           value="0" min="0" step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="detalles[0][descuento_porcentaje]" 
                                                           class="ds-input descuento-input" 
                                                           value="0" min="0" max="100" step="0.01">
                                                </td>
                                                <td>
                                                    <input type="text" class="ds-input subtotal-text" 
                                                           value="0.00" readonly>
                                                    <input type="hidden" name="detalles[0][subtotal]" class="subtotal-hidden" value="0">
                                                </td>
                                                <td>
                                                    <button type="button" class="ds-btn ds-btn--danger ds-btn--sm remove-row">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <button type="button" class="ds-btn ds-btn--success ds-btn--sm" id="addRow">
                                    <i class="fas fa-plus"></i> Agregar Fila
                                </button>
                            </div>
                        </div>
                        
                        <div class="ds-divider"></div>
                        
                        <!-- Totales -->
                        <div class="ds-row">
                            <div class="ds-col-md-8">
                                <!-- Espacio vacío para alinear totales a la derecha -->
                            </div>
                            <div class="ds-col-md-4">
                                <div class="ds-form-group">
                                    <label for="subtotal" class="ds-label">Subtotal:</label>
                                    <input type="text" name="subtotal_display" class="ds-input" id="subtotal" value="0.00" readonly>
                                </div>
                                <div class="ds-form-group">
                                    <label for="iva" class="ds-label">IVA (16%):</label>
                                    <input type="text" name="iva_display" class="ds-input" id="iva" value="0.00" readonly>
                                </div>
                                <div class="ds-form-group">
                                    <label for="total" class="ds-label">Total:</label>
                                    <input type="text" name="total_display" class="ds-input ds-font-bold" id="total" value="0.00" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ds-row">
                            <div class="ds-col-12 ds-flex ds-gap-2">
                                <button type="submit" class="ds-btn ds-btn--primary">Guardar Presupuesto</button>
                                <a href="<?= base_url('/presupuestos') ?>" class="ds-btn ds-btn--secondary">Cancelar</a>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let rowIndex = 1;

// Agregar nueva fila
document.getElementById('addRow').addEventListener('click', function() {
    const tbody = document.querySelector('#detallesTable tbody');
    const newRow = document.createElement('tr');
    
    newRow.innerHTML = `
        <td>
            <select name="detalles[${rowIndex}][id_servicio]" class="ds-input ds-select servicio-select" required>
                <option value="">Seleccionar</option>
                <?php foreach ($servicios as $servicio): ?>
                <option value="<?= $servicio['id'] ?>" data-precio="<?= $servicio['precio_base'] ?>">
                    <?= $servicio['nombre'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="text" name="detalles[${rowIndex}][descripcion]" class="ds-input descripcion-input">
        </td>
        <td>
            <input type="number" name="detalles[${rowIndex}][cantidad]" class="ds-input cantidad-input" 
                   value="1" min="0.01" step="0.01" required>
        </td>
        <td>
            <input type="number" name="detalles[${rowIndex}][precio_unitario]" class="ds-input precio-input" 
                   value="0" min="0" step="0.01" required>
        </td>
        <td>
            <input type="number" name="detalles[${rowIndex}][descuento_porcentaje]" class="ds-input descuento-input" 
                   value="0" min="0" max="100" step="0.01">
        </td>
        <td>
            <input type="text" class="ds-input subtotal-text" value="0.00" readonly>
            <input type="hidden" name="detalles[${rowIndex}][subtotal]" class="subtotal-hidden" value="0">
        </td>
        <td>
            <button type="button" class="ds-btn ds-btn--danger ds-btn--sm remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    rowIndex++;
    
    // Agregar event listeners a la nueva fila
    addRowEventListeners(newRow);
});

// Eliminar fila
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        const row = e.target.closest('tr');
        const tbody = row.parentNode;
        
        // No permitir eliminar si es la única fila
        if (tbody.children.length > 1) {
            row.remove();
            calculateTotals();
        }
    }
});

// Calcular subtotal de una fila
function calculateRowSubtotal(row) {
    const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
    const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
    const descuento = parseFloat(row.querySelector('.descuento-input').value) || 0;
    
    let subtotal = cantidad * precio;
    subtotal = subtotal - (subtotal * (descuento / 100));
    
    row.querySelector('.subtotal-text').value = subtotal.toFixed(2);
    
    // Actualizar el campo hidden con el subtotal calculado
    const hiddenInput = row.querySelector('.subtotal-hidden');
    if (hiddenInput) {
        hiddenInput.value = subtotal.toFixed(2);
    }
    
    return subtotal;
}

// Calcular totales
function calculateTotals() {
    const rows = document.querySelectorAll('#detallesTable tbody tr');
    let subtotal = 0;
    
    rows.forEach(row => {
        subtotal += calculateRowSubtotal(row);
    });
    
    const iva = subtotal * 0.16;
    const total = subtotal + iva;
    
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('iva').value = iva.toFixed(2);
    document.getElementById('total').value = total.toFixed(2);
}

// Agregar event listeners a una fila
function addRowEventListeners(row) {
    // Cambio de servicio
    row.querySelector('.servicio-select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const precio = selectedOption.getAttribute('data-precio');
        if (precio) {
            row.querySelector('.precio-input').value = precio;
        }
        calculateRowSubtotal(row);
        calculateTotals();
    });
    
    // Cambio en cantidad, precio o descuento
    ['cantidad-input', 'precio-input', 'descuento-input'].forEach(className => {
        row.querySelector('.' + className).addEventListener('input', function() {
            calculateRowSubtotal(row);
            calculateTotals();
        });
    });
}

// Inicializar event listeners para la primera fila
document.querySelectorAll('#detallesTable tbody tr').forEach(addRowEventListeners);

// Calcular totales iniciales
calculateTotals();
</script>
<?= $this->endSection() ?>
