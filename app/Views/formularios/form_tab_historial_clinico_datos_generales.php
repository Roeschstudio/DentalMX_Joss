<!-- Aquí va el contenido de Datos Generales -->
<form id="formHistorialClinico" class="ds-row ds-gap-3">
    <h5 class="ds-mb-3">DATOS GENERALES</h5>

    <input type="hidden" name="txtOperacion1" id="txtOperacion1" value="<?= $operacion1 ?>">
    <input type="hidden" name="id" id="id" value="<?= isset($datos1) ? $datos1['id'] : '' ?>">
    <input type="hidden" name="id_paciente" id="id_paciente" value="<?= $isNew ? 0 : esc($paciente['id']) ?>">

    <!-- Edad, sexo, peso, tipo de sangre -->
    <div class="ds-col-md-2">
        <label class="ds-label">Edad</label>
        <input type="number" class="ds-input" name="edad" value="<?= isset($datos1) ? $datos1['edad'] : '' ?>">
    </div>
    <div class="ds-col-md-2">
        <label class="ds-label">Sexo</label>
        <select class="ds-input ds-select" name="sexo">
            <option value="">Selecciona</option>
            <option value="Masculino" <?= (isset($datos1['sexo']) && $datos1['sexo'] === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
            <option value="Femenino" <?= (isset($datos1['sexo']) && $datos1['sexo'] === 'Femenino') ? 'selected' : '' ?>>Femenino</option>
        </select>
    </div>
    <div class="ds-col-md-2">
        <label class="ds-label">Peso</label>
        <input type="text" class="ds-input" name="peso" value="<?= isset($datos1) ? $datos1['peso'] : '' ?>">
    </div>
    <div class="ds-col-md-3">
        <label class="ds-label">Tipo de sangre</label>
        <input type="text" class="ds-input" name="tipo_sangre" value="<?= isset($datos1) ? $datos1['tipo_sangre'] : '' ?>">
    </div>

    <!-- Fecha de nacimiento, estado civil, ocupación -->
    <div class="ds-col-md-3">
        <label class="ds-label">Fecha de nacimiento</label>
        <input type="date" class="ds-input" name="fecha_nacimiento" value="<?= isset($datos1) ? $datos1['fecha_nacimiento'] : '' ?>">
    </div>
    <div class="ds-col-md-3">
        <label class="ds-label">Estado civil</label>
        <input type="text" class="ds-input" name="estado_civil" value="<?= isset($datos1) ? $datos1['estado_civil'] : '' ?>">
    </div>
    <div class="ds-col-md-3">
        <label class="ds-label">Ocupación</label>
        <input type="text" class="ds-input" name="ocupacion" value="<?= isset($datos1) ? $datos1['ocupacion'] : '' ?>">
    </div>

    <!-- Lugar de trabajo y email -->
    <div class="ds-col-md-4">
        <label class="ds-label">Lugar de trabajo</label>
        <input type="text" class="ds-input" name="lugar_trabajo" value="<?= isset($datos1) ? $datos1['lugar_trabajo'] : '' ?>">
    </div>


    <!-- Seguro médico -->
    <div class="ds-col-12">
        <label class="ds-label">¿Cuenta con seguro privado?</label><br>
        <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
            <input type="radio" name="seguro_privado" value="Si" id="seguro_si" 
            <?= ($datos1['cuenta_seguro'] ?? null) == 1 ? 'checked' : '' ?>>
            <label for="seguro_si">Sí</label>
        </div>
        <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
            <input type="radio" name="seguro_privado" value="No" id="seguro_no" 
            <?= ($datos1['cuenta_seguro'] ?? null) == 0 ? 'checked' : '' ?>>
            <label for="seguro_no">No</label>
        </div>

        <?php $seguro = $datos1['seguro'] ?? ''; ?>
        <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
            <input type="radio" name="seguro" value="IMSS" id="seguro_imss"
                <?= $seguro === 'IMSS' ? 'checked' : '' ?>>
            <label for="seguro_imss">IMSS</label>
        </div>

        <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
            <input type="radio" name="seguro" value="ISSSTE" id="seguro_issste"
                <?= $seguro === 'ISSSTE' ? 'checked' : '' ?>>
            <label for="seguro_issste">ISSSTE</label>
        </div>

        <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
            <input type="radio" name="seguro" value="POPULAR" id="seguro_popular"
                <?= $seguro === 'POPULAR' ? 'checked' : '' ?>>
            <label for="seguro_popular">POPULAR</label>
        </div>
    </div>

    <!-- Emergencia -->
    <div class="ds-col-12">
        <label class="ds-label">¿A quién acudir en caso de emergencia?</label>
        <input type="text" class="ds-input" name="contacto_emergencia" value="<?= isset($datos1) ? $datos1['nombre_contacto_emergencia'] : '' ?>">
    </div>

    <!-- Embarazo -->
     <?php $embarazo = $datos1['embarazo'] ?? ''; ?>
    <div class="ds-col-md-4">
        <label class="ds-label">¿Embarazo?</label><br>
        <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
            <input type="radio" name="embarazo" value="Si" id="embarazo_si"
            <?= $embarazo === '1' ? 'checked' : '' ?>>
            <label for="embarazo_si">Sí</label>
        </div>
        <div class="ds-flex ds-items-center ds-gap-2 ds-inline">
            <input type="radio" name="embarazo" value="No" id="embarazo_no"
             <?= $embarazo === '0' ? 'checked' : '' ?>>
            <label for="embarazo_no">No</label>
        </div>
    </div>

    <div class="ds-col-md-4">
        <label class="ds-label">¿Cuántos meses tiene?</label>
        <input type="number" class="ds-input" name="meses_embarazo" value="<?= isset($datos1) ? $datos1['meses_embarazo'] : '' ?>">
    </div>

    <!-- Ginecólogo -->
    <div class="ds-col-md-6">
        <label class="ds-label">Nombre del ginecólogo(a)</label>
        <input type="text" class="ds-input" name="ginecologo" value="<?= isset($datos1) ? $datos1['ginecologo'] : '' ?>">
    </div>
    <div class="ds-col-md-3">
        <label class="ds-label">Tel. del ginecólogo(a)</label>
        <input type="text" class="ds-input" name="telefono_ginecologo" value="<?= isset($datos1) ? $datos1['telefono_ginecologo'] : '' ?>">
    </div>
    <div class="ds-col-md-3">
        <label class="ds-label">Lugar donde lleva el control</label>
        <input type="text" class="ds-input" name="lugar_control" value="<?= isset($datos1) ? $datos1['lugar_control'] : '' ?>">
    </div>

    <div class="ds-col-12">
        <button type="submit" class="ds-btn ds-btn--primary">💾 Guardar</button>
    </div>
</form>
