<form id="formNotaEvolucion" class="ds-row ds-gap-3">
    <div class="ds-flex ds-justify-between ds-items-center ds-mb-3">
        <h5 class="ds-m-0">NOTAS DE EVOLUCIÓN</h5>
        <button type="button" class="ds-btn ds-btn--success ds-btn--sm" onclick="habilitarInputsNotaEvolucion()">
            ➕ Nuevo
        </button>
    </div>
    <input type="hidden" name="txtOperacion6" id="txtOperacion6" value="OPERACION_NUEVO">
    <input type="hidden" name="id_paciente" id="id_paciente" value="<?= $isNew ? 0 : esc($paciente['id']) ?>">
    <input type="hidden" name="id" id="id">
    <div class="ds-col-md-3">
        <label for="fecha" class="ds-label">Fecha</label>
        <input type="date" class="ds-input" id="fecha" name="fecha" required>
    </div>

    <div class="ds-col-md-9">
        <label for="tratamiento" class="ds-label">Tratamiento Realizado</label>
        <input type="text" class="ds-input" id="tratamiento" name="tratamiento_realizado" required>
    </div>

    <div class="ds-col-md-4">
        <label for="total" class="ds-label">Total</label>
        <input type="number" step="0.01" class="ds-input" id="total" name="total" value="0.0" required>
    </div>

    <div class="ds-col-md-4">
        <label for="abono" class="ds-label">Abono</label>
        <input type="number" step="0.01" class="ds-input" id="abono" name="abono">
    </div>

    <div class="ds-col-md-4">
        <label for="saldo" class="ds-label">Saldo</label>
        <input type="number" step="0.01" class="ds-input" id="saldo" name="saldo" value="0.0" required>
    </div>

    <div class="ds-col-md-3">
        <label for="firma" class="ds-label">Firma del paciente o responsable</label>
        <input type="text" class="ds-input" id="firma" name="firma">
    </div>

    <div class="ds-col-12">
        <button type="submit" class="ds-btn ds-btn--primary">💾 Guardar nota</button>
    </div>
</form>

<div class="ds-table-responsive">
    <table id="tblNotasEvolucion" class="ds-table ds-table--bordered ds-table--striped">
        <thead class="ds-table__head">
            <tr>
                <th>id</th>
                <th>Fecha</th>
                <th>Tratamiento Realizado</th>
                <th>Total</th>
                <th>Abono</th>
                <th>Saldo</th>
                <th>Firma</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>
