<?= $this->extend('layout/ds_layout') ?>

<?= $this->section('content') ?>
<div class="ds-page">
    <div class="ds-page__header">
        <h1 class="ds-page__title">📄 Presupuestos</h1>
    </div>
    <div class="ds-container ds-container--fluid">
    <div class="ds-row">
        <div class="ds-col-12">
            <div class="ds-card">
                <div class="ds-card__header">
                    <h3 class="ds-card__title">Listado de Presupuestos</h3>
                    <div class="ds-card__actions ds-flex ds-gap-2">
                        <a href="<?= base_url('/presupuestos/create') ?>" class="ds-btn ds-btn--primary ds-btn--sm">
                            <i class="fas fa-plus"></i> Nuevo Presupuesto
                        </a>
                        <a href="<?= base_url('/presupuestos/deleted') ?>" class="ds-btn ds-btn--secondary ds-btn--sm">
                            <i class="fas fa-trash"></i> Papelera
                        </a>
                    </div>
                </div>
                <div class="ds-card__body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="ds-alert ds-alert--success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="ds-alert ds-alert--danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Filtros -->
                    <div class="ds-card ds-mb-4">
                        <div class="ds-card__header">
                            <h5 class="ds-m-0">Filtros de Búsqueda</h5>
                        </div>
                        <div class="ds-card__body">
                            <form method="GET" action="<?= base_url('/presupuestos') ?>">
                                <div class="ds-row">
                                    <div class="ds-col-md-3">
                                        <div class="ds-form-group">
                                            <label for="search" class="ds-label">Búsqueda</label>
                                            <input type="text" name="search" class="ds-input" 
                                                   id="search" value="<?= $filtros['search'] ?>" 
                                                   placeholder="Folio, paciente, médico...">
                                        </div>
                                    </div>
                                    <div class="ds-col-md-3">
                                        <div class="ds-form-group">
                                            <label for="estado" class="ds-label">Estado</label>
                                            <select name="estado" class="ds-input ds-select" id="estado">
                                                <?php foreach ($estados as $value => $label): ?>
                                                <option value="<?= $value ?>" <?= ($filtros['estado'] == $value) ? 'selected' : '' ?>>
                                                    <?= $label ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ds-col-md-3">
                                        <div class="ds-form-group">
                                            <label for="paciente" class="ds-label">Paciente</label>
                                            <select name="paciente" class="ds-input ds-select" id="paciente">
                                                <option value="">Todos los pacientes</option>
                                                <?php foreach ($pacientes as $paciente): ?>
                                                <option value="<?= $paciente['id'] ?>" 
                                                        <?= ($filtros['paciente'] == $paciente['id']) ? 'selected' : '' ?>>
                                                    <?= $paciente['nombre'] . ' ' . $paciente['primer_apellido'] ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ds-col-md-3">
                                        <div class="ds-form-group">
                                            <label class="ds-label">Rango de Fechas</label>
                                            <div class="ds-input-group">
                                                <input type="date" name="fecha_inicio" class="ds-input" 
                                                       value="<?= $filtros['fecha_inicio'] ?>" placeholder="Inicio">
                                                <input type="date" name="fecha_fin" class="ds-input" 
                                                       value="<?= $filtros['fecha_fin'] ?>" placeholder="Fin">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ds-row">
                                    <div class="ds-col-md-3">
                                        <div class="ds-form-group">
                                            <label class="ds-label">Rango de Montos</label>
                                            <div class="ds-input-group">
                                                <input type="number" name="monto_min" class="ds-input" 
                                                       value="<?= $filtros['monto_min'] ?>" placeholder="Mínimo" step="0.01">
                                                <input type="number" name="monto_max" class="ds-input" 
                                                       value="<?= $filtros['monto_max'] ?>" placeholder="Máximo" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ds-col-md-9">
                                        <div class="ds-form-group">
                                            <label class="ds-label">&nbsp;</label>
                                            <div class="ds-btn-group">
                                                <button type="submit" class="ds-btn ds-btn--primary">
                                                    <i class="fas fa-search"></i> Buscar
                                                </button>
                                                <a href="<?= base_url('/presupuestos') ?>" class="ds-btn ds-btn--secondary">
                                                    <i class="fas fa-times"></i> Limpiar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tabla de presupuestos -->
                    <div class="ds-table-responsive">
                        <table class="ds-table ds-table--bordered ds-table--hover">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Paciente</th>
                                    <th>Médico</th>
                                    <th>Fecha Emisión</th>
                                    <th>Vigencia</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($presupuestos)): ?>
                                    <tr>
                                        <td colspan="8" class="ds-text-center">No hay presupuestos registrados</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($presupuestos as $presupuesto): ?>
                                    <tr>
                                        <td><?= $presupuesto['folio'] ?></td>
                                        <td><?= $presupuesto['paciente_nombre'] . ' ' . $presupuesto['paciente_apellido'] ?></td>
                                        <td><?= $presupuesto['medico_nombre'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($presupuesto['fecha_emision'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($presupuesto['fecha_vigencia'])) ?></td>
                                        <td>$<?= number_format($presupuesto['total'], 2) ?></td>
                                        <td>
                                            <?= $this->include('presupuestos/_badge_estado', ['estado' => $presupuesto['estado']]) ?>
                                        </td>
                                        <td>
                                            <div class="ds-btn-group">
                                                <a href="<?= base_url('/presupuestos/show/' . $presupuesto['id']) ?>" 
                                                   class="ds-btn ds-btn--info ds-btn--sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($presupuesto['estado'] == 'borrador'): ?>
                                                    <a href="<?= base_url('/presupuestos/edit/' . $presupuesto['id']) ?>" 
                                                       class="ds-btn ds-btn--warning ds-btn--sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url('/presupuestos/send/' . $presupuesto['id']) ?>" 
                                                       class="ds-btn ds-btn--success ds-btn--sm" title="Enviar">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </a>
                                                    <a href="<?= base_url('/presupuestos/delete/' . $presupuesto['id']) ?>" 
                                                       class="ds-btn ds-btn--danger ds-btn--sm" title="Eliminar"
                                                       onclick="return confirm('¿Está seguro de eliminar este presupuesto?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($presupuesto['estado'] == 'pendiente'): ?>
                                                    <a href="<?= base_url('/presupuestos/approve/' . $presupuesto['id']) ?>" 
                                                       class="ds-btn ds-btn--success ds-btn--sm" title="Aprobar"
                                                       onclick="return confirm('¿Está seguro de aprobar este presupuesto?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="<?= base_url('/presupuestos/reject/' . $presupuesto['id']) ?>" 
                                                       class="ds-btn ds-btn--danger ds-btn--sm" title="Rechazar"
                                                       onclick="return confirm('¿Está seguro de rechazar este presupuesto?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($presupuesto['estado'] == 'aprobado'): ?>
                                                    <a href="<?= base_url('/presupuestos/convert/' . $presupuesto['id']) ?>" 
                                                       class="ds-btn ds-btn--primary ds-btn--sm" title="Convertir a Cotización"
                                                       onclick="return confirm('¿Convertir este presupuesto a cotización?')">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= base_url('/presupuestos/pdf/' . $presupuesto['id']) ?>" 
                                                   class="ds-btn ds-btn--secondary ds-btn--sm" title="PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <?php if ($total > $perPage): ?>
                    <div class="ds-row ds-mt-3">
                        <div class="ds-col-md-7">
                            <div class="ds-text-sm ds-text-gray-600">
                                Mostrando <?= ($page - 1) * $perPage + 1 ?> a <?= min($page * $perPage, $total) ?> 
                                de <?= $total ?> registros
                            </div>
                        </div>
                        <div class="ds-col-md-5 ds-text-right">
                            <?= $pager->links('default', 'default_full') ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
