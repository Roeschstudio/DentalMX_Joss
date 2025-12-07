<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <div>
            <h1 class="ds-page__title">📋 Nueva Receta Médica</h1>
            <p class="ds-page__subtitle">Completa los detalles de la receta médica</p>
        </div>
        <div class="ds-page__actions">
            <a href="<?= base_url('/recetas/nueva') ?>" class="ds-btn ds-btn--secondary">
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
        </div>
    </div>

    <!-- Formulario de Receta -->
    <form id="formReceta" class="ds-form">
        <input type="hidden" name="id_paciente" value="<?= $paciente['id'] ?>">
        
        <!-- Sección de Medicamentos -->
        <div class="ds-card ds-mb-4">
            <div class="ds-card__header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="ds-card__title">💊 Medicamentos Prescritos</h2>
                <span class="ds-badge ds-badge--primary"><span id="medicamentosCount">0</span> medicamento(s)</span>
            </div>
            <div class="ds-card__body">
                <div class="ds-table-responsive">
                    <table class="ds-table" id="tablaMedicamentos" style="margin-bottom: 20px;">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th style="width: 35%;">Medicamento</th>
                                <th style="width: 20%;">Dosis</th>
                                <th style="width: 20%;">Duración</th>
                                <th style="width: 15%; text-align: center;">Cantidad</th>
                                <th style="width: 10%; text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Filas dinámicas aquí -->
                        </tbody>
                    </table>
                </div>

                <button type="button" class="ds-btn ds-btn--secondary" onclick="agregarFila()" style="width: 100%; margin-top: 10px;">
                    <span class="ds-btn__icon">➕</span> Agregar Medicamento
                </button>
            </div>
        </div>

        <!-- Sección de Notas -->
        <div class="ds-card ds-mb-4">
            <div class="ds-card__header">
                <h2 class="ds-card__title">📝 Notas Adicionales</h2>
            </div>
            <div class="ds-card__body">
                <div class="ds-form__group">
                    <label for="notas_adicionales" class="ds-label">Observaciones o Instrucciones Especiales</label>
                    <textarea class="ds-input ds-textarea" id="notas_adicionales" name="notas_adicionales" rows="5" placeholder="Ej: Tomar medicamentos con alimentos, evitar ciertos medicamentos, etc."></textarea>
                    <p class="ds-text-muted ds-text-sm" style="margin-top: 5px;">Máximo 500 caracteres</p>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="ds-flex ds-gap-3" style="justify-content: flex-end; margin-bottom: 20px;">
            <a href="<?= base_url('/recetas') ?>" class="ds-btn ds-btn--secondary" style="padding: 12px 30px; font-size: 16px;">
                <span class="ds-btn__icon">✕</span> Cancelar
            </a>
            <button type="button" id="btnGuardar" class="ds-btn ds-btn--primary" onclick="guardarReceta()" style="padding: 12px 30px; font-size: 16px;">
                <span class="ds-btn__icon">💾</span> Guardar Receta
            </button>
        </div>
    </form>
</div>

<!-- Template oculto para filas -->
<template id="filaTemplate">
    <tr style="background-color: white; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor='white'">
        <td>
            <select class="ds-input ds-select" name="medicamentos[]" required onchange="actualizarContador()">
                <option value="">Seleccione medicamento...</option>
                <?php if (!empty($medicamentos)): ?>
                    <?php foreach($medicamentos as $med): ?>
                        <option value="<?= $med['id'] ?>"><?= esc($med['nombre_comercial'] ?? '') ?> (<?= esc($med['presentacion'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </td>
        <td><input type="text" class="ds-input" name="dosis[]" placeholder="Ej: 1 cada 8hrs" required style="font-size: 13px;"></td>
        <td><input type="text" class="ds-input" name="duracion[]" placeholder="Ej: 5 días" required style="font-size: 13px;"></td>
        <td style="text-align: center;"><input type="number" class="ds-input" name="cantidad[]" value="1" min="1" required style="text-align: center; font-size: 13px;"></td>
        <td style="text-align: center;">
            <button type="button" class="ds-btn ds-btn--danger ds-btn--sm" onclick="eliminarFila(this)" title="Eliminar">
                🗑️
            </button>
        </td>
    </tr>
</template>

<script>
    function actualizarContador() {
        const count = document.querySelectorAll('#tablaMedicamentos tbody tr').length;
        document.getElementById('medicamentosCount').textContent = count;
    }

    function agregarFila() {
        const template = document.getElementById('filaTemplate');
        const clone = template.content.cloneNode(true);
        document.querySelector('#tablaMedicamentos tbody').appendChild(clone);
        actualizarContador();
    }

    function eliminarFila(btn) {
        const tbody = document.querySelector('#tablaMedicamentos tbody');
        if (tbody.children.length > 1) {
            btn.closest('tr').remove();
            actualizarContador();
        } else {
            alert('⚠️ Debe haber al menos un medicamento en la receta');
        }
    }

    function guardarReceta() {
        const form = document.getElementById('formReceta');
        const tbody = document.querySelector('#tablaMedicamentos tbody');
        const btnGuardar = document.getElementById('btnGuardar');
        
        // Validar que haya al menos un medicamento
        if (tbody.children.length === 0) {
            alert('⚠️ Debe agregar al menos un medicamento a la receta');
            return;
        }

        // Validar que todos los campos requeridos estén llenos
        if(!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Mostrar indicador de carga
        const originalText = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="ds-btn__icon">⏳</span> Guardando...';

        const formData = new FormData(form);
        
        fetch('<?= base_url('/recetas/guardar') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('✅ Receta guardada correctamente');
                // Abrir receta para imprimir en nueva pestaña
                window.open('<?= base_url('/recetas/imprimir/') ?>' + data.id_receta, '_blank');
                // Redirigir al historial del paciente después de 1 segundo
                setTimeout(() => {
                    window.location.href = '<?= base_url('/historial/paciente/') . $paciente['id'] ?>';
                }, 1000);
            } else {
                alert('❌ Error: ' + (data.message || 'Error desconocido al guardar la receta'));
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error de conexión al guardar la receta');
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = originalText;
        });
    }

    // Inicializar con una fila al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        agregarFila();
    });
</script>
<?= $this->endSection() ?>
