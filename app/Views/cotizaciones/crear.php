<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <div>
            <h1 class="ds-page__title">💵 Nueva Cotización</h1>
            <p class="ds-page__subtitle">Completa los detalles de la cotización</p>
        </div>
        <div class="ds-page__actions">
            <a href="<?= base_url('/cotizaciones/nueva') ?>" class="ds-btn ds-btn--secondary">
                <span class="ds-btn__icon">↤</span> Cambiar Paciente
            </a>
        </div>
    </div>

    <!-- Tarjeta del Paciente Seleccionado -->
    <div class="ds-card ds-mb-4">
        <div class="ds-card__header" style="display: flex; align-items: center; gap: 15px;">
            <div class="ds-avatar ds-avatar--lg ds-avatar--primary">
                <?= strtoupper(substr($paciente['nombre'] ?? 'P', 0, 1)) ?>
            </div>
            <div style="flex: 1;">
                <h3 class="ds-card__title">👤 <?= esc($paciente['nombre'] ?? '') ?> <?= esc($paciente['primer_apellido'] ?? '') ?> <?= esc($paciente['segundo_apellido'] ?? '') ?></h3>
                <p class="ds-text-muted">Identificación: <?= esc($paciente['identificacion'] ?? 'N/A') ?> | Teléfono: <?= esc($paciente['celular'] ?? $paciente['telefono'] ?? 'N/A') ?></p>
            </div>
            <div>
                <p class="ds-text-muted ds-text-sm">Vigencia hasta:</p>
                <input type="date" id="fecha_vigencia" class="ds-input" value="<?= date('Y-m-d', strtotime('+15 days')) ?>" style="width: 160px;">
            </div>
        </div>
    </div>
    
    <form id="formCotizacion" class="ds-form">
        <input type="hidden" name="id_paciente" value="<?= $paciente['id'] ?>">
        <input type="hidden" name="fecha_vigencia" id="input_vigencia">

        <!-- Sección de Servicios -->
        <div class="ds-card ds-mb-4">
            <div class="ds-card__header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="ds-card__title">🦷 Servicios a Cotizar</h2>
                <span class="ds-badge ds-badge--primary"><span id="serviciosCount">0</span> servicio(s)</span>
            </div>
            <div class="ds-card__body">
                <div class="ds-table-responsive">
                    <table class="ds-table" id="tablaServicios" style="margin-bottom: 20px;">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th style="width: 40%;">Servicio</th>
                                <th style="width: 18%;">Precio Unitario</th>
                                <th style="width: 12%; text-align: center;">Cantidad</th>
                                <th style="width: 18%; text-align: right;">Subtotal</th>
                                <th style="width: 12%; text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Filas dinámicas -->
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #e8f4f8;">
                                <td colspan="3" class="ds-text-right ds-font-bold" style="font-size: 16px;">TOTAL:</td>
                                <td class="ds-text-right"><strong id="totalGlobal" style="font-size: 20px; color: #0066cc;">$0.00</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="button" class="ds-btn ds-btn--secondary" onclick="agregarFila()" style="width: 100%; margin-top: 10px;">
                    <span class="ds-btn__icon">➕</span> Agregar Servicio
                </button>
            </div>
        </div>
        
        <!-- Sección de Observaciones -->
        <div class="ds-card ds-mb-4">
            <div class="ds-card__header">
                <h2 class="ds-card__title">📝 Observaciones</h2>
            </div>
            <div class="ds-card__body">
                <div class="ds-form__group">
                    <label for="observaciones" class="ds-label">Notas o condiciones especiales</label>
                    <textarea class="ds-input ds-textarea" id="observaciones" name="observaciones" rows="4" placeholder="Ej: Incluye anestesia local, garantía de 6 meses, etc."></textarea>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="ds-flex ds-gap-3" style="justify-content: flex-end; margin-bottom: 20px;">
            <a href="<?= base_url('/cotizaciones') ?>" class="ds-btn ds-btn--secondary" style="padding: 12px 30px; font-size: 16px;">
                <span class="ds-btn__icon">✕</span> Cancelar
            </a>
            <button type="button" id="btnGuardar" class="ds-btn ds-btn--primary" onclick="guardarCotizacion()" style="padding: 12px 30px; font-size: 16px;">
                <span class="ds-btn__icon">💾</span> Guardar Cotización
            </button>
        </div>
    </form>
</div>

<!-- DATA STORE -->
<script>
    const serviciosDB = <?= json_encode($servicios) ?>;
</script>

<!-- Template -->
<template id="filaTemplate">
    <tr style="background-color: white; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor='white'">
        <td>
            <select class="ds-input ds-select servicio-select" name="servicios[]" onchange="actualizarPrecio(this)" required>
                <option value="">Seleccione servicio...</option>
                <?php foreach($servicios as $ser): ?>
                    <option value="<?= $ser['id'] ?>"><?= esc($ser['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" step="0.01" class="ds-input precio-input" name="precios[]" oninput="calcularFila(this)" required placeholder="$0.00" style="text-align: right;">
        </td>
        <td style="text-align: center;">
            <input type="number" class="ds-input cantidad-input" name="cantidades[]" value="1" min="1" oninput="calcularFila(this)" required style="text-align: center; width: 60px;">
        </td>
        <td style="text-align: right;">
            <input type="text" class="ds-input subtotal-input" readonly style="text-align: right; font-weight: bold; background-color: #f9f9f9;">
        </td>
        <td style="text-align: center;">
            <button type="button" class="ds-btn ds-btn--danger ds-btn--sm" onclick="eliminarFila(this)" title="Eliminar">🗑️</button>
        </td>
    </tr>
</template>

<script>
    function actualizarContador() {
        const count = document.querySelectorAll('#tablaServicios tbody tr').length;
        document.getElementById('serviciosCount').textContent = count;
    }

    function agregarFila() {
        const template = document.getElementById('filaTemplate');
        const clone = template.content.cloneNode(true);
        document.querySelector('#tablaServicios tbody').appendChild(clone);
        actualizarContador();
    }

    function eliminarFila(btn) {
        const tbody = document.querySelector('#tablaServicios tbody');
        if (tbody.children.length > 1) {
            btn.closest('tr').remove();
            calcularTotalGlobal();
            actualizarContador();
        } else {
            alert('⚠️ Debe haber al menos un servicio en la cotización');
        }
    }

    function actualizarPrecio(select) {
        const id = select.value;
        const servicio = serviciosDB.find(s => s.id == id);
        const row = select.closest('tr');
        const precioInput = row.querySelector('.precio-input');
        
        if(servicio) {
            precioInput.value = servicio.precio_base;
        } else {
            precioInput.value = '';
        }
        calcularFila(select);
    }

    function calcularFila(element) {
        const row = element.closest('tr');
        const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
        const cantidad = parseInt(row.querySelector('.cantidad-input').value) || 0;
        const subtotal = precio * cantidad;
        
        row.querySelector('.subtotal-input').value = '$' + subtotal.toFixed(2);
        calcularTotalGlobal();
    }

    function calcularTotalGlobal() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(input => {
            const value = input.value.replace('$', '');
            total += parseFloat(value) || 0;
        });
        document.getElementById('totalGlobal').innerText = '$' + total.toFixed(2);
    }

    function guardarCotizacion() {
        // Sincronizar fecha
        document.getElementById('input_vigencia').value = document.getElementById('fecha_vigencia').value;
        
        const form = document.getElementById('formCotizacion');
        const tbody = document.querySelector('#tablaServicios tbody');
        const btnGuardar = document.getElementById('btnGuardar');
        
        // Validar que haya al menos un servicio
        if (tbody.children.length === 0) {
            alert('⚠️ Debe agregar al menos un servicio a la cotización');
            return;
        }
        
        if(!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Mostrar indicador de carga
        const originalText = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="ds-btn__icon">⏳</span> Guardando...';

        const formData = new FormData(form);
        fetch('<?= base_url('/cotizaciones/guardar') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('✅ Cotización guardada correctamente');
                window.open('<?= base_url('/cotizaciones/imprimir/') ?>' + data.id_cotizacion, '_blank');
                setTimeout(() => {
                    window.location.href = '<?= base_url('/historial/paciente/') . $paciente['id'] ?>';
                }, 1000);
            } else {
                alert('❌ Error al guardar la cotización');
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error de conexión al guardar');
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = originalText;
        });
    }

    // Init
    document.addEventListener('DOMContentLoaded', agregarFila);
</script>
<?= $this->endSection() ?>
