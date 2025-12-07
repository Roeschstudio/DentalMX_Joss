<?php
/**
 * Partial view for odontograma teeth
 * Uses FDI (Fédération Dentaire Internationale) numbering system
 * 
 * Adults:
 * - Quadrant 1: Upper Right (18-11)
 * - Quadrant 2: Upper Left (21-28)
 * - Quadrant 3: Lower Left (31-38)
 * - Quadrant 4: Lower Right (41-48)
 * 
 * Children:
 * - Quadrant 5: Upper Right (55-51)
 * - Quadrant 6: Upper Left (61-65)
 * - Quadrant 7: Lower Left (71-75)
 * - Quadrant 8: Lower Right (81-85)
 */

$width = 50;
$height = 50;

// Adult teeth
$dientesAdultosSuperior = array_merge(
    [18, 17, 16, 15, 14, 13, 12, 11], // Right
    [21, 22, 23, 24, 25, 26, 27, 28]  // Left
);

$dientesAdultosInferior = array_merge(
    [48, 47, 46, 45, 44, 43, 42, 41], // Right
    [31, 32, 33, 34, 35, 36, 37, 38]  // Left
);

// Child teeth
$dientesInfantilesSuperior = array_merge(
    [55, 54, 53, 52, 51], // Right
    [61, 62, 63, 64, 65]  // Left
);

$dientesInfantilesInferior = array_merge(
    [85, 84, 83, 82, 81], // Right
    [71, 72, 73, 74, 75]  // Left
);

// Helper function to get surface color
function getSuperficieColor($dientes, $numeroDiente, $superficie, $colores) {
    $campo = 'sup_' . $superficie;
    $codigo = $dientes[$numeroDiente][$campo] ?? 'S001';
    return $colores[$codigo]['color'] ?? '#4CAF50';
}

// Helper function to check if tooth is absent
function isDienteAusente($dientes, $numeroDiente) {
    $estado = $dientes[$numeroDiente]['estado'] ?? 'presente';
    return in_array($estado, ['ausente', 'extraido']);
}
?>

<div class="odontograma-content">
    <!-- Adult teeth - Upper arch -->
    <div class="odontograma-fila odontograma-fila--adultos-superior" style="<?= $tipoDentadura === 'decidua' ? 'display:none;' : '' ?>">
        <?php foreach ($dientesAdultosSuperior as $index => $numeroDiente): ?>
            <?php if ($index === 8): ?>
            <div class="odontograma-linea-media"></div>
            <?php endif; ?>
            <div class="odontograma-diente <?= isDienteAusente($dientes, $numeroDiente) ? 'odontograma-diente--ausente' : '' ?>" 
                 data-diente="<?= $numeroDiente ?>">
                <span class="odontograma-diente__numero"><?= $numeroDiente ?></span>
                <svg class="diente-svg" viewBox="0 0 100 100" width="<?= $width ?>" height="<?= $height ?>">
                    <!-- Oclusal/Incisal (center) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_oclusal'] ?? 'S001' ?>" 
                             data-superficie="oclusal"
                             points="35,35 65,35 65,65 35,65"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'oclusal', $colores) ?>"/>
                    <!-- Vestibular (top) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_vestibular'] ?? 'S001' ?>" 
                             data-superficie="vestibular"
                             points="10,10 90,10 65,35 35,35"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'vestibular', $colores) ?>"/>
                    <!-- Lingual (bottom) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_lingual'] ?? 'S001' ?>" 
                             data-superficie="lingual"
                             points="35,65 65,65 90,90 10,90"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'lingual', $colores) ?>"/>
                    <!-- Mesial (left for upper right, right for upper left) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_mesial'] ?? 'S001' ?>" 
                             data-superficie="mesial"
                             points="<?= $numeroDiente < 20 ? '65,35 90,10 90,90 65,65' : '10,10 35,35 35,65 10,90' ?>"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'mesial', $colores) ?>"/>
                    <!-- Distal (right for upper right, left for upper left) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_distal'] ?? 'S001' ?>" 
                             data-superficie="distal"
                             points="<?= $numeroDiente < 20 ? '10,10 35,35 35,65 10,90' : '65,35 90,10 90,90 65,65' ?>"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'distal', $colores) ?>"/>
                </svg>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Child teeth - Upper arch -->
    <div class="odontograma-fila odontograma-fila--infantiles-superior" style="<?= $tipoDentadura === 'permanente' ? 'display:none;' : '' ?>">
        <div style="width: 150px;"></div> <!-- Spacer for alignment -->
        <?php foreach ($dientesInfantilesSuperior as $index => $numeroDiente): ?>
            <?php if ($index === 5): ?>
            <div class="odontograma-linea-media"></div>
            <?php endif; ?>
            <div class="odontograma-diente <?= isDienteAusente($dientes, $numeroDiente) ? 'odontograma-diente--ausente' : '' ?>" 
                 data-diente="<?= $numeroDiente ?>">
                <span class="odontograma-diente__numero"><?= $numeroDiente ?></span>
                <svg class="diente-svg" viewBox="0 0 100 100" width="<?= $width ?>" height="<?= $height ?>">
                    <polygon class="superficie" data-superficie="oclusal" points="35,35 65,35 65,65 35,65"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'oclusal', $colores) ?>"/>
                    <polygon class="superficie" data-superficie="vestibular" points="10,10 90,10 65,35 35,35"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'vestibular', $colores) ?>"/>
                    <polygon class="superficie" data-superficie="lingual" points="35,65 65,65 90,90 10,90"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'lingual', $colores) ?>"/>
                    <polygon class="superficie" data-superficie="mesial" 
                             points="<?= $numeroDiente < 60 ? '65,35 90,10 90,90 65,65' : '10,10 35,35 35,65 10,90' ?>"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'mesial', $colores) ?>"/>
                    <polygon class="superficie" data-superficie="distal" 
                             points="<?= $numeroDiente < 60 ? '10,10 35,35 35,65 10,90' : '65,35 90,10 90,90 65,65' ?>"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'distal', $colores) ?>"/>
                </svg>
            </div>
        <?php endforeach; ?>
        <div style="width: 150px;"></div> <!-- Spacer for alignment -->
    </div>

    <!-- Separator line -->
    <div class="odontograma-separador--horizontal"></div>

    <!-- Child teeth - Lower arch -->
    <div class="odontograma-fila odontograma-fila--infantiles-inferior" style="<?= $tipoDentadura === 'permanente' ? 'display:none;' : '' ?>">
        <div style="width: 150px;"></div> <!-- Spacer for alignment -->
        <?php foreach ($dientesInfantilesInferior as $index => $numeroDiente): ?>
            <?php if ($index === 5): ?>
            <div class="odontograma-linea-media"></div>
            <?php endif; ?>
            <div class="odontograma-diente <?= isDienteAusente($dientes, $numeroDiente) ? 'odontograma-diente--ausente' : '' ?>" 
                 data-diente="<?= $numeroDiente ?>">
                <span class="odontograma-diente__numero"><?= $numeroDiente ?></span>
                <svg class="diente-svg" viewBox="0 0 100 100" width="<?= $width ?>" height="<?= $height ?>">
                    <polygon class="superficie" data-superficie="oclusal" points="35,35 65,35 65,65 35,65"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'oclusal', $colores) ?>"/>
                    <polygon class="superficie" data-superficie="vestibular" points="35,65 65,65 90,90 10,90"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'vestibular', $colores) ?>"/>
                    <polygon class="superficie" data-superficie="lingual" points="10,10 90,10 65,35 35,35"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'lingual', $colores) ?>"/>
                    <polygon class="superficie" data-superficie="mesial" 
                             points="<?= $numeroDiente > 80 ? '10,10 35,35 35,65 10,90' : '65,35 90,10 90,90 65,65' ?>"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'mesial', $colores) ?>"/>
                    <polygon class="superficie" data-superficie="distal" 
                             points="<?= $numeroDiente > 80 ? '65,35 90,10 90,90 65,65' : '10,10 35,35 35,65 10,90' ?>"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'distal', $colores) ?>"/>
                </svg>
            </div>
        <?php endforeach; ?>
        <div style="width: 150px;"></div> <!-- Spacer for alignment -->
    </div>

    <!-- Adult teeth - Lower arch -->
    <div class="odontograma-fila odontograma-fila--adultos-inferior" style="<?= $tipoDentadura === 'decidua' ? 'display:none;' : '' ?>">
        <?php foreach ($dientesAdultosInferior as $index => $numeroDiente): ?>
            <?php if ($index === 8): ?>
            <div class="odontograma-linea-media"></div>
            <?php endif; ?>
            <div class="odontograma-diente <?= isDienteAusente($dientes, $numeroDiente) ? 'odontograma-diente--ausente' : '' ?>" 
                 data-diente="<?= $numeroDiente ?>">
                <span class="odontograma-diente__numero"><?= $numeroDiente ?></span>
                <svg class="diente-svg" viewBox="0 0 100 100" width="<?= $width ?>" height="<?= $height ?>">
                    <!-- Oclusal/Incisal (center) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_oclusal'] ?? 'S001' ?>" 
                             data-superficie="oclusal"
                             points="35,35 65,35 65,65 35,65"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'oclusal', $colores) ?>"/>
                    <!-- Vestibular (bottom for lower teeth) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_vestibular'] ?? 'S001' ?>" 
                             data-superficie="vestibular"
                             points="35,65 65,65 90,90 10,90"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'vestibular', $colores) ?>"/>
                    <!-- Lingual (top for lower teeth) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_lingual'] ?? 'S001' ?>" 
                             data-superficie="lingual"
                             points="10,10 90,10 65,35 35,35"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'lingual', $colores) ?>"/>
                    <!-- Mesial (right for lower right, left for lower left) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_mesial'] ?? 'S001' ?>" 
                             data-superficie="mesial"
                             points="<?= $numeroDiente > 40 ? '10,10 35,35 35,65 10,90' : '65,35 90,10 90,90 65,65' ?>"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'mesial', $colores) ?>"/>
                    <!-- Distal (left for lower right, right for lower left) -->
                    <polygon class="superficie superficie-<?= $dientes[$numeroDiente]['sup_distal'] ?? 'S001' ?>" 
                             data-superficie="distal"
                             points="<?= $numeroDiente > 40 ? '65,35 90,10 90,90 65,65' : '10,10 35,35 35,65 10,90' ?>"
                             style="fill: <?= getSuperficieColor($dientes, $numeroDiente, 'distal', $colores) ?>"/>
                </svg>
            </div>
        <?php endforeach; ?>
    </div>
</div>
