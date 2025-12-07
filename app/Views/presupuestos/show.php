<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">📋 Detalles del Presupuesto</h1>
    </div>
    <div class="ds-container ds-container--fluid">
    <div class="ds-row">
        <div class="ds-col-12">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="card-title">Detalles del Presupuesto</h3>
                    <div class="ds-card__actions ds-btn-group">
                        <?php if ($presupuesto['estado'] == 'borrador'): ?>
                            <a href="<?= base_url('/presupuestos/edit/' . $presupuesto['id']) ?>" 
                               class="ds-btn ds-btn--warning ds-btn--sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="<?= base_url('/presupuestos/send/' . $presupuesto['id']) ?>" 
                               class="ds-btn ds-btn--success ds-btn--sm">
                                <i class="fas fa-paper-plane"></i> Enviar
                            </a>
                        <?php endif; ?>
                        <?php if ($presupuesto['estado'] == 'pendiente'): ?>
                            <a href="<?= base_url('/presupuestos/approve/' . $presupuesto['id']) ?>" 
                               class="ds-btn ds-btn--success ds-btn--sm"
                               onclick="return confirm('¿Está seguro de aprobar este presupuesto?')">
                                <i class="fas fa-check"></i> Aprobar
                            </a>
                            <a href="<?= base_url('/presupuestos/reject/' . $presupuesto['id']) ?>" 
                               class="ds-btn ds-btn--danger ds-btn--sm"
                               onclick="return confirm('¿Está seguro de rechazar este presupuesto?')">
                                <i class="fas fa-times"></i> Rechazar
                            </a>
                        <?php endif; ?>
                        <?php if ($presupuesto['estado'] == 'aprobado'): ?>
                            <a href="<?= base_url('/presupuestos/convert/' . $presupuesto['id']) ?>" 
                               class="ds-btn ds-btn--primary ds-btn--sm"
                               onclick="return confirm('¿Convertir este presupuesto a cotización?')">
                                <i class="fas fa-exchange-alt"></i> Convertir
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('/presupuestos/pdf/' . $presupuesto['id']) ?>" 
                           class="ds-btn ds-btn--secondary ds-btn--sm">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <a href="<?= base_url('/presupuestos') ?>" class="ds-btn ds-btn--light ds-btn--sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="ds-card__body">
                    <!-- Datos principales -->
                    <div class="ds-row ds-mb-4">
                        <div class="ds-col-md-6">
                            <h5 class="ds-text-base ds-font-semibold">Información del Presupuesto</h5>
                            <table class="ds-table ds-table--sm">
                                <tr>
                                    <td><strong>Folio:</strong></td>
                                    <td><?= $presupuesto['folio'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Emisión:</strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($presupuesto['fecha_emision'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Vigencia:</strong></td>
                                    <td><?= date('d/m/Y', strtotime($presupuesto['fecha_vigencia'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td><?= $this->include('presupuestos/_badge_estado', ['estado' => $presupuesto['estado']]) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="ds-col-md-6">
                            <h5 class="ds-text-base ds-font-semibold">Información del Paciente</h5>
                            <table class="ds-table ds-table--sm">
                                <tr>
                                    <td><strong>Paciente:</strong></td>
                                    <td><?= $presupuesto['paciente_nombre'] . ' ' . $presupuesto['paciente_apellido'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Médico:</strong></td>
                                    <td><?= $presupuesto['medico_nombre'] ?></td>
                                </tr>
                                <?php if (!empty($presupuesto['observaciones'])): ?>
                                <tr>
                                    <td><strong>Observaciones:</strong></td>
                                    <td><?= $presupuesto['observaciones'] ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Detalles del presupuesto -->
                    <h5 class="ds-text-base ds-font-semibold">Detalles del Presupuesto</h5>
                    <div class="ds-table-responsive">
                        <table class="ds-table ds-table--bordered">
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Descuento %</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($presupuesto['detalles'] as $detalle): ?>
                                <tr>
                                    <td><?= $detalle['servicio_nombre'] ?></td>
                                    <td><?= $detalle['descripcion'] ?></td>
                                    <td><?= $detalle['cantidad'] ?></td>
                                    <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                                    <td><?= $detalle['descuento_porcentaje'] ?>%</td>
                                    <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Totales -->
                    <div class="ds-row">
                        <div class="ds-col-md-8">
                            <!-- Espacio vacío para alinear totales a la derecha -->
                        </div>
                        <div class="ds-col-md-4">
                            <table class="ds-table ds-table--sm">
                                <tr>
                                    <td><strong>Subtotal:</strong></td>
                                    <td>$<?= number_format($presupuesto['subtotal'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>IVA (16%):</strong></td>
                                    <td>$<?= number_format($presupuesto['iva'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td><strong>$<?= number_format($presupuesto['total'], 2) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
