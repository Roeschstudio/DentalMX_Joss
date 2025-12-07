<?php
/**
 * Tab de Odontograma para Historia Clínica
 * 
 * Este componente se integra en historial_clinica_form.php
 * Implementa el Sistema FDI (Federación Dental Internacional)
 * 
 * Variables esperadas:
 * - $paciente: Datos del paciente (con 'id')
 * - $isNew: Boolean indicando si es nuevo registro
 */

$pacienteId = $paciente['id'] ?? 0;
$width = 50;
$height = 50;

// Estructura de dientes FDI
$dientesAdultosSuperior = array_merge(
    [18, 17, 16, 15, 14, 13, 12, 11],
    [21, 22, 23, 24, 25, 26, 27, 28]
);
$dientesAdultosInferior = array_merge(
    [48, 47, 46, 45, 44, 43, 42, 41],
    [31, 32, 33, 34, 35, 36, 37, 38]
);
$dientesInfantilesSuperior = array_merge(
    [55, 54, 53, 52, 51],
    [61, 62, 63, 64, 65]
);
$dientesInfantilesInferior = array_merge(
    [85, 84, 83, 82, 81],
    [71, 72, 73, 74, 75]
);
?>

<div class="ds-card">
    <div class="ds-card__header ds-flex ds-justify-between ds-items-center">
        <div>
            <h3 class="ds-card__title">
                <i class="fas fa-tooth"></i> Odontograma
            </h3>
            <p class="ds-text-sm ds-text-muted ds-mb-0">
                Sistema FDI (Federación Dental Internacional) - Click en una superficie para cambiar su estado
            </p>
        </div>
        <?php if ($pacienteId > 0): ?>
        <div class="ds-flex ds-gap-2">
            <a href="<?= base_url('odontograma/' . $pacienteId) ?>" class="ds-btn ds-btn--primary ds-btn--sm" target="_blank">
                <i class="fas fa-expand"></i> Abrir Completo
            </a>
            <a href="<?= base_url('odontograma/' . $pacienteId . '/historial') ?>" class="ds-btn ds-btn--outline ds-btn--sm" target="_blank">
                <i class="fas fa-history"></i> Historial
            </a>
        </div>
        <?php endif; ?>
    </div>
    <div class="ds-card__body">
        <?php if ($pacienteId > 0): ?>
        <div id="odontograma-tab-container" class="odontograma-container" data-paciente="<?= $pacienteId ?>">
            <!-- Toolbar -->
            <div class="odontograma-toolbar ds-mb-3">
                <div class="odontograma-toolbar__grupo">
                    <span class="odontograma-toolbar__label">Vista:</span>
                    <select id="tipo-dentadura-tab" class="ds-form__select ds-form__select--sm" style="width: auto;">
                        <option value="adultos">Adultos (Permanente)</option>
                        <option value="infantiles">Infantiles (Decidua)</option>
                        <option value="mixta">Mixta</option>
                    </select>
                </div>
                <div class="odontograma-toolbar__grupo">
                    <span class="odontograma-toolbar__label">Estado:</span>
                    <select id="estado-activo" class="ds-form__select ds-form__select--sm" style="width: auto;">
                        <option value="S001" data-color="#4CAF50">Sano</option>
                        <option value="S002" data-color="#F44336">Cariado</option>
                        <option value="S003" data-color="#2196F3">Obturado</option>
                        <option value="S004" data-color="#FF9800">Fracturado</option>
                        <option value="S005" data-color="#9E9E9E">Desgastado</option>
                        <option value="S006" data-color="#FFD700">Corona</option>
                        <option value="S007" data-color="#FF6B35">Incrustación</option>
                        <option value="S008" data-color="#9C27B0">Sellante</option>
                    </select>
                    <span id="color-preview" class="odontograma-leyenda__color" style="width: 24px; height: 24px; background-color: #4CAF50;"></span>
                </div>
            </div>

            <!-- Contenedor del odontograma -->
            <div class="odontograma-wrapper">
                <!-- Arcada Superior Adultos -->
                <div class="odontograma-fila odontograma-fila--adultos-superior">
                    <?php foreach ($dientesAdultosSuperior as $index => $numeroDiente): ?>
                        <?php if ($index === 8): ?>
                        <div class="odontograma-linea-media"></div>
                        <?php endif; ?>
                        <div class="odontograma-diente" data-diente="<?= $numeroDiente ?>">
                            <span class="odontograma-diente__numero"><?= $numeroDiente ?></span>
                            <svg class="diente-svg" viewBox="0 0 100 100" width="<?= $width ?>" height="<?= $height ?>">
                                <polygon class="superficie superficie-S001" data-superficie="oclusal" points="35,35 65,35 65,65 35,65" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="vestibular" points="10,10 90,10 65,35 35,35" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="lingual" points="35,65 65,65 90,90 10,90" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="mesial" points="<?= $numeroDiente < 20 ? '65,35 90,10 90,90 65,65' : '10,10 35,35 35,65 10,90' ?>" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="distal" points="<?= $numeroDiente < 20 ? '10,10 35,35 35,65 10,90' : '65,35 90,10 90,90 65,65' ?>" style="fill: #4CAF50"/>
                            </svg>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Arcada Superior Infantiles -->
                <div class="odontograma-fila odontograma-fila--infantiles-superior" style="display:none;">
                    <div style="width: 150px;"></div>
                    <?php foreach ($dientesInfantilesSuperior as $index => $numeroDiente): ?>
                        <?php if ($index === 5): ?>
                        <div class="odontograma-linea-media"></div>
                        <?php endif; ?>
                        <div class="odontograma-diente" data-diente="<?= $numeroDiente ?>">
                            <span class="odontograma-diente__numero"><?= $numeroDiente ?></span>
                            <svg class="diente-svg" viewBox="0 0 100 100" width="<?= $width ?>" height="<?= $height ?>">
                                <polygon class="superficie superficie-S001" data-superficie="oclusal" points="35,35 65,35 65,65 35,65" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="vestibular" points="10,10 90,10 65,35 35,35" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="lingual" points="35,65 65,65 90,90 10,90" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="mesial" points="<?= $numeroDiente < 60 ? '65,35 90,10 90,90 65,65' : '10,10 35,35 35,65 10,90' ?>" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="distal" points="<?= $numeroDiente < 60 ? '10,10 35,35 35,65 10,90' : '65,35 90,10 90,90 65,65' ?>" style="fill: #4CAF50"/>
                            </svg>
                        </div>
                    <?php endforeach; ?>
                    <div style="width: 150px;"></div>
                </div>

                <!-- Separador -->
                <div class="odontograma-separador--horizontal"></div>

                <!-- Arcada Inferior Infantiles -->
                <div class="odontograma-fila odontograma-fila--infantiles-inferior" style="display:none;">
                    <div style="width: 150px;"></div>
                    <?php foreach ($dientesInfantilesInferior as $index => $numeroDiente): ?>
                        <?php if ($index === 5): ?>
                        <div class="odontograma-linea-media"></div>
                        <?php endif; ?>
                        <div class="odontograma-diente" data-diente="<?= $numeroDiente ?>">
                            <span class="odontograma-diente__numero"><?= $numeroDiente ?></span>
                            <svg class="diente-svg" viewBox="0 0 100 100" width="<?= $width ?>" height="<?= $height ?>">
                                <polygon class="superficie superficie-S001" data-superficie="oclusal" points="35,35 65,35 65,65 35,65" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="vestibular" points="35,65 65,65 90,90 10,90" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="lingual" points="10,10 90,10 65,35 35,35" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="mesial" points="<?= $numeroDiente > 80 ? '10,10 35,35 35,65 10,90' : '65,35 90,10 90,90 65,65' ?>" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="distal" points="<?= $numeroDiente > 80 ? '65,35 90,10 90,90 65,65' : '10,10 35,35 35,65 10,90' ?>" style="fill: #4CAF50"/>
                            </svg>
                        </div>
                    <?php endforeach; ?>
                    <div style="width: 150px;"></div>
                </div>

                <!-- Arcada Inferior Adultos -->
                <div class="odontograma-fila odontograma-fila--adultos-inferior">
                    <?php foreach ($dientesAdultosInferior as $index => $numeroDiente): ?>
                        <?php if ($index === 8): ?>
                        <div class="odontograma-linea-media"></div>
                        <?php endif; ?>
                        <div class="odontograma-diente" data-diente="<?= $numeroDiente ?>">
                            <span class="odontograma-diente__numero"><?= $numeroDiente ?></span>
                            <svg class="diente-svg" viewBox="0 0 100 100" width="<?= $width ?>" height="<?= $height ?>">
                                <polygon class="superficie superficie-S001" data-superficie="oclusal" points="35,35 65,35 65,65 35,65" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="vestibular" points="35,65 65,65 90,90 10,90" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="lingual" points="10,10 90,10 65,35 35,35" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="mesial" points="<?= $numeroDiente > 40 ? '10,10 35,35 35,65 10,90' : '65,35 90,10 90,90 65,65' ?>" style="fill: #4CAF50"/>
                                <polygon class="superficie superficie-S001" data-superficie="distal" points="<?= $numeroDiente > 40 ? '65,35 90,10 90,90 65,65' : '10,10 35,35 35,65 10,90' ?>" style="fill: #4CAF50"/>
                            </svg>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Leyenda -->
            <div class="odontograma-leyenda ds-mt-3">
                <div class="odontograma-leyenda__item">
                    <span class="odontograma-leyenda__color" style="background-color: #4CAF50;"></span>
                    <span class="odontograma-leyenda__label">Sano</span>
                </div>
                <div class="odontograma-leyenda__item">
                    <span class="odontograma-leyenda__color" style="background-color: #F44336;"></span>
                    <span class="odontograma-leyenda__label">Cariado</span>
                </div>
                <div class="odontograma-leyenda__item">
                    <span class="odontograma-leyenda__color" style="background-color: #2196F3;"></span>
                    <span class="odontograma-leyenda__label">Obturado</span>
                </div>
                <div class="odontograma-leyenda__item">
                    <span class="odontograma-leyenda__color" style="background-color: #FF9800;"></span>
                    <span class="odontograma-leyenda__label">Fracturado</span>
                </div>
                <div class="odontograma-leyenda__item">
                    <span class="odontograma-leyenda__color" style="background-color: #9E9E9E;"></span>
                    <span class="odontograma-leyenda__label">Desgastado</span>
                </div>
                <div class="odontograma-leyenda__item">
                    <span class="odontograma-leyenda__color" style="background-color: #FFD700;"></span>
                    <span class="odontograma-leyenda__label">Corona</span>
                </div>
                <div class="odontograma-leyenda__item">
                    <span class="odontograma-leyenda__color" style="background-color: #FF6B35;"></span>
                    <span class="odontograma-leyenda__label">Incrustación</span>
                </div>
                <div class="odontograma-leyenda__item">
                    <span class="odontograma-leyenda__color" style="background-color: #9C27B0;"></span>
                    <span class="odontograma-leyenda__label">Sellante</span>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('odontograma-tab-container');
            if (!container) return;
            
            const pacienteId = container.dataset.paciente;
            const estadoSelect = document.getElementById('estado-activo');
            const colorPreview = document.getElementById('color-preview');
            const tipoDentadura = document.getElementById('tipo-dentadura-tab');
            
            // Colores por estado
            const colores = {
                'S001': '#4CAF50', // Sano
                'S002': '#F44336', // Cariado
                'S003': '#2196F3', // Obturado
                'S004': '#FF9800', // Fracturado
                'S005': '#9E9E9E', // Desgastado
                'S006': '#FFD700', // Corona
                'S007': '#FF6B35', // Incrustación
                'S008': '#9C27B0', // Sellante
                'S009': '#795548', // Erosionado
                'S010': '#607D8B'  // Pigmentado
            };
            
            // Actualizar preview de color
            estadoSelect.addEventListener('change', function() {
                colorPreview.style.backgroundColor = colores[this.value] || '#4CAF50';
            });
            
            // Cambiar tipo de dentadura
            tipoDentadura.addEventListener('change', function() {
                const tipo = this.value;
                const adultosS = container.querySelectorAll('.odontograma-fila--adultos-superior, .odontograma-fila--adultos-inferior');
                const infantilesS = container.querySelectorAll('.odontograma-fila--infantiles-superior, .odontograma-fila--infantiles-inferior');
                
                adultosS.forEach(el => el.style.display = (tipo === 'adultos' || tipo === 'mixta') ? 'flex' : 'none');
                infantilesS.forEach(el => el.style.display = (tipo === 'infantiles' || tipo === 'mixta') ? 'flex' : 'none');
            });
            
            // Click en superficies
            container.addEventListener('click', function(e) {
                const superficie = e.target.closest('.superficie');
                if (!superficie) return;
                
                const diente = superficie.closest('.odontograma-diente');
                if (!diente) return;
                
                const numeroDiente = diente.dataset.diente;
                const superficieNombre = superficie.dataset.superficie;
                const estadoActual = estadoSelect.value;
                const color = colores[estadoActual];
                
                // Actualizar visualmente
                superficie.style.fill = color;
                superficie.className = `superficie superficie-${estadoActual}`;
                
                // Enviar al servidor
                const formData = new FormData();
                formData.append('id_paciente', pacienteId);
                formData.append('numero_diente', numeroDiente);
                formData.append('superficie', superficieNombre);
                formData.append('estado', estadoActual);
                
                fetch('/odontograma/api/superficie', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Error al guardar:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
            
            // Cargar datos existentes del odontograma
            if (pacienteId > 0) {
                fetch(`/odontograma/api/get/${pacienteId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.dientes) {
                            const dientes = data.data.dientes;
                            Object.keys(dientes).forEach(numDiente => {
                                const dienteData = dientes[numDiente];
                                const dienteEl = container.querySelector(`[data-diente="${numDiente}"]`);
                                if (!dienteEl) return;
                                
                                ['oclusal', 'vestibular', 'lingual', 'mesial', 'distal'].forEach(sup => {
                                    const estado = dienteData[`sup_${sup}`] || 'S001';
                                    const supEl = dienteEl.querySelector(`[data-superficie="${sup}"]`);
                                    if (supEl) {
                                        supEl.style.fill = colores[estado] || '#4CAF50';
                                        supEl.className = `superficie superficie-${estado}`;
                                    }
                                });
                            });
                        }
                    })
                    .catch(error => console.error('Error cargando odontograma:', error));
            }
        });
        </script>
        <?php else: ?>
        <div class="ds-alert ds-alert--warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Primero debe guardar los datos del paciente para acceder al odontograma.</span>
        </div>
        <?php endif; ?>
    </div>
</div>
