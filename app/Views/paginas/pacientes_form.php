<?= $this->extend('layout/ds_layout') ?>
<?= $this->section('content') ?>

<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">Pacientes</h1>
    </div>
    
    <div class="ds-grid ds-grid--2">
        <div class="ds-card">
            <div class="ds-card__header">
                <h2 class="ds-card__title">Registrar Paciente</h2>
            </div>
            <div class="ds-card__body">
                <form id="formPaciente" class="ds-form">
                    <!-- Campo oculto para ID -->
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="txtOperacion" id="txtOperacion" value="OPERACION_NUEVO">
                    
                    <div class="ds-grid ds-grid--2">
                        <div class="ds-form__group">
                            <label for="nombre" class="ds-form__label">Nombre</label>
                            <input type="text" class="ds-form__input" id="nombre" name="nombre" required>
                            <div class="ds-form__error">Por favor ingresa el nombre.</div>
                        </div>

                        <div class="ds-form__group">
                            <label for="primer_apellido" class="ds-form__label">Primer apellido</label>
                            <input type="text" class="ds-form__input" id="primer_apellido" name="primer_apellido" required>
                            <div class="ds-form__error">Por favor ingresa el primer apellido.</div>
                        </div>

                        <div class="ds-form__group">
                            <label for="segundo_apellido" class="ds-form__label">Segundo apellido</label>
                            <input type="text" class="ds-form__input" id="segundo_apellido" name="segundo_apellido">
                        </div>

                        <div class="ds-form__group">
                            <label for="email" class="ds-form__label">Correo electrónico</label>
                            <input type="email" class="ds-form__input" id="email" name="email">
                            <div class="ds-form__error">Por favor ingresa un correo válido.</div>
                        </div>

                        <div class="ds-form__group">
                            <label for="celular" class="ds-form__label">WhatsApp</label>
                            <input type="tel" class="ds-form__input" id="celular" name="celular" required pattern="^\+?[0-9\s\-]{7,15}$">
                            <div class="ds-form__error">Por favor ingresa un número de WhatsApp válido.</div>
                        </div>

                        <div class="ds-form__group">
                            <label for="nacionalidad" class="ds-form__label">Nacionalidad</label>
                            <input type="text" class="ds-form__input" id="nacionalidad" name="nacionalidad">
                        </div>

                        <div class="ds-form__group ds-form__group--full">
                            <label for="domicilio" class="ds-form__label">Dirección</label>
                            <textarea class="ds-form__textarea" id="domicilio" name="domicilio" rows="2" required></textarea>
                            <div class="ds-form__error">Por favor ingresa la dirección.</div>
                        </div>
                    </div>
                    
                    <div class="ds-form__actions">
                        <button type="submit" class="ds-btn ds-btn--primary">
                            💾 Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="ds-card">
            <div class="ds-card__header">
                <h2 class="ds-card__title">Lista de Pacientes</h2>
            </div>
            <div class="ds-card__body">
                <div class="ds-table-container">
                    <table id="tblPacientes" class="ds-table ds-table--striped">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>nombre</th>
                                <th>Paterno</th>
                                <th>Materno</th>
                                <th>Domicilio</th>
                                <th>WhatsApp</th>
                                <th>Correo</th>
                                <th>Clínica</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let tblPacientes;
    $(document).ready(function() {
        setUpTablaPactblPacientesientes();

        $('#formPaciente #txtOperacion').val(OPERACION_NUEVO);
        
        // Validación inicializada en validations.js

        $("#formPaciente").submit(function(event) {
            event.preventDefault();
            
            // Usar el nuevo sistema de validaciones
            if (validatePacientesForm()) {
                guardarPaciente();
            } else {
                // Mostrar mensaje de error general
                Swal.fire({
                    icon: 'error',
                    title: 'Errores de Validación',
                    text: 'Por favor, corrige los errores marcados en el formulario.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }); /*FIN DEL DOCUMENT READY */

    function setUpTablaPactblPacientesientes() {
        alertaCargando();
        tblPacientes = $('#tblPacientes').DataTable({
            "ajax": URL_PACIENTES_GET_ALL,
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
                    "data": "nombre"
                },
                {
                    "data": "primer_apellido"
                },
                {
                    "data": "segundo_apellido"
                },
                {
                    "data": "domicilio"
                },
                {
                    "data": "celular"
                },
                {
                    "data": "email"
                },
                {
                    "data": null,
                    "defaultContent": ""
                },
                {
                    "data": null,
                    "defaultContent": ""
                }
            ],
            columnDefs: [{
                targets: 7,
                render: function(data, type, row, rowIdx) {
                    const urlHistorial = 'historial/paciente/' + row.id;
                    const urlOdontograma = 'odontograma/' + row.id;
                    return `
                        <div class="ds-btn-group">
                            <a href="${urlHistorial}" 
                               data-toggle="tooltip" 
                               title="Ver Historial Clínico" 
                               class="ds-btn ds-btn--success ds-btn--sm">
                                📋 Historial
                            </a>
                            <a href="${urlOdontograma}" 
                               data-toggle="tooltip" 
                               title="Ver Odontograma" 
                               class="ds-btn ds-btn--primary ds-btn--sm">
                                🦷 Odontograma
                            </a>
                        </div>
                    `;
                }
            }, {
                targets: 8,
                render: function(data, type, row, rowIdx) {
                    const btnEditar = '<a href="javascript:void(0)" onclick="setDataForEditPaciente(' + rowIdx.row + ')" data-toggle="tooltip" title="Editar" class="ds-btn ds-btn--success ds-btn--sm">✏️</a>';
                    const btnEliminar = '<a href="javascript:void(0)" onclick="setearBorrarPaciente(' + rowIdx.row + ')" data-toggle="tooltip" title="Eliminar" class="ds-btn ds-btn--danger ds-btn--sm">🗑️</a>';
                    return btnEditar + btnEliminar;
                }
            }]
        });
    }

    function guardarPaciente() {
        let datos = serializarFormularioAJson('formPaciente');

        // Mostrar notificación de carga
        dentalNotifications.cargando('Guardando paciente...');

        enviarAjax(
            'formPaciente',
            datos,
            'POST',
            URL_PACIENTES_SAVE_NEW_OR_EXISTING,
            (respuesta) => {
                console.log('Respuesta exitosa lambda:', respuesta);
                
                // Usar sistema de notificaciones
                dentalNotifications.paciente.guardado(datos.nombre + ' ' + datos.primer_apellido);
                
                $('#txtOperacion').val(OPERACION_NUEVO);
                resetDataTablePacientes();
            }
        );
    }

    function setDataForEditPaciente(rowId) {
        let rowData = tblPacientes.row(rowId).data();
        //id,txtOperacion,nombre,primer_apellido,segundo_apellido,email,celular,domicilio,created_at,updated_at
        $('#id').val(rowData.id);
        $('#txtOperacion').val(OPERACION_EDITAR);
        $('#nombre').val(rowData.nombre);
        $('#primer_apellido').val(rowData.primer_apellido);
        $('#segundo_apellido').val(rowData.segundo_apellido);
        $('#email').val(rowData.email);
        $('#celular').val(rowData.celular);
        $('#domicilio').val(rowData.domicilio);
    }

    function setearBorrarPaciente(rowId) {
        let rowData = tblPacientes.row(rowId).data();
        const id = rowData['id'];
        const nombre_usuario = rowData['nombre'] + ' ' + rowData['primer_apellido'] + ' ' + (rowData['segundo_apellido'] || '');

        // Usar sistema de confirmaciones
        dentalConfirmations.confirmarEliminarPaciente(nombre_usuario).then((result) => {
            if (result.isConfirmed) {
                callDeletePaciente(id, nombre_usuario);
            }
        });
    }

    function callDeletePaciente(id_usuario, nombre_usuario) {
        const datosCliente = {
            "id": id_usuario
        }

        let settings = {
            "url": URL_PACIENTES_BORRAR,
            "method": "DELETE",
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify(datosCliente),
            beforeSend: function() {
                dentalNotifications.cargando('Eliminando paciente...');
            }
        };

        $.ajax(settings)
            .done(function(response) {
                dentalNotifications.paciente.eliminado(nombre_usuario);
                resetDataTablePacientes();
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                try {
                    let errorResponse = JSON.parse(jqXHR.responseText);
                    
                    // Manejar error de clave foránea
                    if (errorResponse.message && errorResponse.message.includes('registros asociados')) {
                        dentalNotifications.error(
                            'No se puede eliminar el paciente porque tiene registros asociados (citas, recetas, etc.)',
                            { timer: 6000 }
                        );
                    } else {
                        dentalNotifications.error(errorResponse.message || 'Error al eliminar paciente');
                    }

                } catch (e) {
                    dentalNotifications.errorRed();
                }
            })
            .always(function() {
                $('#modal-cargando').modal('hide');
            });
    }

    function resetDataTablePacientes() {
        if (tblPacientes) {
            tblPacientes.ajax.reload(); // Reload data
        }
    }

</script>

<?= $this->endSection() ?>