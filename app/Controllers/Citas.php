<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Navigation;
use App\Models\CitasModel;
use App\Models\PacientesModel;
use App\Models\UsuariosModel;
use App\Models\ServiciosModel;

class Citas extends BaseController
{
    protected $citasModel;
    protected $pacientesModel;
    protected $usuariosModel;
    protected $serviciosModel;
    
    public function __construct()
    {
        $this->citasModel = new CitasModel();
        $this->pacientesModel = new PacientesModel();
        $this->usuariosModel = new UsuariosModel();
        $this->serviciosModel = new ServiciosModel();
    }
    
    /**
     * Vista principal - Lista de citas
     */
    public function index()
    {
        $navigationData = Navigation::prepareNavigationData('citas', [
            'subtitle' => 'Lista de todas las citas programadas'
        ]);
        
        // Obtener filtros
        $filtros = [
            'estado' => $this->request->getGet('estado'),
            'fecha' => $this->request->getGet('fecha') ?? date('Y-m-d'),
            'id_paciente' => $this->request->getGet('paciente'),
        ];
        
        // Obtener citas del día o según filtros
        $fecha = $filtros['fecha'];
        $citas = $this->citasModel->getCitasDelDia($fecha);
        
        // Formatear citas para la vista
        $citasFormateadas = [];
        foreach ($citas as $cita) {
            $citasFormateadas[] = [
                'id' => $cita['id'] ?? null,
                'id_paciente' => $cita['id_paciente'] ?? null,
                'paciente_id' => $cita['id_paciente'] ?? null,
                'paciente_nombre' => trim(($cita['paciente_nombre'] ?? '') . ' ' . ($cita['paciente_apellido'] ?? '')),
                'paciente_apellido' => $cita['paciente_apellido'] ?? '',
                'fecha_inicio' => $cita['fecha_inicio'] ?? null,
                'fecha_fin' => $cita['fecha_fin'] ?? null,
                'hora' => $cita['fecha_inicio'] ? date('H:i', strtotime($cita['fecha_inicio'])) : '--:--',
                'hora_fin' => $cita['fecha_fin'] ? date('H:i', strtotime($cita['fecha_fin'])) : '--:--',
                'servicio' => $cita['servicio_nombre'] ?? 'Sin servicio',
                'estado' => $cita['estado'] ?? 'programada',
                'tipo_cita' => $cita['tipo_cita'] ?? '',
                'titulo' => $cita['titulo'] ?? 'Sin título',
                'doctor_nombre' => $cita['doctor_nombre'] ?? 'Sin asignar',
                'id_usuario' => $cita['id_usuario'] ?? null,
            ];
        }
        
        // Obtener lista de pacientes para filtros
        $pacientes = $this->pacientesModel->orderBy('nombre', 'ASC')->findAll();
        
        $data = array_merge($navigationData, [
            'citas' => $citasFormateadas,
            'filtros' => $filtros,
            'pacientes' => $pacientes,
            'estados' => ['programada', 'confirmada', 'en_progreso', 'completada', 'cancelada'],
        ]);
        
        return view('citas/index', $data);
    }
    
    /**
     * Vista del calendario
     */
    public function calendario()
    {
        $navigationData = Navigation::prepareNavigationData('calendario', [
            'subtitle' => 'Vista de calendario de citas'
        ]);
        
        // Obtener pacientes para el modal de nueva cita
        $pacientes = $this->pacientesModel->orderBy('nombre', 'ASC')->findAll();
        
        // Obtener doctores (usuarios)
        $doctores = $this->usuariosModel->findAll();
        
        // Obtener servicios
        $servicios = $this->serviciosModel->findAll();
        
        // Obtener colores
        $coloresTipo = $this->citasModel->getColoresPorTipo();
        $coloresEstado = $this->citasModel->getColoresPorEstado();
        
        $data = array_merge($navigationData, [
            'pacientes' => $pacientes,
            'doctores' => $doctores,
            'servicios' => $servicios,
            'coloresTipo' => $coloresTipo,
            'coloresEstado' => $coloresEstado,
            'fechaActual' => date('Y-m-d'),
        ]);
        
        return view('citas/calendario', $data);
    }
    
    /**
     * Formulario de nueva cita
     */
    public function nueva()
    {
        $navigationData = Navigation::prepareNavigationData('cita_nueva', [
            'subtitle' => 'Programar nueva cita'
        ]);
        
        $pacientes = $this->pacientesModel->orderBy('nombre', 'ASC')->findAll();
        $servicios = $this->serviciosModel->findAll();
        $doctores = $this->usuariosModel->findAll();
        
        $data = array_merge($navigationData, [
            'formAction' => base_url('/citas/guardar'),
            'pacientes' => $pacientes,
            'servicios' => $servicios,
            'doctores' => $doctores,
            'cita' => [],
            'tiposCita' => ['consulta', 'tratamiento', 'revision', 'urgencia'],
        ]);
        
        return view('citas/form', $data);
    }
    
    /**
     * Guardar nueva cita
     */
    public function guardar()
    {
        $data = $this->request->getPost();
        
        // Validar datos
        $rules = [
            'id_paciente' => 'required|integer',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'id_usuario' => 'required|integer',
            'titulo' => 'required|string|max_length[200]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Convertir datetime-local a formato correcto
        $fecha_inicio = $data['fecha_inicio'];
        $fecha_fin = $data['fecha_fin'];
        
        // Validar formato de fechas
        if (!strtotime($fecha_inicio) || !strtotime($fecha_fin)) {
            return redirect()->back()->withInput()
                   ->with('error', 'Formato de fecha inválido');
        }
        
        // Asegurar formato correcto (agregar segundos si falta)
        if (strlen($fecha_inicio) === 16) {
            $fecha_inicio .= ':00';
        }
        if (strlen($fecha_fin) === 16) {
            $fecha_fin .= ':00';
        }
        
        // Obtener ID de usuario (doctor) - ya viene del formulario
        $id_usuario = $data['id_usuario'];
        
        // Validar que la fecha fin sea después de la fecha inicio
        if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
            return redirect()->back()->withInput()
                   ->with('error', 'La hora de fin debe ser posterior a la hora de inicio');
        }
        
        // Verificar disponibilidad del doctor
        if (!$this->citasModel->verificarDisponibilidad($id_usuario, $fecha_inicio, $fecha_fin)) {
            return redirect()->back()->withInput()
                   ->with('error', 'El horario seleccionado ya está ocupado');
        }

        // Obtener nombre del paciente para el título si no está completado
        $paciente = $this->pacientesModel->find($data['id_paciente']);
        $titulo = $data['titulo'] ?? ($paciente ? trim($paciente['nombre'] . ' ' . ($paciente['primer_apellido'] ?? '')) : 'Cita');
        
        $citaData = [
            'id_paciente' => $data['id_paciente'],
            'id_usuario' => $id_usuario,
            'id_servicio' => $data['id_servicio'] ?? null,
            'titulo' => $titulo,
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'estado' => 'programada',
            'tipo_cita' => $data['tipo_cita'] ?? 'consulta',
            'color' => $this->citasModel->getColoresPorTipo()[$data['tipo_cita'] ?? 'consulta'] ?? '#5ccdde',
            'notas' => $data['notas'] ?? null,
        ];
        
        if ($this->citasModel->insert($citaData)) {
            // Registrar actividad en el historial del paciente
            $historialModel = new \App\Models\HistorialActividadesModel();
            $citaId = $this->citasModel->getInsertID();
            $historialModel->registrarActividad(
                $citaData['id_paciente'],                          // id_paciente
                'cita',                                            // tipo_actividad
                $citaId,                                           // id_referencia
                'Cita programada - ' . $citaData['titulo']        // descripción
            );
            
            return redirect()->to('/citas')
                   ->with('success', 'Cita programada correctamente');
        } else {
            return redirect()->back()->withInput()
                   ->with('error', 'Error al programar la cita');
        }
    }
    
    /**
     * Ver detalles de una cita
     */
    public function ver($id)
    {
        $cita = $this->citasModel->getCitaWithRelations($id);
        
        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada');
        }
        
        $navigationData = Navigation::prepareNavigationData('cita_detalle', [
            'subtitle' => 'Detalles de la cita'
        ]);
        
        $data = array_merge($navigationData, [
            'cita' => $cita,
        ]);
        
        return view('citas/ver', $data);
    }
    
    /**
     * Formulario de edición
     */
    public function editar($id)
    {
        $cita = $this->citasModel->getCitaWithRelations($id);
        
        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada');
        }
        
        // Verificar si puede editarse
        $rol = session()->get('rol_id') ?? 1;
        $id_usuario = session()->get('id') ?? 1;
        
        if (!$this->citasModel->puedeEditarse($id, $id_usuario, $rol)) {
            return redirect()->to('/citas')->with('error', 'Esta cita no puede ser editada');
        }
        
        $navigationData = Navigation::prepareNavigationData('cita_editar', [
            'subtitle' => 'Editar cita'
        ]);
        
        $pacientes = $this->pacientesModel->orderBy('nombre', 'ASC')->findAll();
        $servicios = $this->serviciosModel->findAll();
        $doctores = $this->usuariosModel->findAll();
        
        // Formatear datos para el formulario
        $cita['fecha'] = date('Y-m-d', strtotime($cita['fecha_inicio']));
        $cita['hora'] = date('H:i', strtotime($cita['fecha_inicio']));
        $cita['duracion'] = (strtotime($cita['fecha_fin']) - strtotime($cita['fecha_inicio'])) / 60;
        
        $data = array_merge($navigationData, [
            'formAction' => base_url('/citas/' . $id . '/actualizar'),
            'pacientes' => $pacientes,
            'servicios' => $servicios,
            'doctores' => $doctores,
            'cita' => $cita,
            'tiposCita' => ['consulta', 'tratamiento', 'revision', 'urgencia'],
        ]);
        
        return view('citas/form', $data);
    }
    
    /**
     * Actualizar cita
     */
    public function actualizar($id)
    {
        $cita = $this->citasModel->find($id);
        
        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada');
        }
        
        $data = $this->request->getPost();
        
        // Validar datos
        $rules = [
            'id_paciente' => 'required|integer',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'id_usuario' => 'required|integer',
            'titulo' => 'required|string|max_length[200]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Convertir datetime-local a formato correcto
        $fecha_inicio = $data['fecha_inicio'];
        $fecha_fin = $data['fecha_fin'];
        
        // Validar formato de fechas
        if (!strtotime($fecha_inicio) || !strtotime($fecha_fin)) {
            return redirect()->back()->withInput()
                   ->with('error', 'Formato de fecha inválido');
        }
        
        // Asegurar formato correcto (agregar segundos si falta)
        if (strlen($fecha_inicio) === 16) {
            $fecha_inicio .= ':00';
        }
        if (strlen($fecha_fin) === 16) {
            $fecha_fin .= ':00';
        }
        
        // Validar que la fecha fin sea después de la fecha inicio
        if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
            return redirect()->back()->withInput()
                   ->with('error', 'La hora de fin debe ser posterior a la hora de inicio');
        }
        
        $id_usuario = $data['id_usuario'];
        
        // Verificar disponibilidad (excluyendo la cita actual)
        if (!$this->citasModel->verificarDisponibilidad($id_usuario, $fecha_inicio, $fecha_fin, $id)) {
            return redirect()->back()->withInput()
                   ->with('error', 'El horario seleccionado ya está ocupado');
        }
        
        $updateData = [
            'id_paciente' => $data['id_paciente'],
            'id_usuario' => $id_usuario,
            'id_servicio' => $data['id_servicio'] ?? null,
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'tipo_cita' => $data['tipo_cita'] ?? $cita['tipo_cita'],
            'color' => $this->citasModel->getColoresPorTipo()[$data['tipo_cita'] ?? $cita['tipo_cita']] ?? $cita['color'],
            'notas' => $data['notas'] ?? null,
        ];
        
        // Actualizar estado solo si viene en los datos y es edición
        if (!empty($data['estado'])) {
            $updateData['estado'] = $data['estado'];
        }
        
        if ($this->citasModel->update($id, $updateData)) {
            // Registrar actividad en historial
            $historialModel = new \App\Models\HistorialActividadesModel();
            $historialModel->registrarActividad(
                $updateData['id_paciente'],
                'cita',
                $id,
                'Cita actualizada - ' . $updateData['titulo']
            );
            
            return redirect()->to('/citas')
                   ->with('success', 'Cita actualizada correctamente');
        } else {
            return redirect()->back()->withInput()
                   ->with('error', 'Error al actualizar la cita');
        }
    }
    
    /**
     * Eliminar/Cancelar cita
     */
    public function eliminar($id)
    {
        $cita = $this->citasModel->find($id);
        
        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada');
        }
        
        // Registrar eliminación en historial antes de eliminar
        $historialModel = new \App\Models\HistorialActividadesModel();
        $historialModel->registrarActividad(
            $cita['id_paciente'],
            'cita',
            $id,
            'Cita eliminada - ' . $cita['titulo']
        );
        
        // Soft delete
        if ($this->citasModel->delete($id)) {
            return redirect()->to('/citas')
                   ->with('success', 'Cita eliminada correctamente');
        } else {
            return redirect()->to('/citas')
                   ->with('error', 'Error al eliminar la cita');
        }
    }
    
    /**
     * Cambiar estado de cita
     */
    public function cambiarEstado($id)
    {
        $nuevo_estado = $this->request->getPost('estado') ?? $this->request->getGet('estado');
        
        if (!$nuevo_estado) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'error' => 'Estado no especificado']);
            }
            return redirect()->back()->with('error', 'Estado no especificado');
        }
        
        $resultado = $this->citasModel->cambiarEstado($id, $nuevo_estado);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($resultado);
        }
        
        if ($resultado['success']) {
            return redirect()->to('/citas')
                   ->with('success', 'Estado de cita actualizado correctamente');
        } else {
            return redirect()->back()
                   ->with('error', $resultado['error']);
        }
    }
    
    // ============================================
    // API ENDPOINTS PARA FULLCALENDAR
    // ============================================
    
    /**
     * API: Obtener citas para FullCalendar
     */
    public function getCitas()
    {
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        
        if (!$start || !$end) {
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }
        
        // Obtener filtros
        $filtros = [
            'estado' => $this->request->getGet('estado'),
            'tipo_cita' => $this->request->getGet('tipo_cita'),
            'id_paciente' => $this->request->getGet('id_paciente'),
            'id_usuario' => $this->request->getGet('id_usuario'),
        ];
        
        $citas = $this->citasModel->getCitasByRango($start, $end, $filtros);
        
        $events = [];
        foreach ($citas as $cita) {
            $pacienteNombre = trim($cita['paciente_nombre'] . ' ' . ($cita['paciente_apellido'] ?? ''));
            
            $events[] = [
                'id' => $cita['id'],
                'title' => $pacienteNombre,
                'start' => $cita['fecha_inicio'],
                'end' => $cita['fecha_fin'],
                'color' => $cita['color'],
                'extendedProps' => [
                    'id_paciente' => $cita['id_paciente'],
                    'id_usuario' => $cita['id_usuario'],
                    'paciente' => $pacienteNombre,
                    'paciente_telefono' => $cita['paciente_telefono'] ?? '',
                    'paciente_email' => $cita['paciente_email'] ?? '',
                    'doctor' => $cita['doctor_nombre'] ?? '',
                    'servicio' => $cita['servicio_nombre'] ?? '',
                    'estado' => $cita['estado'],
                    'tipo_cita' => $cita['tipo_cita'],
                    'descripcion' => $cita['descripcion'] ?? '',
                    'notas' => $cita['notas'] ?? '',
                    'titulo' => $cita['titulo'],
                ]
            ];
        }
        
        return $this->response->setJSON($events);
    }
    
    /**
     * API: Obtener detalles de una cita
     */
    public function getCita($id)
    {
        $cita = $this->citasModel->getCitaWithRelations($id);
        
        if (!$cita) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Cita no encontrada']);
        }
        
        return $this->response->setJSON($cita);
    }
    
    /**
     * API: Crear cita (desde modal)
     */
    public function store()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        // Validar
        if (empty($data['id_paciente']) || empty($data['fecha_inicio']) || empty($data['fecha_fin'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Faltan campos obligatorios'
            ]);
        }
        
        $id_usuario = $data['id_usuario'] ?? session()->get('id') ?? 1;
        
        // Verificar disponibilidad
        if (!$this->citasModel->verificarDisponibilidad($id_usuario, $data['fecha_inicio'], $data['fecha_fin'])) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'El horario seleccionado no está disponible'
            ]);
        }
        
        // Obtener paciente para título por defecto
        $paciente = $this->pacientesModel->find($data['id_paciente']);
        $titulo = $data['titulo'] ?? ($paciente ? $paciente['nombre'] . ' ' . ($paciente['primer_apellido'] ?? '') : 'Cita');
        
        $citaData = [
            'id_paciente' => $data['id_paciente'],
            'id_usuario' => $id_usuario,
            'id_servicio' => $data['id_servicio'] ?? null,
            'titulo' => $titulo,
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'estado' => 'programada',
            'tipo_cita' => $data['tipo_cita'] ?? 'consulta',
            'color' => $this->citasModel->getColoresPorTipo()[$data['tipo_cita'] ?? 'consulta'] ?? '#5ccdde',
            'notas' => $data['notas'] ?? null,
        ];
        
        $id = $this->citasModel->insert($citaData);
        
        if ($id) {
            $nuevaCita = $this->citasModel->getCitaWithRelations($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cita creada correctamente',
                'cita' => $nuevaCita,
                'id' => $id
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Error al crear la cita'
            ]);
        }
    }
    
    /**
     * API: Actualizar cita (desde modal o drag & drop)
     */
    public function update($id)
    {
        $cita = $this->citasModel->find($id);
        
        if (!$cita) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'error' => 'Cita no encontrada'
            ]);
        }
        
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        $id_usuario = $data['id_usuario'] ?? $cita['id_usuario'];
        $fecha_inicio = $data['fecha_inicio'] ?? $cita['fecha_inicio'];
        $fecha_fin = $data['fecha_fin'] ?? $cita['fecha_fin'];
        
        // Verificar disponibilidad (excluyendo la cita actual)
        if (!$this->citasModel->verificarDisponibilidad($id_usuario, $fecha_inicio, $fecha_fin, $id)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'El horario seleccionado no está disponible'
            ]);
        }
        
        $updateData = [];
        
        // Solo actualizar campos proporcionados
        $campos = ['id_paciente', 'id_usuario', 'id_servicio', 'titulo', 'descripcion', 
                   'fecha_inicio', 'fecha_fin', 'estado', 'tipo_cita', 'color', 'notas'];
        
        foreach ($campos as $campo) {
            if (isset($data[$campo])) {
                $updateData[$campo] = $data[$campo];
            }
        }
        
        // Actualizar color si cambió el tipo
        if (isset($data['tipo_cita'])) {
            $updateData['color'] = $this->citasModel->getColoresPorTipo()[$data['tipo_cita']] ?? $cita['color'];
        }
        
        if ($this->citasModel->update($id, $updateData)) {
            $citaActualizada = $this->citasModel->getCitaWithRelations($id);
            
            // Registrar actividad en el historial del paciente si cambió el estado
            if (isset($updateData['estado'])) {
                $historialModel = new \App\Models\HistorialActividadesModel();
                $historialModel->registrarActividad(
                    $cita['id_paciente'],                              // id_paciente
                    'cita',                                            // tipo_actividad
                    $id,                                               // id_referencia
                    'Cita actualizada - Estado: ' . $updateData['estado']  // descripción
                );
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cita actualizada correctamente',
                'cita' => $citaActualizada
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Error al actualizar la cita'
            ]);
        }
    }
    
    /**
     * API: Actualizar fechas (drag & drop)
     */
    public function actualizarFecha($id)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        if (empty($data['fecha_inicio']) || empty($data['fecha_fin'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Fechas no proporcionadas'
            ]);
        }
        
        $id_usuario = session()->get('id') ?? 1;
        $rol = session()->get('rol_id') ?? 1;
        
        $resultado = $this->citasModel->actualizarFechas(
            $id, 
            $data['fecha_inicio'], 
            $data['fecha_fin'],
            $id_usuario,
            $rol
        );
        
        return $this->response->setJSON($resultado);
    }
    
    /**
     * API: Eliminar cita
     */
    public function delete($id)
    {
        $cita = $this->citasModel->find($id);
        
        if (!$cita) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'error' => 'Cita no encontrada'
            ]);
        }
        
        if ($this->citasModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cita eliminada correctamente'
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Error al eliminar la cita'
            ]);
        }
    }
    
    /**
     * API: Verificar disponibilidad de horario
     */
    public function verificarDisponibilidad()
    {
        // Aceptar tanto GET como POST
        $method = $this->request->getMethod();
        
        if ($method === 'POST' || $this->request->isAJAX()) {
            $data = $this->request->getJSON(true) ?? $this->request->getPost();
            $id_usuario = $data['id_usuario'] ?? session()->get('id') ?? 1;
            $fecha_inicio = $data['fecha_inicio'] ?? null;
            $fecha_fin = $data['fecha_fin'] ?? null;
            $excluir_id = $data['id_cita'] ?? null;
        } else {
            $id_usuario = $this->request->getGet('id_usuario') ?? session()->get('id') ?? 1;
            $fecha_inicio = $this->request->getGet('fecha_inicio');
            $fecha_fin = $this->request->getGet('fecha_fin');
            $excluir_id = $this->request->getGet('excluir_id');
        }
        
        if (!$fecha_inicio || !$fecha_fin) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Fechas no proporcionadas',
                'disponible' => false,
                'message' => 'Por favor proporcione fechas de inicio y fin'
            ]);
        }
        
        // Asegurar formato correcto
        if (strlen($fecha_inicio) === 16) {
            $fecha_inicio .= ':00';
        }
        if (strlen($fecha_fin) === 16) {
            $fecha_fin .= ':00';
        }
        
        $disponible = $this->citasModel->verificarDisponibilidad($id_usuario, $fecha_inicio, $fecha_fin, $excluir_id);
        
        $mensaje = $disponible ? 'Horario disponible' : 'Horario no disponible';
        $conflictos = [];
        
        if (!$disponible) {
            $conflictos = $this->citasModel->getCitasConflictivas($id_usuario, $fecha_inicio, $fecha_fin, $excluir_id);
        }
        
        return $this->response->setJSON([
            'disponible' => $disponible,
            'message' => $mensaje,
            'conflictos' => $conflictos
        ]);
    }
    
    /**
     * API: Buscar pacientes (para autocompletado)
     */
    public function buscarPacientes()
    {
        $query = $this->request->getGet('q');
        
        if (!$query || strlen($query) < 2) {
            return $this->response->setJSON([]);
        }
        
        $pacientes = $this->pacientesModel
            ->like('nombre', $query)
            ->orLike('primer_apellido', $query)
            ->orLike('segundo_apellido', $query)
            ->limit(10)
            ->findAll();
        
        $resultados = [];
        foreach ($pacientes as $p) {
            $resultados[] = [
                'id' => $p['id'],
                'nombre' => $p['nombre'] . ' ' . ($p['primer_apellido'] ?? '') . ' ' . ($p['segundo_apellido'] ?? ''),
                'telefono' => $p['telefono'] ?? '',
                'email' => $p['email'] ?? '',
            ];
        }
        
        return $this->response->setJSON($resultados);
    }
    
    /**
     * API: Obtener estadísticas de citas
     */
    public function estadisticas()
    {
        $id_usuario = $this->request->getGet('id_usuario');
        $fecha_inicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-01');
        $fecha_fin = $this->request->getGet('fecha_fin') ?? date('Y-m-t');
        
        $stats = $this->citasModel->getEstadisticas($id_usuario, $fecha_inicio, $fecha_fin);
        
        return $this->response->setJSON($stats);
    }
}
