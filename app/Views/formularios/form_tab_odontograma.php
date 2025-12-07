<?php
$width = 60;
$height = 60;
$dientesFilaSuperior = array_merge(
  range(18, 11), // del 18 al 11
  range(21, 28)  // del 21 al 28
);
$segundaFila = array_merge(
  array_fill(0, 3, null), // 3 columnas vacías al inicio
  range(55, 51),          // dientes del cuadrante 5 (de 55 a 51)
  range(61, 65),          // dientes del cuadrante 6 (de 61 a 65)
  array_fill(0, 3, null)  // 3 columnas vacías al final
);
$terceraFila = array_merge(
    array_fill(0, 3, null), // 3 columnas vacías al inicio
    range(85, 81),          // dientes del cuadrante 5 (55 a 51)
    range(71, 75),          // dientes del cuadrante 6 (61 a 65)
    array_fill(0, 3, null)  // 3 columnas vacías al final
);
$dientesFilaInferior= array_merge(
  range(48, 41), // del 18 al 11
  range(31, 38)  // del 21 al 28
);
?>
<div class="ds-row">
  <div class="ds-col-12">
    <table class="ds-table">
      <tr>
        <?php foreach ($dientesFilaSuperior as $numeroDiente): ?>
          <td>
            <div class="ds-text-center ds-font-bold ds-mb-1"><?= $numeroDiente ?></div>
            <svg version="1.1" id="Capa_<? $numeroDiente ?>" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;"
              xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
              x="0px" y="0px"
              width="<?= $width ?>"
              height="<?= $height ?>"
              viewBox="0 0 1080 1080"
              style="enable-background:new 0 0 1080 1080;" xml:space="preserve">
              <style type="text/css">
                .st0 {
                  fill: none;
                  stroke: #1D1D1B;
                  stroke-miterlimit: 10;
                  vector-effect: non-scaling-stroke;
                }
              </style>
              <switch>
                <g i:extraneous="self">
                  <polygon class="st0" points="85.03,994.97 85.03,85.03 346.01,346.01 346.01,733.99 		" />
                  <polygon class="st0" points="994.97,994.97 994.97,85.03 733.99,346.01 733.99,733.99 		" />
                  <polygon class="st0" points="85.03,85.03 994.97,85.03 733.99,346.01 346.01,346.01 		" />
                  <polygon class="st0" points="85.03,994.97 994.97,994.97 733.99,733.99 346.01,733.99 		" />
                  <rect x="346.01" y="346.01" class="st0" width="387.98" height="387.98" />
                </g>
              </switch>
            </svg>
          </td>
        <?php endforeach; ?>
      </tr>
      <tr>
        <?php foreach ($segundaFila as $numeroDiente): ?>
          <td>
            <?php if ($numeroDiente !== null): ?>
              <div class="ds-text-center ds-font-bold ds-mb-1"><?= $numeroDiente ?></div>
              <svg version="1.1" id="Capa_<? $numeroDiente ?>" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;"
                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                x="0px" y="0px"
                width="<?= $width ?>"
                height="<?= $height ?>"
                viewBox="0 0 1080 1080"
                style="enable-background:new 0 0 1080 1080;" xml:space="preserve">
                <style type="text/css">
                  .st0 {
                    fill: none;
                    stroke: #1D1D1B;
                    stroke-miterlimit: 10;
                    vector-effect: non-scaling-stroke;
                  }
                </style>
                <switch>
                  <g i:extraneous="self">
                    <polygon class="st0" points="85.03,994.97 85.03,85.03 346.01,346.01 346.01,733.99 		" />
                    <polygon class="st0" points="994.97,994.97 994.97,85.03 733.99,346.01 733.99,733.99 		" />
                    <polygon class="st0" points="85.03,85.03 994.97,85.03 733.99,346.01 346.01,346.01 		" />
                    <polygon class="st0" points="85.03,994.97 994.97,994.97 733.99,733.99 346.01,733.99 		" />
                    <rect x="346.01" y="346.01" class="st0" width="387.98" height="387.98" />
                  </g>
                </switch>
              </svg>
            <?php endif ?>
          </td>
        <?php endforeach; ?>
      </tr>
      <tr>
        <?php foreach ($terceraFila as $numeroDiente): ?>
          <td>
            <?php if ($numeroDiente !== null): ?>
              <div class="ds-text-center ds-font-bold ds-mb-1"><?= $numeroDiente ?></div>
              <svg version="1.1" id="Capa_<? $numeroDiente ?>" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;"
                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                x="0px" y="0px"
                width="<?= $width ?>"
                height="<?= $height ?>"
                viewBox="0 0 1080 1080"
                style="enable-background:new 0 0 1080 1080;" xml:space="preserve">
                <style type="text/css">
                  .st0 {
                    fill: none;
                    stroke: #1D1D1B;
                    stroke-miterlimit: 10;
                    vector-effect: non-scaling-stroke;
                  }
                </style>
                <switch>
                  <g i:extraneous="self">
                    <polygon class="st0" points="85.03,994.97 85.03,85.03 346.01,346.01 346.01,733.99 		" />
                    <polygon class="st0" points="994.97,994.97 994.97,85.03 733.99,346.01 733.99,733.99 		" />
                    <polygon class="st0" points="85.03,85.03 994.97,85.03 733.99,346.01 346.01,346.01 		" />
                    <polygon class="st0" points="85.03,994.97 994.97,994.97 733.99,733.99 346.01,733.99 		" />
                    <rect x="346.01" y="346.01" class="st0" width="387.98" height="387.98" />
                  </g>
                </switch>
              </svg>
            <?php endif ?>
          </td>
        <?php endforeach; ?>
      </tr>
      <tr>
        <?php foreach ($dientesFilaInferior as $numeroDiente): ?>
          <td>
            <div class="ds-text-center ds-font-bold ds-mb-1"><?= $numeroDiente ?></div>
            <svg version="1.1" id="Capa_<? $numeroDiente ?>" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;"
              xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
              x="0px" y="0px"
              width="<?= $width ?>"
              height="<?= $height ?>"
              viewBox="0 0 1080 1080"
              style="enable-background:new 0 0 1080 1080;" xml:space="preserve">
              <style type="text/css">
                .st0 {
                  fill: none;
                  stroke: #1D1D1B;
                  stroke-miterlimit: 10;
                  vector-effect: non-scaling-stroke;
                }
              </style>
              <switch>
                <g i:extraneous="self">
                  <polygon class="st0" points="85.03,994.97 85.03,85.03 346.01,346.01 346.01,733.99 		" />
                  <polygon class="st0" points="994.97,994.97 994.97,85.03 733.99,346.01 733.99,733.99 		" />
                  <polygon class="st0" points="85.03,85.03 994.97,85.03 733.99,346.01 346.01,346.01 		" />
                  <polygon class="st0" points="85.03,994.97 994.97,994.97 733.99,733.99 346.01,733.99 		" />
                  <rect x="346.01" y="346.01" class="st0" width="387.98" height="387.98" />
                </g>
              </switch>
            </svg>
          </td>
        <?php endforeach; ?>
      </tr>
    </table>
  </div>
</div>
