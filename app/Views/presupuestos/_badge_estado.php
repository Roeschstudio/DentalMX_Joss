<?php
// Safety check for undefined estado
if (!isset($estado) || empty($estado)) {
    $estado = 'borrador';
}

$badgeClass = [
    'borrador' => 'secondary',
    'pendiente' => 'warning',
    'aprobado' => 'success',
    'rechazado' => 'danger',
    'convertido' => 'info'
];

$badgeText = [
    'borrador' => 'Borrador',
    'pendiente' => 'Pendiente',
    'aprobado' => 'Aprobado',
    'rechazado' => 'Rechazado',
    'convertido' => 'Convertido'
];
?>

<span class="ds-badge ds-badge--<?= $badgeClass[$estado] ?? 'secondary' ?>">
    <?= $badgeText[$estado] ?? 'Desconocido' ?>
</span>
