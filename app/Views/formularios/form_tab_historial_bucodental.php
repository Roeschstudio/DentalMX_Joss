<form id="form-historial-bucodental" method="POST" class="ds-row ds-gap-3">
    <h5 class="ds-mb-3">HISTORIA BUCODENTAL</h5>
    <input type="hidden" name="txtOperacion4" id="txtOperacion4" value="<?= $operacion4 ?>">
    <input type="hidden" name="id" id="id" value="<?= isset($datos4) ? $datos4['id'] : '' ?>">
    <input type="hidden" name="id_paciente" id="id_paciente" value="<?= $isNew ? 0 : esc($paciente['id']) ?>">
    <div class="ds-row">
        <div class="ds-col-auto">
            <label class="ds-label">¿Es su primera vez en el dentista?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="primera_vez_dentista" id="primera_vez_dentista"
            value="<?= isset($datos4) ? $datos4['primera_vez_dentista'] : '' ?>">
        </div>
        <div class="ds-col-auto">
            <label class="ds-label">¿Hace cuanto fué la última vez?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="cuanto_ultima_vez" id="cuanto_ultima_vez"
            value="<?= isset($datos4) ? $datos4['cuanto_ultima_vez'] : '' ?>">
        </div>
    </div>
    <br />
    <div class="ds-row">
        <div class="ds-col-auto">
            <label class="ds-label">¿Que tratamiento le realizaron?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="tratamiento_realizaron" id="tratamiento_realizaron"
            value="<?= isset($datos4) ? $datos4['tratamiento_realizaron'] : '' ?>">
        </div>
        <div class="ds-col-auto">
            <label class="ds-label">¿Le tomaron radiografía?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="tomaron_radiografia" id="tomaron_radiografia"
            value="<?= isset($datos4) ? $datos4['tomaron_radiografia'] : '' ?>">
        </div>
    </div>
    <br />
    <div class="ds-row">
        <div class="ds-col-auto">
            <label class="ds-label">¿Tiene movilidad en sus dientes?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="movilidad_dientes" id="movilidad_dientes"
            value="<?= isset($datos4) ? $datos4['movilidad_dientes'] : '' ?>">
        </div>
        <div class="ds-col-auto">
            <label class="ds-label">¿Le sangran las encías?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="sangran_encias" id="sangran_encias"
            value="<?= isset($datos4) ? $datos4['sangran_encias'] : '' ?>">
        </div>
    </div>
    <br />
    <div class="ds-row">
        <div class="ds-col-auto">
            <label class="ds-label">¿Con que frecuencia?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="frecuencia_sangran_encias" id="frecuencia_sangran_encias"
            value="<?= isset($datos4) ? $datos4['frecuencia_sangran_encias'] : '' ?>">
        </div>
        <div class="ds-col-auto">
            <label class="ds-label">¿Tiene mal sabor de boca?¿Mal olor?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="mal_sabor_boca" id="mal_sabor_boca">
            value="<?= isset($datos4) ? $datos4['mal_sabor_boca'] : '' ?>">
        </div>
    </div>
    <br />
    <div class="ds-row">
        <div class="ds-col-auto">
            <label class="ds-label">¿Resequedad en la boca?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="resequedad_boca" id="resequedad_boca"
            value="<?= isset($datos4) ? $datos4['resequedad_boca'] : '' ?>">
        </div>
        <div class="ds-col-auto">
            <label class="ds-label">¿Ha tenido infección en los dientes?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="tenido_infeccion_dientes" id="tenido_infeccion_dientes">
            value="<?= isset($datos4) ? $datos4['tenido_infeccion_dientes'] : '' ?>">
        </div>
    </div>
    <br />
    <div class="ds-row">
        <div class="ds-col-auto">
            <label class="ds-label">¿Hace cuanto tiempo?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="cuanto_tiempo_infeccion_dientes" id="cuanto_tiempo_infeccion_dientes"
            value="<?= isset($datos4) ? $datos4['cuanto_tiempo_infeccion_dientes'] : '' ?>">
        </div>
        <div class="ds-col-auto">
            <label class="ds-label">¿Le rechinan los dientes?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="rechinan_dientes" id="rechinan_dientes"
            value="<?= isset($datos4) ? $datos4['rechinan_dientes'] : '' ?>">
        </div>
    </div>
    <br />
    <div class="ds-row">
        <div class="ds-col-auto">
            <label class="ds-label">¿Dolor de cabeza?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="dolor_cabeza" id="dolor_cabeza"
            value="<?= isset($datos4) ? $datos4['dolor_cabeza'] : '' ?>">
        </div>
        <div class="ds-col-auto">
            <label class="ds-label">¿Con que frecuencia?¿Desde cuándo?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="frecuencia_dolor_cabeza" id="frecuencia_dolor_cabeza"
            value="<?= isset($datos4) ? $datos4['frecuencia_dolor_cabeza'] : '' ?>">
        </div>
    </div>
    <br />
    <div class="ds-row">
        <div class="ds-col-auto">
            <label class="ds-label">¿Cuantas veces se cepilla al día?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="veces_cepilla_al_dia" id="veces_cepilla_al_dia"
            value="<?= isset($datos4) ? $datos4['veces_cepilla_al_dia'] : '' ?>">
        </div>
        <div class="ds-col-auto">
            <label class="ds-label">¿Cada cuándo cambia su cepillo?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="cuando_cambia_cepillo" id="cuando_cambia_cepillo"
            value="<?= isset($datos4) ? $datos4['cuando_cambia_cepillo'] : '' ?>">
        </div>
    </div>
    <br />
    <div class="ds-row">
        <div class="ds-col-auto">
            <label class="ds-label">¿Usa hilo dental?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="usa_hilo_dental" id="usa_hilo_dental"
            value="<?= isset($datos4) ? $datos4['usa_hilo_dental'] : '' ?>">
        </div>
        <div class="ds-col-auto">
            <label class="ds-label">¿Enjuages bucales?</label>
        </div>
        <div class="ds-col-auto">
            <input type="text" class="ds-input" name="enjuages_bucales" id="enjuages_bucales"
            value="<?= isset($datos4) ? $datos4['enjuages_bucales'] : '' ?>">
        </div>
    </div>
    <br />
    <div class="ds-row">
        <div class="ds-col-12">
            <label class="ds-label" for="txt_motivo_consulta">Explique el motivo de la consulta</label>
            <input type="text" class="ds-input" name="motivo_consulta" id="motivo_consulta"
            value="<?= isset($datos4) ? $datos4['motivo_consulta'] : '' ?>">
        </div>
    </div>
    <button type="submit" class="ds-btn ds-btn--primary ds-mt-4">💾 Guardar</button>
    <br />
</form>
