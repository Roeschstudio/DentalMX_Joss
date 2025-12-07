<form id="form-antecedentes-patologicos" class="ds-row ds-gap-3">
    <h5 class="ds-mb-3">ANTECEDENTES PATOLÓGICOS</h5>
    <input type="hidden" name="txtOperacion3" id="txtOperacion3" value="<?= $operacion3 ?>">
        <input type="hidden" name="id" id="id" value="<?= isset($datos3) ? $datos3['id'] : '' ?>">
        <input type="hidden" name="id_paciente" id="id_paciente" value="<?= $isNew ? 0 : esc($paciente['id']) ?>">
    <div class="ds-row">
        <div class="ds-col-md-3">
            <label class="ds-label">¿Esta bajo tratamiento médico?</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="tratamiento" value="Si" id="tratamiento_si" <?= ($datos3['tratamiento'] ?? null) == 1 ? 'checked' : '' ?>>
                <label for="tratamiento_si">Sí</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="tratamiento" value="No" id="tratamiento_no" <?= ($datos3['tratamiento'] ?? null) == 0 ? 'checked' : '' ?> >
                <label for="tratamiento_no">No</label>
            </div>
        </div>
        <div class="ds-col-md-4">
            <label class="ds-label">¿Qué tipo de tratamiento?</label>
            <input type="text" class="ds-input" name="tipo_tratamiento"  value="<?= isset($datos3) ? $datos3['tipo_tratamiento'] : '' ?>">
        </div>
    </div>
    <!-- SUSTANCIA O MEDICAMENTO -->
    <div class="ds-row">
        <div class="ds-col-md-3">
            <label class="ds-label">¿Toma alguna sustancia o medicamento?</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="sustancia" value="Si" id="sustancia_si"
                <?= ($datos3['sustancia'] ?? null) == 1 ? 'checked' : '' ?>>
                <label for="sustancia_si">Sí</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="sustancia" value="No" id="sustancia_no"
                <?= ($datos3['sustancia'] ?? null) == 0 ? 'checked' : '' ?>>
                <label for="sustancia_no">No</label>
            </div>
        </div>
        <div class="ds-col-md-4">
            <label class="ds-label">¿Cuál?</label>
            <input type="text" class="ds-input" name="tipo_sustancia" value="<?= isset($datos3) ? $datos3['tipo_sustancia'] : '' ?>">
        </div>
    </div>

    <!-- Ha sido hospotalizado -->
    <div class="ds-row">
        <div class="ds-col-md-3">
            <label class="ds-label">¿Ha sido hospitalizado?</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="hospitalizado" value="Si" id="hospitalizado_si"
                <?= ($datos3['hospitalizado'] ?? null) == 1 ? 'checked' : '' ?>>
                <label for="hospitalizado_si">Sí</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="hospitalizado" value="No" id="hospitalizado_no"
                <?= ($datos3['hospitalizado'] ?? null) == 0? 'checked' : '' ?>>
                <label for="hospitalizado_no">No</label>
            </div>
        </div>
        <div class="ds-col-md-4">
            <label class="ds-label">Motivo</label>
            <input type="text" class="ds-input" name="motivo_hospitalizado"
            value="<?= isset($datos3) ? $datos3['motivo_hospitalizado'] : '' ?>">
        </div>
    </div>

    <!-- Es alérgico a alguna sustancia o medicamento-->
    <div class="ds-row">
        <div class="ds-col-md-3">
            <label class="ds-label">¿Es alérgico a alguna sustancia o medicamento?</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="alergico" value="Si" id="alergico_si"
                <?= ($datos3['alergico'] ?? null) == 1? 'checked' : '' ?>>
                <label for="alergico_si">Sí</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="alergico" value="No" id="alergico_no"
                <?= ($datos3['alergico'] ?? null) == 0? 'checked' : '' ?>>
                <label for="alergico_no">No</label>
            </div>
        </div>
        <div class="ds-col-md-4">
            <label class="ds-label">¿Cuál?</label>
            <input type="text" class="ds-input" name="sustancia_alergia"
            value="<?= isset($datos3) ? $datos3['sustancia_alergia'] : '' ?>">
        </div>
    </div>
    <!-- Has sido anestesiado alguna vez -->
    <div class="ds-row">
        <div class="ds-col-md-3">
            <label class="ds-label">¿Has sido anestesiado alguna vez?</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="anestesiado" value="Si" id="anestesiado_si"
                <?= ($datos3['anestesiado'] ?? null) == 1? 'checked' : '' ?>>
                <label for="anestesiado_si">Sí</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="anestesiado" value="No" id="anestesiado_no"
                <?= ($datos3['anestesiado'] ?? null) == 0? 'checked' : '' ?>>
                <label for="anestesiado_no">No</label>
            </div>
        </div>
        <div class="ds-col-md-4">
            <label class="ds-label">¿Ha tenido alguna reacción?</label>
            <input type="text" class="ds-input" name="anestesia_reaccion" id="anestesia_reaccion"
            value="<?= isset($datos3) ? $datos3['anestesia_reaccion'] : '' ?>">
        </div>
    </div>
    <!--  Marque si padece algún tipo de enfermedad-->
    <div class="ds-row">
        <div class="ds-col-12">
            <label class="ds-label">Marque si padece algún tipo de enfermedad</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_hipertension" id="chk_hipertension"
                <?= ($datos3['chk_hipertension'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_hipertension">Hipertensión Arterial</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_cardiopatia" id="chk_cardiopatia"
                <?= ($datos3['chk_cardiopatia'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_cardiopatia">Cardiopatías</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_hepatica" id="chk_hepatica"
                <?= ($datos3['chk_hepatica'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_hepatica">Hepáticas</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_pulmonar" id="chk_pulmonar"
                <?= ($datos3['chk_pulmonar'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_pulmonar">Pulmonares</label>
            </div>
        </div>
        <div class="ds-col-12">
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_digestivas" id="chk_digestivas"
                <?= ($datos3['chk_digestivas'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_digestivas">Digestivas</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_diabetes" id="chk_diabetes"
                <?= ($datos3['chk_diabetes'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_diabetes">Diabetes</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_asma" id="chk_asma"
                <?= ($datos3['chk_asma'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_asma">Asma</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_transtornos" id="chk_transtornos"
                <?= ($datos3['chk_transtornos'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_transtornos">Transtornos de coagulación</label>
            </div>
        </div>
        <div class="ds-col-12">
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_vih" id="chk_vih"
                <?= ($datos3['chk_vih'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_vih">VIH</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_epilepcia" id="chk_epilepcia"
                <?= ($datos3['chk_epilepcia'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_epilepcia">Epilepcia</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_respiratorias" id="chk_respiratorias"
                <?= ($datos3['chk_respiratorias'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_respiratorias">Respiratorias</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="checkbox" name="chk_nerviosa" id="chk_nerviosa"
                <?= ($datos3['chk_nerviosa'] ?? null) == 1? 'checked' : '' ?>>
                <label for="chk_nerviosa">Sistema Nervioso</label>
            </div>
        </div>
        <div class="ds-col-md-4">
            <label class="ds-label" for="txt_otra_enfermedad">Otra:</label>
            <input class="ds-input" type="text" name="txt_otra_enfermedad" id="txt_otra_enfermedad"
            value="<?= isset($datos3) ? $datos3['txt_otra_enfermedad'] : '' ?>">
        </div>
    </div>
    <!--  Padece tiroides -->
    <div class="ds-row">
        <div class="ds-col-md-3">
            <label class="ds-label">¿Padece de la tiroides?</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="tiroides" value="Si" id="tiroides_si"
                <?= ($datos3['tiroides'] ?? null) == 1? 'checked' : '' ?>>
                <label for="tiroides_si">Sí</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="tiroides" value="No" id="tiroides_no"
                <?= ($datos3['tiroides'] ?? null) == 0? 'checked' : '' ?>>
                <label for="tiroides_no">No</label>
            </div>
        </div>
    </div>
    <!--  Padece fiebre reumática -->
    <div class="ds-row">
        <div class="ds-col-md-3">
            <label class="ds-label">¿Padece fiebre reumática?</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="reumatica" value="Si" id="reumatica_si"
                <?= ($datos3['reumatica'] ?? null) == 1? 'checked' : '' ?>>
                <label for="reumatica_si">Sí</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="reumatica" value="No" id="reumatica_no"
                <?= ($datos3['reumatica'] ?? null) == 0? 'checked' : '' ?>>
                <label for="reumatica_no">No</label>
            </div>
        </div>
    </div>

    <!-- Consume bebidas alcoholicas -->
    <div class="ds-row">
        <div class="ds-col-md-3">
            <label class="ds-label">¿Consume bebidas alcohólicas?</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="alcoholicas" value="Si" id="alcoholicas_si"
                <?= ($datos3['alcoholicas'] ?? null) == 1? 'checked' : '' ?>>
                <label for="alcoholicas_si">Sí</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="alcoholicas" value="No" id="alcoholicas_no"
                <?= ($datos3['alcoholicas'] ?? null) == 0? 'checked' : '' ?>>
                <label for="alcoholicas_no">No</label>
            </div>
        </div>
        <div class="ds-col-md-4">
            <label class="ds-label">¿Con que frecuencia?</label>
            <input type="text" class="ds-input" name="alcoholicas_frecuencia" id="alcoholicas_frecuencia"
            value="<?= isset($datos3) ? $datos3['alcoholicas_frecuencia'] : '' ?>">
        </div>
    </div>

    <!-- Fuma cigarrillos -->
    <div class="ds-row">
        <div class="ds-col-md-3">
            <label class="ds-label">¿Fuma cigarrillos?</label><br>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="cigarrillos" value="Si" id="cigarrillos_si"
                <?= ($datos3['cigarrillos'] ?? null) == 1? 'checked' : '' ?>>
                <label for="cigarrillos_si">Sí</label>
            </div>
            <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
                <input type="radio" name="cigarrillos" value="No" id="cigarrillos_no"
                <?= ($datos3['cigarrillos'] ?? null) == 0? 'checked' : '' ?>>
                <label for="cigarrillos_no">No</label>
            </div>
        </div>
        <div class="ds-col-md-4">
            <label class="ds-label">¿Con que frecuencia?</label>
            <input type="text" class="ds-input" name="cigarrillos_frecuencia" id="cigarrillos_frecuencia"
            value="<?= isset($datos3) ? $datos3['cigarrillos_frecuencia'] : '' ?>">
        </div>
    </div>
    <button type="submit" class="ds-btn ds-btn--primary">💾 Guardar</button>
</form>
