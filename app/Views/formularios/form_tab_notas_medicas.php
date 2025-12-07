<form id="form-notas-medicas" class="ds-row ds-gap-3">
    <h5 class="mb-3">NOTAS MÉDICAS DE EVOLUCIÓN</h5>
  <div class="ds-col-md-3">
    <label for="fecha" class="ds-label">Fecha</label>
    <input type="date" class="ds-input" id="fecha" name="fecha" required>
  </div>

  <div class="ds-col-md-4">
    <label for="tx" class="ds-label">Tx (Tratamiento)</label>
    <input type="text" class="ds-input" id="tx" name="tx" required>
  </div>

  <div class="ds-col-md-5">
    <label for="indicaciones" class="ds-label">Indicaciones</label>
    <input type="text" class="ds-input" id="indicaciones" name="indicaciones" required>
  </div>

  <div class="ds-col-12">
    <button type="submit" class="ds-btn ds-btn--primary">💾 Guardar nota médica</button>
  </div>
</form>
<!-- Tabla de notas médicas -->
<div class="ds-table-responsive ds-mt-4">
  <table id="tablaNotasMedicas" class="ds-table ds-table--striped ds-table--bordered">
    <thead class="ds-table__head">
      <tr>
        <th>Fecha</th>
        <th>Tx (Tratamiento)</th>
        <th>Indicaciones</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>2025-06-01</td><td>Profilaxis</td><td>Reforzar higiene oral. Cepillado 3 veces al día con pasta fluorada.</td></tr>
      <tr><td>2025-06-03</td><td>Endodoncia 1.6</td><td>Tomar analgésico cada 8h si hay dolor. Revisar en 7 días.</td></tr>
      <tr><td>2025-06-04</td><td>Extracción 4.8</td><td>Reposo 24h. No enjuagar. Hielo en zona. Dieta blanda.</td></tr>
      <tr><td>2025-06-05</td><td>Aplicación de flúor</td><td>Evitar alimentos y bebidas por 30 min post-aplicación.</td></tr>
      <tr><td>2025-06-06</td><td>Revisión ortodoncia</td><td>Se ajustan ligas. Revisar molestias en próxima cita.</td></tr>
      <tr><td>2025-06-07</td><td>Colocación de resina 2.3</td><td>Evitar morder alimentos duros con ese diente.</td></tr>
      <tr><td>2025-06-08</td><td>Control de placa bacteriana</td><td>Uso de enjuague con clorhexidina 0.12% por 7 días.</td></tr>
      <tr><td>2025-06-09</td><td>Tratamiento periodontal</td><td>Profilaxis + raspado. Indicar reevaluación en 2 semanas.</td></tr>
      <tr><td>2025-06-10</td><td>Revisión post-extracción</td><td>Cicatrización adecuada. No requiere intervención adicional.</td></tr>
      <tr><td>2025-06-11</td><td>Diagnóstico de bruxismo</td><td>Indicar férula de descarga. Evitar café y estrés.</td></tr>
      <tr><td>2025-06-12</td><td>Consulta general</td><td>Sin hallazgos relevantes. Mantener control semestral.</td></tr>
      <tr><td>2025-06-13</td><td>Aplicación de selladores</td><td>En piezas 3.6 y 4.6. Indicar buena higiene posterior.</td></tr>
      <tr><td>2025-06-14</td><td>Blanqueamiento dental</td><td>Evitar café, té y tabaco por 72h. Uso de gel por 7 días.</td></tr>
      <tr><td>2025-06-15</td><td>Consulta prequirúrgica</td><td>Evaluación general. Instrucciones para cirugía el 17/06.</td></tr>
      <tr><td>2025-06-16</td><td>Tratamiento caries incipiente</td><td>Fluorización tópica. Reevaluar en 3 meses.</td></tr>
      <tr><td>2025-06-17</td><td>Cirugía mucogingival</td><td>Reposo absoluto 48h. Indicaciones post-operatorias entregadas.</td></tr>
      <tr><td>2025-06-18</td><td>Revisión ortodoncia</td><td>Progreso estable. Ajuste en arco superior.</td></tr>
      <tr><td>2025-06-19</td><td>Urgencia por dolor</td><td>Endodoncia de urgencia iniciada en pieza 2.6.</td></tr>
      <tr><td>2025-06-20</td><td>Evaluación implantológica</td><td>Indicar CBCT. Explicar procedimiento y fases.</td></tr>
      <tr><td>2025-06-21</td><td>Colocación provisional</td><td>Ajuste oclusal correcto. Revisión en 15 días.</td></tr>
    </tbody>
  </table>
</div>
