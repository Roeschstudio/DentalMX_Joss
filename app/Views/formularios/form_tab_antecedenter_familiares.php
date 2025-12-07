<form id="form-antecentes-familiares">
    <div class="mb-4">
        <h5 class="mb-3">ANTECEDENTES HEREDO FAMILIARES</h5>

        <input type="hidden" name="txtOperacion2" id="txtOperacion2" value="<?= $operacion2 ?>">
        <input type="hidden" name="id" id="id" value="<?= isset($datos2) ? $datos2['id'] : '' ?>">
        <input type="hidden" name="id_paciente" id="id_paciente" value="<?= $isNew ? 0 : esc($paciente['id']) ?>">

    <div class="ds-mb-3">
            <label class="ds-label">
                En su familia, ¿algún integrante padece algún tipo de enfermedad (Hipertensión, Diabetes, etc.)?
            </label>
            <input type="text" class="ds-input ds-mb-2" name="integrante_padece" id="integrante_padece" placeholder="Especificar si alguien padece"
                value="<?= isset($datos2) ? $datos2['integrante_padece'] : '' ?>">
            <label class="ds-label">¿Cuál?</label>
            <input type="text" class="ds-input" name="cual_enfermedad" id="cual_enfermedad" placeholder="Ej: Diabetes, cáncer, etc."
                value="<?= isset($datos2) ? $datos2['cual_enfermedad'] : '' ?>">
        </div>

        <div class="ds-table-responsive">
            <table class="ds-table ds-table--bordered ds-table--middle">
                <thead class="ds-table__head ds-text-center">
                    <tr>
                        <th>Familiar</th>
                        <th>Vivo</th>
                        <th>Muerto</th>
                        <th>¿De qué murió?</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Padre -->
                    <tr>
                        <td>Padre</td>
                        <td class="ds-text-center">
                            <input type="radio" name="padre_alive" value="vivo" class="form-check-input"
                                <?= ($datos2['padre_alive'] ?? null) == 1 ? 'checked' : '' ?>>
                        </td>
                        <td class="ds-text-center">
                            <input type="radio" name="padre_alive" value="muerto" class="form-check-input"
                                <?= ($datos2['padre_alive'] ?? null) == 0 ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <input type="text" name="razon_padre" class="ds-input"
                                value="<?= isset($datos2) ? $datos2['razon_padre'] : '' ?>">
                        </td>
                    </tr>
                    <!-- Madre -->
                    <tr>
                        <td>Madre</td>
                        <td class="text-center">
                            <input type="radio" name="madre_alive" id="madre_alive" value="vivo" class="form-check-input"
                               <?= ($datos2['madre_alive'] ?? null) == 1 ? 'checked' : '' ?>>
                        </td>
                        <td class="ds-text-center">
                            <input type="radio" name="madre_alive" value="muerto" class="form-check-input"
                               <?= ($datos2['madre_alive'] ?? null) == 0 ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <input type=" text" name="razon_madre" class="ds-input"
                                value="<?= isset($datos2) ? $datos2['razon_madre'] : '' ?>">
                        </td>
                    </tr>
                    <!-- Hermano -->
                    <tr>
                        <td>Hermano</td>
                        <td class="text-center">
                            <input type="radio" name="hermano_alive" value="vivo" class="form-check-input"
                                <?= ($datos2['hermano_alive'] ?? null) == 1 ? 'checked' : '' ?>>
                        </td>
                        <td class="ds-text-center">
                            <input type="radio" name="hermano_alive" value="muerto" class="form-check-input"
                                <?= ($datos2['hermano_alive'] ?? null) == 0 ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <input type=" text" name="razon_hermano" class="ds-input"
                                value="<?= isset($datos2) ? $datos2['razon_hermano'] : '' ?>">
                        </td>
                    </tr>
                    <!-- Hermana -->
                    <tr>
                        <td>Hermana</td>
                        <td class="text-center">
                            <input type="radio" name="hermana_alive" value="vivo" class="form-check-input"
                                <?= ($datos2['hermana_alive'] ?? null) == 1 ? 'checked' : '' ?>>
                        </td>
                        <td class="ds-text-center">
                            <input type="radio" name="hermana_alive" value="muerto" class="form-check-input"
                                <?= ($datos2['hermana_alive'] ?? null) == 0 ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <input type=" text" name="razon_hermana" class="ds-input"
                                value="<?= isset($datos2) ? $datos2['razon_hermana'] : '' ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <button type="submit" class="ds-btn ds-btn--primary">💾 Guardar</button>
</form>
