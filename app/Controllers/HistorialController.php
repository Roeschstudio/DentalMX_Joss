<?php

namespace App\Controllers;

use App\Models\HistorialActividadesModel;
use App\Models\TratamientosRealizadosModel;
use App\Models\HistorialAdjuntosModel;
use App\Models\Patient;
use App\Controllers\Navigation;
use Dompdf\Dompdf;

/**
 * Controlador para gestionar el historial de actividades de los pacientes
 * 
 * Proporciona funcionalidades para:
 * - Visualizar el timeline de actividades
 * - Filtrar y buscar actividades
 * - Ver detalles de actividades específicas
 * - Gestionar tratamientos
 * - Exportar historial en diferentes formatos
 */
class HistorialController extends BaseController
{
    protected HistorialActividadesModel $historialModel;
    protected TratamientosRealizadosModel $tratamientosModel;
    protected HistorialAdjuntosModel $adjuntosModel;
    protected Patient $pacientesModel;

    public function __construct()
    {
        $this->historialModel = new HistorialActividadesModel();
        $this->tratamientosModel = new TratamientosRealizadosModel();
        $this->adjuntosModel = new HistorialAdjuntosModel();
        $this->pacientesModel = new Patient();
    }

    /**
     * Mostrar timeline principal del paciente
     */
    public function index($id_paciente = null)
    {
        if (!$id_paciente) {
            return redirect()->to('/pacientes')
                           ->with('error', 'Debe seleccionar un paciente');
        }

        // Verificar que el paciente exista
        $paciente = $this->pacientesModel->find($id_paciente);
        if (!$paciente) {
            return redirect()->to('/pacientes')
                           ->with('error', 'Paciente no encontrado');
        }

        // Obtener parámetros de filtrado
        $filtros = $this->getRequestFilters();
        $page = (int)($this->request->getGet('page') ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Obtener timeline del paciente
        $timeline = $this->historialModel->getTimelineByPaciente($id_paciente, $limit, $offset, $filtros);
        $totalActividades = $this->historialModel->countTimelineByPaciente($id_paciente, $filtros);
        
        // Obtener estadísticas del paciente
        $estadisticas = $this->historialModel->getEstadisticas($id_paciente);
        
        // Obtener tratamientos activos
        $tratamientosActivos = $this->tratamientosModel->getTratamientosActivos($id_paciente);
        
        // Obtener resumen financiero
        $resumenFinanciero = $this->tratamientosModel->getResumenFinanciero($id_paciente);

        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('paciente_historial', [
            'subtitle' => 'Historial de actividades',
            'paciente' => $paciente
        ]);

        $data = array_merge($navigationData, [
            'paciente' => $paciente,
            'timeline' => $timeline,
            'estadisticas' => $estadisticas,
            'tratamientos_activos' => $tratamientosActivos,
            'resumen_financiero' => $resumenFinanciero,
            'filtros' => $filtros,
            'tipos_actividad' => HistorialActividadesModel::$tiposActividad,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $totalActividades,
                'total_pages' => ceil($totalActividades / $limit)
            ]
        ]);

        return view('historial/index', $data);
    }

    /**
     * Mostrar detalles de una actividad específica
     */
    public function detalles($id_actividad)
    {
        // Obtener detalles de la actividad
        $actividad = $this->historialModel->getDetallesActividad($id_actividad);
        
        if (!$actividad) {
            return redirect()->back()
                           ->with('error', 'Actividad no encontrada');
        }

        // Obtener paciente
        $paciente = $this->pacientesModel->find($actividad['id_paciente']);

        // Obtener datos adicionales según tipo de actividad
        $datosAdicionales = $this->historialModel->getDatosActividad(
            $actividad['tipo_actividad'], 
            $actividad['id_referencia']
        );

        // Obtener adjuntos de la actividad
        $adjuntos = $this->adjuntosModel->getAdjuntosPorActividad($id_actividad);

        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('paciente_historial_detalle', [
            'subtitle' => 'Detalle de actividad',
            'paciente' => $paciente
        ]);

        $data = array_merge($navigationData, [
            'paciente' => $paciente,
            'actividad' => $actividad,
            'datos_adicionales' => $datosAdicionales,
            'adjuntos' => $adjuntos,
            'tipo_config' => HistorialActividadesModel::getTipoActividad($actividad['tipo_actividad'])
        ]);

        return view('historial/detalles', $data);
    }

    /**
     * Buscar actividades en el historial
     */
    public function buscar($id_paciente)
    {
        $termino = $this->request->getGet('q') ?? $this->request->getGet('termino');
        
        if (!$termino) {
            return redirect()->to("/historial/{$id_paciente}");
        }

        $paciente = $this->pacientesModel->find($id_paciente);
        if (!$paciente) {
            return redirect()->to('/pacientes')
                           ->with('error', 'Paciente no encontrado');
        }

        $actividades = $this->historialModel->buscarActividades($id_paciente, $termino);

        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('paciente_historial', [
            'subtitle' => 'Resultados de búsqueda',
            'paciente' => $paciente
        ]);

        $data = array_merge($navigationData, [
            'paciente' => $paciente,
            'actividades' => $actividades,
            'termino' => $termino,
            'tipos_actividad' => HistorialActividadesModel::$tiposActividad
        ]);

        return view('historial/busqueda', $data);
    }

    /**
     * Obtener actividades por tipo (AJAX)
     */
    public function porTipo($id_paciente, $tipo_actividad)
    {
        // Validar tipo de actividad
        $tiposValidos = array_keys(HistorialActividadesModel::$tiposActividad);
        
        if (!in_array($tipo_actividad, $tiposValidos)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Tipo de actividad no válido'
                ]);
            }
            return redirect()->back()->with('error', 'Tipo de actividad no válido');
        }

        $actividades = $this->historialModel->getActividadesPorTipo($id_paciente, $tipo_actividad);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'actividades' => $actividades,
                'tipo' => HistorialActividadesModel::getTipoActividad($tipo_actividad)
            ]);
        }

        $paciente = $this->pacientesModel->find($id_paciente);
        
        $navigationData = Navigation::prepareNavigationData('paciente_historial', [
            'subtitle' => 'Actividades: ' . HistorialActividadesModel::getTipoActividad($tipo_actividad)['label'],
            'paciente' => $paciente
        ]);

        $data = array_merge($navigationData, [
            'paciente' => $paciente,
            'actividades' => $actividades,
            'tipo_actividad' => $tipo_actividad,
            'tipo_config' => HistorialActividadesModel::getTipoActividad($tipo_actividad)
        ]);

        return view('historial/por_tipo', $data);
    }

    /**
     * Obtener actividades recientes (AJAX)
     */
    public function recientes($id_paciente)
    {
        $dias = (int)($this->request->getGet('dias') ?? 7);
        $actividades = $this->historialModel->getActividadesRecientes($id_paciente, $dias);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'actividades' => $actividades,
                'dias' => $dias
            ]);
        }

        $paciente = $this->pacientesModel->find($id_paciente);
        
        $navigationData = Navigation::prepareNavigationData('paciente_historial', [
            'subtitle' => "Actividades de los últimos {$dias} días",
            'paciente' => $paciente
        ]);

        $data = array_merge($navigationData, [
            'paciente' => $paciente,
            'actividades' => $actividades,
            'dias' => $dias
        ]);

        return view('historial/recientes', $data);
    }

    /**
     * Obtener estadísticas del historial (AJAX)
     */
    public function estadisticas($id_paciente)
    {
        $fecha_inicio = $this->request->getGet('fecha_inicio');
        $fecha_fin = $this->request->getGet('fecha_fin');
        
        $estadisticas = $this->historialModel->getEstadisticas($id_paciente, $fecha_inicio, $fecha_fin);
        $resumenFinanciero = $this->tratamientosModel->getResumenFinanciero($id_paciente);
        
        return $this->response->setJSON([
            'success' => true,
            'estadisticas' => $estadisticas,
            'resumen_financiero' => $resumenFinanciero
        ]);
    }

    /**
     * Obtener resumen para dashboard del paciente (AJAX)
     */
    public function resumen($id_paciente)
    {
        $resumen = $this->historialModel->getResumenDashboard($id_paciente);
        $tratamientosActivos = $this->tratamientosModel->getTratamientosActivos($id_paciente);
        
        return $this->response->setJSON([
            'success' => true,
            'resumen' => $resumen,
            'tratamientos_activos' => $tratamientosActivos
        ]);
    }

    /**
     * Eliminar una actividad del historial (AJAX)
     */
    public function eliminar($id_actividad)
    {
        // Verificar que es una petición AJAX
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Operación no válida');
        }

        // Obtener actividad para verificar permisos
        $actividad = $this->historialModel->find($id_actividad);
        
        if (!$actividad) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Actividad no encontrada'
            ]);
        }

        // Eliminar actividad (incluyendo adjuntos)
        if ($this->historialModel->eliminarActividad($id_actividad)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Actividad eliminada correctamente'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al eliminar la actividad'
            ]);
        }
    }

    /**
     * Exportar historial a formato específico
     */
    public function exportar($id_paciente, $formato = 'pdf')
    {
        $paciente = $this->pacientesModel->find($id_paciente);
        
        if (!$paciente) {
            return redirect()->to('/pacientes')
                           ->with('error', 'Paciente no encontrado');
        }

        $filtros = $this->getRequestFilters();
        $actividades = $this->historialModel->getActividadesParaExportar(
            $id_paciente, 
            $filtros['fecha_inicio'] ?? null, 
            $filtros['fecha_fin'] ?? null, 
            $filtros['tipo_actividad'] ?? []
        );

        switch (strtolower($formato)) {
            case 'json':
                return $this->exportarJSON($paciente, $actividades);
            case 'csv':
                return $this->exportarCSV($paciente, $actividades);
            case 'pdf':
                return $this->exportarPDF($paciente, $actividades);
            default:
                return redirect()->back()
                               ->with('error', 'Formato de exportación no válido');
        }
    }

    /**
     * Exportar a JSON
     */
    private function exportarJSON(array $paciente, array $actividades)
    {
        $data = [
            'paciente' => [
                'id' => $paciente['id'],
                'nombre' => $paciente['nombre'] . ' ' . $paciente['primer_apellido'],
                'email' => $paciente['email'] ?? null
            ],
            'exportado_en' => date('Y-m-d H:i:s'),
            'total_actividades' => count($actividades),
            'actividades' => $actividades
        ];

        return $this->response
                    ->setHeader('Content-Type', 'application/json')
                    ->setHeader('Content-Disposition', 'attachment; filename="historial_' . $paciente['id'] . '_' . date('Ymd') . '.json"')
                    ->setBody(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Exportar a CSV
     */
    private function exportarCSV(array $paciente, array $actividades)
    {
        $output = fopen('php://temp', 'r+');
        
        // Encabezados
        fputcsv($output, [
            'ID',
            'Fecha',
            'Tipo',
            'Descripción',
            'Médico',
            'Creado'
        ]);
        
        // Datos
        foreach ($actividades as $actividad) {
            fputcsv($output, [
                $actividad['id'],
                $actividad['fecha_actividad'],
                $actividad['tipo_actividad'],
                $actividad['descripcion'] ?? '',
                ($actividad['medico_nombre'] ?? '') . ' ' . ($actividad['medico_apellido'] ?? ''),
                $actividad['created_at'] ?? ''
            ]);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $this->response
                    ->setHeader('Content-Type', 'text/csv; charset=utf-8')
                    ->setHeader('Content-Disposition', 'attachment; filename="historial_' . $paciente['id'] . '_' . date('Ymd') . '.csv"')
                    ->setBody("\xEF\xBB\xBF" . $csv); // UTF-8 BOM para Excel
    }

    /**
     * Exportar a PDF
     */
    private function exportarPDF(array $paciente, array $actividades)
    {
        $html = view('historial/pdf', [
            'paciente' => $paciente,
            'actividades' => $actividades,
            'fecha_exportacion' => date('d/m/Y H:i'),
            'tipos_actividad' => HistorialActividadesModel::$tiposActividad
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'attachment; filename="historial_' . $paciente['id'] . '_' . date('Ymd') . '.pdf"')
                    ->setBody($dompdf->output());
    }

    /**
     * Subir adjunto a una actividad (AJAX)
     */
    public function subirAdjunto($id_actividad)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Operación no válida');
        }

        $actividad = $this->historialModel->find($id_actividad);
        if (!$actividad) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Actividad no encontrada'
            ]);
        }

        $archivo = $this->request->getFile('archivo');
        $descripcion = $this->request->getPost('descripcion');

        if (!$archivo) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No se recibió ningún archivo'
            ]);
        }

        $resultado = $this->adjuntosModel->guardarAdjunto($id_actividad, $archivo, $descripcion);

        return $this->response->setJSON($resultado);
    }

    /**
     * Descargar adjunto
     */
    public function descargarAdjunto($id_adjunto)
    {
        $adjunto = $this->adjuntosModel->find($id_adjunto);
        
        if (!$adjunto) {
            return redirect()->back()->with('error', 'Archivo no encontrado');
        }

        $rutaCompleta = WRITEPATH . $adjunto['ruta_archivo'];
        
        if (!file_exists($rutaCompleta)) {
            return redirect()->back()->with('error', 'El archivo no existe en el servidor');
        }

        return $this->response->download($rutaCompleta, null)
                             ->setFileName($adjunto['nombre_archivo']);
    }

    /**
     * Eliminar adjunto (AJAX)
     */
    public function eliminarAdjunto($id_adjunto)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Operación no válida');
        }

        if ($this->adjuntosModel->eliminarAdjunto($id_adjunto)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Archivo eliminado correctamente'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => 'Error al eliminar el archivo'
        ]);
    }

    /**
     * Obtener filtros de la request
     */
    private function getRequestFilters(): array
    {
        $filtros = [];
        
        $tipo = $this->request->getGet('tipo');
        if ($tipo) {
            $filtros['tipo_actividad'] = is_array($tipo) ? $tipo : [$tipo];
        }
        
        $fecha_inicio = $this->request->getGet('fecha_inicio');
        if ($fecha_inicio) {
            $filtros['fecha_inicio'] = $fecha_inicio . ' 00:00:00';
        }
        
        $fecha_fin = $this->request->getGet('fecha_fin');
        if ($fecha_fin) {
            $filtros['fecha_fin'] = $fecha_fin . ' 23:59:59';
        }
        
        $busqueda = $this->request->getGet('q');
        if ($busqueda) {
            $filtros['busqueda'] = $busqueda;
        }
        
        return $filtros;
    }

    // ========================================
    // MÉTODOS PARA TRATAMIENTOS
    // ========================================

    /**
     * Mostrar tratamientos del paciente
     */
    public function tratamientos($id_paciente)
    {
        $paciente = $this->pacientesModel->find($id_paciente);
        
        if (!$paciente) {
            return redirect()->to('/pacientes')
                           ->with('error', 'Paciente no encontrado');
        }

        $estado = $this->request->getGet('estado');
        $tratamientos = $this->tratamientosModel->getTratamientosByPaciente($id_paciente, $estado);
        $resumenFinanciero = $this->tratamientosModel->getResumenFinanciero($id_paciente);
        $estadisticas = $this->tratamientosModel->getEstadisticas($id_paciente);

        $navigationData = Navigation::prepareNavigationData('paciente_tratamientos', [
            'subtitle' => 'Tratamientos realizados',
            'paciente' => $paciente
        ]);

        $data = array_merge($navigationData, [
            'paciente' => $paciente,
            'tratamientos' => $tratamientos,
            'resumen_financiero' => $resumenFinanciero,
            'estadisticas' => $estadisticas,
            'estados' => TratamientosRealizadosModel::$estados,
            'estado_actual' => $estado
        ]);

        return view('historial/tratamientos', $data);
    }

    /**
     * Ver detalle de un tratamiento
     */
    public function verTratamiento($id_tratamiento)
    {
        $tratamiento = $this->tratamientosModel->getTratamientoConDetalles($id_tratamiento);
        
        if (!$tratamiento) {
            return redirect()->back()->with('error', 'Tratamiento no encontrado');
        }

        $paciente = $this->pacientesModel->find($tratamiento['id_paciente']);

        $navigationData = Navigation::prepareNavigationData('paciente_tratamiento_detalle', [
            'subtitle' => 'Detalle de tratamiento',
            'paciente' => $paciente
        ]);

        $data = array_merge($navigationData, [
            'paciente' => $paciente,
            'tratamiento' => $tratamiento,
            'estado_config' => TratamientosRealizadosModel::getEstado($tratamiento['estado'])
        ]);

        return view('historial/tratamiento_detalle', $data);
    }

    /**
     * Actualizar estado de tratamiento (AJAX)
     */
    public function actualizarEstadoTratamiento($id_tratamiento)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Operación no válida');
        }

        $nuevo_estado = $this->request->getPost('estado');
        $fecha_fin = $this->request->getPost('fecha_fin');

        $estados_validos = array_keys(TratamientosRealizadosModel::$estados);
        if (!in_array($nuevo_estado, $estados_validos)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Estado no válido'
            ]);
        }

        if ($this->tratamientosModel->actualizarEstado($id_tratamiento, $nuevo_estado, $fecha_fin)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'estado' => TratamientosRealizadosModel::getEstado($nuevo_estado)
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => 'Error al actualizar el estado'
        ]);
    }

    /**
     * Registrar pago en tratamiento (AJAX)
     */
    public function registrarPagoTratamiento($id_tratamiento)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Operación no válida');
        }

        $monto = (float)$this->request->getPost('monto');

        if ($monto <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'El monto debe ser mayor que 0'
            ]);
        }

        if ($this->tratamientosModel->registrarPago($id_tratamiento, $monto)) {
            $tratamiento = $this->tratamientosModel->find($id_tratamiento);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'nuevo_pagado' => $tratamiento['pagado'],
                'saldo' => (float)$tratamiento['costo'] - (float)$tratamiento['pagado']
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => 'Error al registrar el pago'
        ]);
    }
}
