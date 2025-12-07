<?php

namespace App\Controllers;

use App\Models\PresupuestosModel;
use App\Models\PresupuestosDetallesModel;
use App\Models\PacientesModel;
use App\Models\ServiciosModel;
use App\Models\CotizacionesModel;
use App\Models\CotizacionesDetallesModel;
use CodeIgniter\Controller;

class PresupuestosController extends Controller
{
    protected $presupuestosModel;
    protected $detallesModel;
    protected $pacientesModel;
    protected $serviciosModel;

    public function __construct()
    {
        $this->presupuestosModel = new PresupuestosModel();
        $this->detallesModel = new PresupuestosDetallesModel();
        $this->pacientesModel = new PacientesModel();
        $this->serviciosModel = new ServiciosModel();
        
        // Load the form helper for form_open() and related functions
        helper('form');
    }

    // Listado de presupuestos
    public function index()
    {
        // Obtener parámetros de búsqueda y filtros
        $estado = $this->request->getGet('estado');
        $paciente = $this->request->getGet('paciente');
        $fecha_inicio = $this->request->getGet('fecha_inicio');
        $fecha_fin = $this->request->getGet('fecha_fin');
        $monto_min = $this->request->getGet('monto_min');
        $monto_max = $this->request->getGet('monto_max');
        $search = $this->request->getGet('search');
        
        // Construir consulta base
        $builder = $this->presupuestosModel->select('presupuestos.*, 
                                                    pacientes.nombre as paciente_nombre,
                                                    pacientes.primer_apellido as paciente_apellido,
                                                    usuarios.nombre as medico_nombre')
                                             ->join('pacientes', 'pacientes.id = presupuestos.id_paciente')
                                             ->join('usuarios', 'usuarios.id = presupuestos.id_usuario');
        
        // Aplicar filtros
        if ($estado) {
            $builder->where('presupuestos.estado', $estado);
        }
        
        if ($paciente) {
            $builder->where('presupuestos.id_paciente', $paciente);
        }
        
        if ($fecha_inicio) {
            $builder->where('presupuestos.fecha_emision >=', $fecha_inicio . ' 00:00:00');
        }
        
        if ($fecha_fin) {
            $builder->where('presupuestos.fecha_emision <=', $fecha_fin . ' 23:59:59');
        }
        
        if ($monto_min) {
            $builder->where('presupuestos.total >=', $monto_min);
        }
        
        if ($monto_max) {
            $builder->where('presupuestos.total <=', $monto_max);
        }
        
        if ($search) {
            $builder->groupStart()
                    ->like('presupuestos.folio', $search)
                    ->orLike('pacientes.nombre', $search)
                    ->orLike('pacientes.primer_apellido', $search)
                    ->orLike('usuarios.nombre', $search)
                    ->groupEnd();
        }
        
        // Paginación
        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;
        
        $data['presupuestos'] = $builder->orderBy('presupuestos.fecha_emision', 'DESC')
                                        ->paginate($perPage);
        
        $data['pager'] = $this->presupuestosModel->pager;
        $data['total'] = $this->presupuestosModel->pager->getTotal();
        $data['perPage'] = $perPage;
        $data['page'] = $page;
        
        // Datos para filtros
        $data['estados'] = [
            '' => 'Todos',
            'borrador' => 'Borrador',
            'pendiente' => 'Pendiente',
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado',
            'convertido' => 'Convertido'
        ];
        
        $data['pacientes'] = $this->pacientesModel->orderBy('nombre', 'ASC')->findAll();
        
        // Mantener valores de filtros
        $data['filtros'] = [
            'estado' => $estado,
            'paciente' => $paciente,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'monto_min' => $monto_min,
            'monto_max' => $monto_max,
            'search' => $search,
        ];
        
        // Variables para el layout
        $data['pageTitle'] = 'Presupuestos';
        $data['currentPage'] = 'presupuestos';
        
        return view('presupuestos/index', $data);
    }

    // Formulario de creación
    public function create()
    {
        // Obtener pacientes y servicios para selects
        $data['pacientes'] = $this->pacientesModel->findAll();
        $data['servicios'] = $this->serviciosModel->findAll();
        
        // Generar folio único
        $data['folio'] = $this->presupuestosModel->generateFolio();
        
        // Variables para el layout
        $data['pageTitle'] = 'Crear Presupuesto';
        $data['currentPage'] = 'presupuestos';
        
        return view('presupuestos/create', $data);
    }

    // Guardar nuevo presupuesto
    public function store()
    {
        // Validar datos principales
        $rules = [
            'id_paciente' => 'required|integer',
            'id_usuario' => 'required|integer',
            'fecha_vigencia' => 'required|valid_date[Y-m-d]',
            'observaciones' => 'permit_empty|string|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'PresupuestosController::store() - Validación fallida: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener datos del formulario
        $presupuestoData = [
            'id_paciente' => $this->request->getPost('id_paciente'),
            'id_usuario' => $this->request->getPost('id_usuario'),
            'folio' => $this->request->getPost('folio'),
            'fecha_emision' => date('Y-m-d H:i:s'),
            'fecha_vigencia' => $this->request->getPost('fecha_vigencia'),
            'observaciones' => $this->request->getPost('observaciones'),
            'estado' => 'borrador',
            'subtotal' => 0.00,  // Required by Model validation - will be updated after detalles are inserted
            'iva' => 0.00,        // Required by Model validation - will be updated after detalles are inserted
            'total' => 0.00,      // Required by Model validation - will be updated after detalles are inserted
        ];

        log_message('debug', 'PresupuestosController::store() - Datos del presupuesto: ' . json_encode($presupuestoData));

        // Obtener detalles del formulario
        $detalles = $this->request->getPost('detalles');
        if (empty($detalles)) {
            log_message('error', 'PresupuestosController::store() - No hay detalles en el formulario');
            return redirect()->back()->withInput()->with('error', 'Debe agregar al menos un servicio al presupuesto');
        }

        log_message('debug', 'PresupuestosController::store() - Detalles recibidos: ' . json_encode($detalles));

        // Iniciar transacción
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insertar presupuesto
            log_message('debug', 'PresupuestosController::store() - Intentando insertar presupuesto');
            $id_presupuesto = $this->presupuestosModel->insert($presupuestoData);
            
            log_message('debug', 'PresupuestosController::store() - Resultado insert: ' . var_export($id_presupuesto, true));
            
            // Validar que la inserción fue exitosa
            if (!$id_presupuesto) {
                log_message('error', 'PresupuestosController::store() - Insert retornó falso/nulo');
                log_message('error', 'PresupuestosController::store() - Errores del modelo: ' . json_encode($this->presupuestosModel->errors()));
                throw new \Exception('Error al insertar el presupuesto en la base de datos');
            }

            // Insertar detalles
            $subtotal = 0;
            foreach ($detalles as $detalle) {
                if (!empty($detalle['id_servicio']) && !empty($detalle['cantidad']) && !empty($detalle['precio_unitario'])) {
                    // Calcular subtotal para este detalle
                    $cantidad = floatval($detalle['cantidad']);
                    $precio_unitario = floatval($detalle['precio_unitario']);
                    $descuento_porcentaje = floatval($detalle['descuento_porcentaje'] ?? 0);
                    
                    $subtotal_detalle = $cantidad * $precio_unitario;
                    $descuento_monto = $subtotal_detalle * ($descuento_porcentaje / 100);
                    $subtotal_final = $subtotal_detalle - $descuento_monto;
                    
                    $detalleData = [
                        'id_presupuesto' => $id_presupuesto,
                        'id_servicio' => $detalle['id_servicio'],
                        'descripcion' => $detalle['descripcion'] ?? '',
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio_unitario,
                        'descuento_porcentaje' => $descuento_porcentaje,
                        'subtotal' => $subtotal_final,
                    ];

                    $this->detallesModel->saveDetalle($detalleData);
                    $subtotal += $subtotal_final;
                }
            }

            // Calcular totales
            $iva = $subtotal * 0.16; // 16% IVA
            $total = $subtotal + $iva;

            // Actualizar presupuesto con totales
            if ($id_presupuesto && is_numeric($id_presupuesto)) {
                $this->presupuestosModel->update((int)$id_presupuesto, [
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'total' => $total,
                ]);
            } else {
                throw new \Exception('ID de presupuesto inválido');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'PresupuestosController::store() - Transacción falló');
                return redirect()->back()->withInput()->with('error', 'Error al guardar el presupuesto');
            }

            // Registrar actividad en el historial del paciente
            $historialModel = new \App\Models\HistorialActividadesModel();
            $historialModel->registrarActividad(
                $presupuestoData['id_paciente'],     // id_paciente
                'presupuesto',                       // tipo_actividad
                $id_presupuesto,                    // id_referencia
                'Presupuesto creado - Folio: ' . $presupuestoData['folio']  // descripción
            );

            log_message('info', 'PresupuestosController::store() - Presupuesto creado exitosamente con ID: ' . $id_presupuesto);
            return redirect()->to('/presupuestos')->with('success', 'Presupuesto creado correctamente');

        } catch (\Exception $e) {
            log_message('error', 'PresupuestosController::store() - Excepción: ' . $e->getMessage());
            log_message('error', 'PresupuestosController::store() - Stack trace: ' . $e->getTraceAsString());
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Mostrar detalles de un presupuesto
    public function show($id)
    {
        $presupuesto = $this->presupuestosModel->getPresupuestoWithDetalles($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos')->with('error', 'Presupuesto no encontrado');
        }
        
        $data['presupuesto'] = $presupuesto;
        $data['pageTitle'] = 'Detalles del Presupuesto';
        $data['currentPage'] = 'presupuestos';
        
        return view('presupuestos/show', $data);
    }

    // Formulario de edición
    public function edit($id)
    {
        $presupuesto = $this->presupuestosModel->getPresupuestoWithDetalles($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos')->with('error', 'Presupuesto no encontrado');
        }
        
        if ($presupuesto['estado'] !== 'borrador') {
            return redirect()->to('/presupuestos')->with('error', 'Solo se pueden editar presupuestos en borrador');
        }
        
        $data['presupuesto'] = $presupuesto;
        $data['pacientes'] = $this->pacientesModel->findAll();
        $data['servicios'] = $this->serviciosModel->findAll();
        $data['pageTitle'] = 'Editar Presupuesto';
        $data['currentPage'] = 'presupuestos';
        
        return view('presupuestos/edit', $data);
    }

    // Actualizar presupuesto
    public function update($id)
    {
        $presupuesto = $this->presupuestosModel->find($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos')->with('error', 'Presupuesto no encontrado');
        }
        
        if ($presupuesto['estado'] !== 'borrador') {
            return redirect()->to('/presupuestos')->with('error', 'Solo se pueden editar presupuestos en borrador');
        }
        
        // Validar datos principales
        $rules = [
            'id_paciente' => 'required|integer',
            'id_usuario' => 'required|integer',
            'fecha_vigencia' => 'required|valid_date[Y-m-d]',
            'observaciones' => 'permit_empty|string|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener datos del formulario
        $presupuestoData = [
            'id_paciente' => $this->request->getPost('id_paciente'),
            'id_usuario' => $this->request->getPost('id_usuario'),
            'fecha_vigencia' => $this->request->getPost('fecha_vigencia'),
            'observaciones' => $this->request->getPost('observaciones'),
        ];

        // Obtener detalles del formulario
        $detalles = $this->request->getPost('detalles');
        if (empty($detalles)) {
            return redirect()->back()->withInput()->with('error', 'Debe agregar al menos un servicio al presupuesto');
        }

        // Iniciar transacción
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Actualizar presupuesto
            $this->presupuestosModel->update($id, $presupuestoData);

            // Eliminar detalles existentes
            $this->detallesModel->deleteByPresupuesto($id);

            // Insertar nuevos detalles
            $subtotal = 0;
            foreach ($detalles as $detalle) {
                if (!empty($detalle['id_servicio']) && !empty($detalle['cantidad']) && !empty($detalle['precio_unitario'])) {
                    // Calcular subtotal para este detalle
                    $cantidad = floatval($detalle['cantidad']);
                    $precio_unitario = floatval($detalle['precio_unitario']);
                    $descuento_porcentaje = floatval($detalle['descuento_porcentaje'] ?? 0);
                    
                    $subtotal_detalle = $cantidad * $precio_unitario;
                    $descuento_monto = $subtotal_detalle * ($descuento_porcentaje / 100);
                    $subtotal_final = $subtotal_detalle - $descuento_monto;
                    
                    $detalleData = [
                        'id_presupuesto' => $id,
                        'id_servicio' => $detalle['id_servicio'],
                        'descripcion' => $detalle['descripcion'] ?? '',
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio_unitario,
                        'descuento_porcentaje' => $descuento_porcentaje,
                        'subtotal' => $subtotal_final,
                    ];

                    $this->detallesModel->saveDetalle($detalleData);
                    $subtotal += $subtotal_final;
                }
            }

            // Calcular totales
            $iva = $subtotal * 0.16; // 16% IVA
            $total = $subtotal + $iva;
            // Actualizar presupuesto con totales
            if ($id && is_numeric($id)) {
                $this->presupuestosModel->update((int)$id, [
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'total' => $total,
                ]);
            } else {
                throw new \Exception('ID de presupuesto inválido');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Error al actualizar el presupuesto');
            }

            return redirect()->to('/presupuestos')->with('success', 'Presupuesto actualizado correctamente');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Enviar presupuesto por email
    public function send($id)
    {
        $presupuesto = $this->presupuestosModel->find($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos')->with('error', 'Presupuesto no encontrado');
        }
        
        if ($presupuesto['estado'] !== 'borrador') {
            return redirect()->to('/presupuestos')->with('error', 'Solo se pueden enviar presupuestos en borrador');
        }
        
        // Cambiar estado a pendiente
        $this->presupuestosModel->cambiarEstado($id, 'pendiente');
        
        // Aquí se implementaría el envío de email
        
        return redirect()->to('/presupuestos')->with('success', 'Presupuesto enviado correctamente');
    }

    // Aprobar presupuesto
    public function approve($id)
    {
        $presupuesto = $this->presupuestosModel->find($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos')->with('error', 'Presupuesto no encontrado');
        }
        
        if ($presupuesto['estado'] !== 'pendiente') {
            return redirect()->to('/presupuestos')->with('error', 'Solo se pueden aprobar presupuestos pendientes');
        }
        
        // Cambiar estado a aprobado
        $this->presupuestosModel->cambiarEstado($id, 'aprobado');
        
        return redirect()->to('/presupuestos')->with('success', 'Presupuesto aprobado correctamente');
    }

    // Rechazar presupuesto
    public function reject($id)
    {
        $presupuesto = $this->presupuestosModel->find($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos')->with('error', 'Presupuesto no encontrado');
        }
        
        if ($presupuesto['estado'] !== 'pendiente') {
            return redirect()->to('/presupuestos')->with('error', 'Solo se pueden rechazar presupuestos pendientes');
        }
        
        // Cambiar estado a rechazado
        $this->presupuestosModel->cambiarEstado($id, 'rechazado');
        
        return redirect()->to('/presupuestos')->with('success', 'Presupuesto rechazado correctamente');
    }

    // Convertir presupuesto a cotización
    public function convert($id)
    {
        $presupuesto = $this->presupuestosModel->find($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos')->with('error', 'Presupuesto no encontrado');
        }
        
        if ($presupuesto['estado'] !== 'aprobado') {
            return redirect()->to('/presupuestos')->with('error', 'Solo se pueden convertir presupuestos aprobados');
        }
        
        // Iniciar transacción
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Crear cotización basada en el presupuesto
            $cotizacionModel = new CotizacionesModel();
            $cotizacionDetallesModel = new CotizacionesDetallesModel();
            
            $dataCotizacion = [
                'id_paciente' => $presupuesto['id_paciente'],
                'id_usuario' => $presupuesto['id_usuario'],
                'folio' => $cotizacionModel->generateFolio(),
                'fecha_emision' => date('Y-m-d H:i:s'),
                'fecha_vigencia' => date('Y-m-d', strtotime('+30 days')),
                'subtotal' => $presupuesto['subtotal'],
                'iva' => $presupuesto['iva'],
                'total' => $presupuesto['total'],
                'estado' => 'pendiente',
                'observaciones' => 'Convertido desde presupuesto: ' . $presupuesto['folio']
            ];
            
            $id_cotizacion = $cotizacionModel->insert($dataCotizacion);
            
            // Copiar detalles
            $detalles = $this->detallesModel->getDetallesByPresupuesto($id);
            
            foreach ($detalles as $detalle) {
                $cotizacionDetallesModel->insert([
                    'id_cotizacion' => $id_cotizacion,
                    'id_servicio' => $detalle['id_servicio'],
                    'descripcion' => $detalle['descripcion'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'descuento_porcentaje' => $detalle['descuento_porcentaje'],
                    'subtotal' => $detalle['subtotal']
                ]);
            }
            
            // Actualizar estado del presupuesto
            $this->presupuestosModel->cambiarEstado($id, 'convertido');
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return redirect()->to('/presupuestos')->with('error', 'Error al convertir presupuesto a cotización');
            }
            
            return redirect()->to('/presupuestos')->with('success', 'Presupuesto convertido a cotización correctamente');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('/presupuestos')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Generar PDF del presupuesto
    public function pdf($id)
    {
        $presupuesto = $this->presupuestosModel->getPresupuestoWithDetalles($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos')->with('error', 'Presupuesto no encontrado');
        }
        
        $data['presupuesto'] = $presupuesto;
        
        // Cargar vista PDF
        $html = view('presupuestos/pdf', $data);
        
        // Configurar DomPDF si está disponible
        if (class_exists('Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // Descargar PDF
            $dompdf->stream('presupuesto_' . $presupuesto['folio'] . '.pdf', ['Attachment' => true]);
        } else {
            // Alternativa simple sin DomPDF - mostrar HTML para impresión
            return $this->response
                ->setHeader('Content-Type', 'text/html')
                ->setBody($html);
        }
    }

    // Eliminar presupuesto (soft delete)
    public function delete($id)
    {
        $presupuesto = $this->presupuestosModel->find($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos')->with('error', 'Presupuesto no encontrado');
        }
        
        // Solo se pueden eliminar presupuestos en borrador o rechazados
        if (!in_array($presupuesto['estado'], ['borrador', 'rechazado'])) {
            return redirect()->to('/presupuestos')->with('error', 'Solo se pueden eliminar presupuestos en borrador o rechazados');
        }
        
        // Soft delete
        $this->presupuestosModel->delete($id);
        
        return redirect()->to('/presupuestos')->with('success', 'Presupuesto eliminado correctamente');
    }

    // Vista de presupuestos eliminados
    public function deleted()
    {
        $data['presupuestos'] = $this->presupuestosModel->onlyDeleted()
                                                        ->select('presupuestos.*, 
                                                                pacientes.nombre as paciente_nombre,
                                                                pacientes.primer_apellido as paciente_apellido,
                                                                usuarios.nombre as medico_nombre')
                                                        ->join('pacientes', 'pacientes.id = presupuestos.id_paciente')
                                                        ->join('usuarios', 'usuarios.id = presupuestos.id_usuario')
                                                        ->orderBy('presupuestos.deleted_at', 'DESC')
                                                        ->findAll();
        
        $data['pageTitle'] = 'Presupuestos Eliminados';
        $data['currentPage'] = 'presupuestos';
        
        return view('presupuestos/deleted', $data);
    }

    // Restaurar presupuesto eliminado
    public function restore($id)
    {
        $presupuesto = $this->presupuestosModel->onlyDeleted()->find($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos/deleted')->with('error', 'Presupuesto eliminado no encontrado');
        }
        
        // Restaurar
        $this->presupuestosModel->restore($id);
        
        return redirect()->to('/presupuestos/deleted')->with('success', 'Presupuesto restaurado correctamente');
    }

    // Eliminar permanentemente
    public function forceDelete($id)
    {
        $presupuesto = $this->presupuestosModel->onlyDeleted()->find($id);
        
        if (!$presupuesto) {
            return redirect()->to('/presupuestos/deleted')->with('error', 'Presupuesto eliminado no encontrado');
        }
        
        // Iniciar transacción para eliminar permanentemente
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Eliminar detalles
            $this->detallesModel->where('id_presupuesto', $id)->delete();
            
            // Eliminar presupuesto permanentemente
            $this->presupuestosModel->purgeDeleted();
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return redirect()->to('/presupuestos/deleted')->with('error', 'Error al eliminar permanentemente el presupuesto');
            }
            
            return redirect()->to('/presupuestos/deleted')->with('success', 'Presupuesto eliminado permanentemente');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('/presupuestos/deleted')->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
