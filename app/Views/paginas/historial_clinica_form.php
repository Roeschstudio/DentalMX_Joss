<?= $this->extend('layout/ds_layout') ?>
<?= $this->section('content') ?>

<div class="ds-page">
    <div class="ds-page__header">
        <a href="<?= base_url('pacientes') ?>" class="ds-btn ds-btn--outline" title="Regresar">
            ← Regresar
        </a>
        <h1 class="ds-page__title">Historia Clínica Odontológica General</h1>
    </div>
    
    <div class="ds-card ds-card--primary">
        <div class="ds-card__header">
            <h2 class="ds-card__title">Datos del paciente</h2>
        </div>
        <div class="ds-card__body">
            <input type="hidden" name="id_paciente_general" id="id_paciente_general" value="<?= $isNew ? 0 : esc($paciente['id']) ?>">
            <div class="ds-grid ds-grid--3">
                <!-- Nombre y Nacionalidad -->
                <div class="ds-form__group">
                    <label class="ds-form__label">Nombre del paciente</label>
                    <input type="text" class="ds-form__input" name="nombre_compledo_general" value="<?= $isNew ? '' : esc($nombre_completo) ?>" readonly>
                </div>
                <div class="ds-form__group">
                    <label class="ds-form__label">Nacionalidad</label>
                    <input type="text" class="ds-form__input" name="nacionalidad_general" value="<?= $isNew ? '' : esc($paciente['nacionalidad']) ?>" readonly>
                </div>

                <!-- Domicilio y contacto -->
                <div class="ds-form__group">
                    <label class="ds-form__label">Domicilio</label>
                    <input type="text" class="ds-form__input" name="domicilio_general" value="<?= $isNew ? '' : esc($paciente['domicilio']) ?>" readonly>
                </div>

                <div class="ds-form__group">
                    <label class="ds-form__label">WhatsApp / Cel.</label>
                    <input type="text" class="ds-form__input" name="celular_general" value="<?= $isNew ? '' : esc($paciente['celular']) ?>" readonly>
                </div>

                <div class="ds-form__group">
                    <label class="ds-form__label">E-mail</label>
                    <input type="email" class="ds-form__input" name="email_general" value="<?= $isNew ? '' : esc($paciente['email']) ?>" readonly>
                </div>
            </div>
        </div>
    </div>
    
    <div class="ds-tabs">
        <!-- Nav Tabs -->
        <ul class="ds-tabs__nav" id="tabsHistorial" role="tablist">
            <li class="ds-tabs__item" role="presentation">
                <button class="ds-tabs__link ds-tabs__link--active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab">1. Datos Generales</button>
            </li>
            <li class="ds-tabs__item" role="presentation">
                <button class="ds-tabs__link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab">2. Antecedentes Familiares</button>
            </li>
            <li class="ds-tabs__item" role="presentation">
                <button class="ds-tabs__link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab">3. Antecedentes Patológicos</button>
            </li>
            <li class="ds-tabs__item" role="presentation">
                <button class="ds-tabs__link" id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4" type="button" role="tab">4. Historial Bucodental</button>
            </li>
            <li class="ds-tabs__item" role="presentation">
                <button class="ds-tabs__link" id="tab5-tab" data-bs-toggle="tab" data-bs-target="#tab5" type="button" role="tab">5. Odontograma</button>
            </li>
            <li class="ds-tabs__item" role="presentation">
                <button class="ds-tabs__link" id="tab6-tab" data-bs-toggle="tab" data-bs-target="#tab6" type="button" role="tab">6. Notas de evolución</button>
            </li>
            <li class="ds-tabs__item" role="presentation">
                <button class="ds-tabs__link" id="tab7-tab" data-bs-toggle="tab" data-bs-target="#tab7" type="button" role="tab">7. Nota médica evolución</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="ds-tabs__content" id="tabsHistorialContent">
            <div class="ds-tabs__pane ds-tabs__pane--active" id="tab1" role="tabpanel">
                <?= view('formularios/form_tab_historial_clinico_datos_generales', [
                    'paciente' => $paciente,
                    'isNew' => $isNew,
                    'operacion1' => $operacion1,
                    'datos1' => $datos1
                ]) ?>
            </div>
            <div class="ds-tabs__pane" id="tab2" role="tabpanel">
                <?= view('formularios/form_tab_antecedenter_familiares', [
                    'paciente' => $paciente,
                    'isNew' => $isNew,
                    'operacion2' => $operacion1,
                    'datos2' => $datos2
                ]) ?>
            </div>
            <div class="ds-tabs__pane" id="tab3" role="tabpanel">
                <?= view('formularios/form_tab_antecedenter_patologicos', [
                    'paciente' => $paciente,
                    'isNew' => $isNew,
                    'operacion3' => $operacion3,
                    'datos3' => $datos3
                ]) ?>
            </div>
            <div class="ds-tabs__pane" id="tab4" role="tabpanel">
                <?= view('formularios/form_tab_historial_bucodental', [
                    'paciente' => $paciente,
                    'isNew' => $isNew,
                    'operacion3' => $operacion4,
                    'datos3' => $datos4
                ]) ?>
            </div>
            <div class="ds-tabs__pane" id="tab5" role="tabpanel">
                <?= view('formularios/form_tab_odontograma_new', [
                    'paciente' => $paciente,
                    'isNew' => $isNew
                ]) ?>
            </div>
            <div class="ds-tabs__pane" id="tab6" role="tabpanel">
                <?= view('formularios/form_tab_notas_evolucion', [
                    'paciente' => $paciente,
                    'isNew' => $isNew,
                ]) ?>
            </div>
            <div class="ds-tabs__pane" id="tab7" role="tabpanel">
                <?= view('formularios/form_tab_notas_medicas') ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let tablaNotas;
    $(document).ready(function() {

        $("#formHistorialClinico").validate({
            rules: {
                edad: {
                    required: true,
                },
                sexo: {
                    required: true,
                },
                peso: {
                    required: true
                },
                fecha_nacimiento: {
                    required: true
                },
                estado_civil: {
                    required: true
                }

            },
            messages: {
                edad: {
                    required: "Requerido"
                },
                sexo: {
                    required: "Requerido"
                },
                peso: {
                    required: "Requerido."
                },
                fecha_nacimiento: {
                    required: "Requerido."
                },
                estado_civil: {
                    required: "Requerido."
                }
            }
        });

        $('#tablaNotas').DataTable();
        $('#tablaNotasMedicas').DataTable();

        $("#formHistorialClinico").submit(function(event) {
            event.preventDefault();
            if ($('#formHistorialClinico').valid()) {
                guardarDatos1();
            }
        });


        $("#form-antecentes-familiares").submit(function(event) {
            event.preventDefault();
            if ($('#form-antecentes-familiares').valid()) {
                guardarDatos2();
            }
        });

        configurarLimpiezaRazon('padre_alive', 'razon_padre');
        configurarLimpiezaRazon('madre_alive', 'razon_madre');
        configurarLimpiezaRazon('hermano_alive', 'razon_hermano');
        configurarLimpiezaRazon('hermana_alive', 'razon_hermana');

        $("#form-antecedentes-patologicos").submit(function(event) {
            event.preventDefault();
            guardarDatos3();
        });

        $("#form-historial-bucodental").submit(function(event) {
            event.preventDefault();
            guardarDatos4();
        });


        setUpTablaNotasEvolucion();
        $("#formNotaEvolucion").validate({
            rules: {
                fecha: {
                    required: true,
                },
                tratamiento: {
                    required: true,
                },
                peso: {
                    required: true
                },
                total: {
                    required: true
                },
                saldo: {
                    required: true
                }

            },
            messages: {
                fecha: {
                    required: "Requerido"
                },
                tratamiento: {
                    required: "Requerido"
                },
                peso: {
                    required: "Requerido."
                },
                total: {
                    required: "Requerido."
                },
                saldo: {
                    required: "Requerido."
                }
            }
        });

        $("#formNotaEvolucion").submit(function(event) {
            event.preventDefault();
            if ($('#formNotaEvolucion').valid()) {
                guardarNotasEvolucion();
            }
        });

    });

    function guardarDatos1() {
        let datos = serializarFormularioAJson('formHistorialClinico');

        let operacion = $('#txtOperacion1').val();

        if (operacion == OPERACION_NUEVO) {
            delete datos['id'];
        }
        const embarazo = datos['embarazo']?.toLowerCase() === 'si';
        const cuenta_seguro = datos['seguro_privado']?.toLowerCase() === 'si';
        datos['embarazo'] = embarazo;
        datos['cuenta_seguro'] = cuenta_seguro;
        if (!cuenta_seguro) {
            datos['seguro'] = null;
        }

        delete datos['txtOperacion1'];
        delete datos['seguro_privado'];
        console.log('json enviar', datos);
        enviarAjax(
            'formHistorialClinico',
            datos,
            'POST',
            URL_PACIENTES_SAVE_DATOS1,
            (respuesta) => {
                alertaExito(respuesta.message);
                $('#txtOperacion1').val(OPERACION_EDITAR);
                $('#formHistorialClinico').find('input[name="id"]').val(respuesta.id);
            }
        );
    }

    function guardarDatos2() {
        let datos = serializarFormularioAJson('form-antecentes-familiares');
        let datos_normalizados = normalizarCamposAlive(datos);

        let operacion = $('#txtOperacion2').val();

        if (operacion == OPERACION_NUEVO) {
            delete datos_normalizados['id'];
        }

        delete datos_normalizados['txtOperacion2'];
        console.log('json enviar', datos_normalizados);
        enviarAjax(
            'form-antecentes-familiares',
            datos_normalizados,
            'POST',
            URL_PACIENTES_SAVE_DATOS2,
            (respuesta) => {
                alertaExito(respuesta.message);
                $('#txtOperacion2').val(OPERACION_EDITAR);
                $('#form-antecentes-familiares').find('input[name="id"]').val(respuesta.id);
            }
        );
    }

    function guardarDatos3() {
        const datos = generarJSONAntecedentesPatologicos();

        let operacion = $('#txtOperacion3').val();
        let $form = $('#form-antecedentes-patologicos');
        let id_paciente = $form.find('input[name="id_paciente"]').val();
        let id = $form.find('input[name="id"]').val();

        datos.id_paciente = id_paciente;
        if (operacion === OPERACION_EDITAR) {
            datos.id = id;
        }

        enviarAjax(
            'form-antecedentes-patologicos',
            datos,
            'POST',
            URL_PACIENTES_SAVE_PATOLOGICOS,
            (respuesta) => {
                alertaExito(respuesta.message);
                $('#txtOperacion3').val(OPERACION_EDITAR);
                $('#form-antecedentes-patologicos').find('input[name="id"]').val(respuesta.id);
            }
        );
    }

    function guardarDatos4() {
        const datos = serializarFormularioAJson('form-historial-bucodental');
        let operacion = $('#txtOperacion4').val();
        let $form = $('#form-historial-bucodental');
        let id_paciente = $form.find('input[name="id_paciente"]').val();
        let id = $form.find('input[name="id"]').val();
        datos.id_paciente = id_paciente;
        if (operacion === OPERACION_EDITAR) {
            datos.id = id;
        }
        console.log(datos);

        enviarAjax(
            'form-historial-bucodental',
            datos,
            'POST',
            URL_PACIENTES_SAVE_BUCODENTAL,
            (respuesta) => {
                alertaExito(respuesta.message);
                $('#txtOperacion4').val(OPERACION_EDITAR);
                $('#form-historial-bucodental').find('input[name="id"]').val(respuesta.id);
            }
        );
    }

    function normalizarCamposAlive(data) {
        const nuevo = {
            ...data
        };

        Object.keys(nuevo).forEach(key => {
            if (key.endsWith('_alive')) {
                nuevo[key] = nuevo[key]?.toLowerCase() === 'vivo';
            }
        });

        return nuevo;
    }

    function configurarLimpiezaRazon(nombreRadio, nombreCampoTexto) {
        $(`input[name="${nombreRadio}"]`).on('change', function() {
            if ($(this).val().toLowerCase() === 'vivo') {
                $(`input[name="${nombreCampoTexto}"]`).val('').prop('disabled', true);
            } else {
                $(`input[name="${nombreCampoTexto}"]`).prop('disabled', false);
            }
        });
    }

    function generarJSONAntecedentesPatologicos() {
        const getRadioBool = name => $('input[name="' + name + '"]:checked').val() === 'Si';
        const getCheckboxBool = name => $('#' + name).is(':checked');
        const getInputVal = name => $('input[name="' + name + '"]').val().trim();

        const datos = {
            tratamiento: getRadioBool('tratamiento'),
            tipo_tratamiento: getInputVal('tipo_tratamiento'),
            sustancia: getRadioBool('sustancia'),
            tipo_sustancia: getInputVal('tipo_sustancia'),
            hospitalizado: getRadioBool('hospitalizado'),
            motivo_hospitalizado: getInputVal('motivo_hospitalizado'),
            alergico: getRadioBool('alergico'),
            sustancia_alergia: getInputVal('sustancia_alergia'),
            anestesiado: getRadioBool('anestesiado'),
            anestesia_reaccion: getInputVal('anestesia_reaccion'),
            chk_hipertension: getCheckboxBool('chk_hipertension'),
            chk_cardiopatia: getCheckboxBool('chk_cardiopatia'),
            chk_hepatica: getCheckboxBool('chk_hepatica'),
            chk_pulmonar: getCheckboxBool('chk_pulmonar'),
            chk_digestivas: getCheckboxBool('chk_digestivas'),
            chk_diabetes: getCheckboxBool('chk_diabetes'),
            chk_asma: getCheckboxBool('chk_asma'),
            chk_transtornos: getCheckboxBool('chk_transtornos'),
            chk_vih: getCheckboxBool('chk_vih'),
            chk_epilepcia: getCheckboxBool('chk_epilepcia'),
            chk_respiratorias: getCheckboxBool('chk_respiratorias'),
            chk_nerviosa: getCheckboxBool('chk_nerviosa'),
            txt_otra_enfermedad: getInputVal('txt_otra_enfermedad'),
            tiroides: getRadioBool('tiroides'),
            reumatica: getRadioBool('reumatica'),
            alcoholicas: getRadioBool('alcoholicas'),
            alcoholicas_frecuencia: getInputVal('alcoholicas_frecuencia'),
            cigarrillos: getRadioBool('cigarrillos'),
            cigarrillos_frecuencia: getInputVal('cigarrillos_frecuencia')
        };

        return datos;
    }

    function guardarNotasEvolucion() {
        const datos = serializarFormularioAJson('formNotaEvolucion');
        let operacion = $('#txtOperacion6').val();
        let $form = $('#formNotaEvolucion');
        let id_paciente = $form.find('input[name="id_paciente"]').val();
        let id = $form.find('input[name="id"]').val();
        datos.id_paciente = id_paciente;
        if (operacion === OPERACION_EDITAR) {
            datos.id = id;
        } else {
            delete datos['id'];
        }
        delete datos['txtOperacion6'];
        console.log(datos);
        enviarAjax(
            'formNotaEvolucion',
            datos,
            'POST',
            URL_PACIENTES_SAVE_NOTA_EVOLUCION,
            (respuesta) => {
                alertaExito(respuesta.message);
                $('#formNotaEvolucion').trigger('reset');
                $('#txtOperacion6').val(OPERACION_NUEVO);
                deshabilitarInputsNotaEvolucion();
                resetDataTableNotasEvolucion();
            }
        );
    }

    function setUpTablaNotasEvolucion() {
        let id_paciente = $('#formNotaEvolucion').find('input[name="id_paciente"]').val();
        let url = URL_PACIENTES_NOTAS_EVOLUCION_GET_ALL.replace('ID_PACIENTE', id_paciente);
        console.log(url);
        alertaCargando();
        tablaNotas = $('#tblNotasEvolucion').DataTable({
            "ajax": url,
            "pageLength": 200,
            "scrollX": true,
            "scrollY": "500px",
            "scrollCollapse": true,
            "initComplete": function(settings, json) {
                ocultarAlertaCargando();
            },
            "columns": [{
                    "data": "id"
                },
                {
                    "data": "fecha"
                },
                {
                    "data": "tratamiento_realizado"
                },
                {
                    "data": "total"
                },
                {
                    "data": "abono"
                },
                {
                    "data": "saldo"
                },
                {
                    "data": "firma"
                },
                {
                    "data": "id_paciente"
                }
            ],
            columnDefs: [{
                    targets: 0,
                    visible: false
                },
                {
                    targets: 7,
                    render: function(data, type, row, rowIdx) {
                        const btnEditar = '<a href="javascript:void(0)" onclick="setDataForEditNotaEvolucion(' + rowIdx.row + ')" data-toggle="tooltip" title="Editar" class="ds-btn ds-btn--success ds-btn--sm">✏️</a>';
                        const btnEliminar = '<a href="javascript:void(0)" onclick="setDataForDeleteNotaEvolucion(' + rowIdx.row + ')" data-toggle="tooltip" title="Eliminar" class="ds-btn ds-btn--danger ds-btn--sm">🗑️</a>';
                        return btnEditar + btnEliminar;
                    },
                    className: 'text-center'
                }
            ]
        });
    }

    function setDataForEditNotaEvolucion(rowId) {
        let rowData = tablaNotas.row(rowId).data();
        $('#formNotaEvolucion').find('input[name="id"]').val(rowData.id);
        $('#formNotaEvolucion').find('input[name="id_paciente"]').val(rowData.id_paciente);
        $('#formNotaEvolucion').find('input[name="fecha"]').val(rowData.fecha);
        $('#formNotaEvolucion').find('input[name="tratamiento_realizado"]').val(rowData.tratamiento_realizado);
        $('#formNotaEvolucion').find('input[name="total"]').val(rowData.total);
        $('#formNotaEvolucion').find('input[name="abono"]').val(rowData.abono);
        $('#formNotaEvolucion').find('input[name="saldo"]').val(rowData.saldo);
        $('#formNotaEvolucion').find('input[name="firma"]').val(rowData.firma);
        $('#txtOperacion6').val(OPERACION_EDITAR);
        $('#formNotaEvolucion input[type="text"], #formNotaEvolucion input[type="date"], #formNotaEvolucion input[type="number"]').prop('disabled', false);

    }

    function resetDataTableNotasEvolucion() {
        if (tablaNotas) {
            tablaNotas.ajax.reload(); // Reload data
        }
    }

    function deshabilitarInputsNotaEvolucion() {
    $('#formNotaEvolucion input[type="text"], #formNotaEvolucion input[type="date"], #formNotaEvolucion input[type="number"]').prop('disabled', true);
    }

    function habilitarInputsNotaEvolucion() {
        $('#txtOperacion6').val(OPERACION_NUEVO);
        $('#formNotaEvolucion').find('input[name="id"]').val("");
        $('#formNotaEvolucion input[type="text"], #formNotaEvolucion input[type="date"], #formNotaEvolucion input[type="number"]').prop('disabled', false);
    }
</script>

<?= $this->endSection() ?>