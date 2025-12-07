<?php

namespace App\Controllers;

use App\Models\PacientesModel;
use App\Models\ServiciosModel;
use App\Models\CotizacionesModel;
use App\Controllers\Navigation;

class Cotizaciones extends BaseController
{
    /**
     * Lista de cotizaciones/presupuestos
     */
    public function index()
    {
        $navigationData = Navigation::prepareNavigationData('cotizaciones', [
            'subtitle' => 'Gestión de presupuestos'
        ]);
        
        // Obtener cotizaciones reales de la base de datos
        $cotizaciones = $this->getCotizaciones();
        
        $data = array_merge($navigationData, [
            'cotizaciones' => $cotizaciones
        ]);
        
        return view('cotizaciones/index', $data);
    }
    
    /**
     * Obtener cotizaciones con información del paciente
     */
    private function getCotizaciones(): array {
        $cotizaciones = [];
        
        try {
            $db = \Config\Database::connect();
            
            if ($db->tableExists('cotizaciones')) {
                // Intentar con tabla 'pacientes' primero, luego con 'patients'
                $pacientesTable = $db->tableExists('pacientes') ? 'pacientes' : 'patients';
                $nombreField = $pacientesTable === 'pacientes' ? 'nombre' : 'nombre';
                $apellidoField = $pacientesTable === 'pacientes' ? "CONCAT(p.primer_apellido, ' ', COALESCE(p.segundo_apellido, ''))" : 'p.apellido';
                
                if ($pacientesTable === 'pacientes') {
                    $query = $db->table('cotizaciones c')
                        ->select("c.*, p.nombre as paciente_nombre, CONCAT(p.primer_apellido, ' ', COALESCE(p.segundo_apellido, '')) as paciente_apellido")
                        ->join('pacientes p', 'p.id = c.id_paciente', 'left')
                        ->orderBy('c.fecha_emision', 'DESC')
                        ->get();
                } else {
                    $query = $db->table('cotizaciones c')
                        ->select('c.*, p.nombre as paciente_nombre, CONCAT(p.primer_apellido, \' \', COALESCE(p.segundo_apellido, \'\')) as paciente_apellido')
                        ->join('pacientes p', 'p.id = c.id_paciente', 'left')
                        ->orderBy('c.fecha_emision', 'DESC')
                        ->get();
                }
                
                $cotizaciones = $query->getResultArray();
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener cotizaciones: ' . $e->getMessage());
        }
        
        return $cotizaciones;
    }
    
    public function crear($id_paciente)
    {
        $pacientesModel = new PacientesModel();
        $serviciosModel = new ServiciosModel();

        $paciente = $pacientesModel->find($id_paciente);
        if (!$paciente) {
            return redirect()->to('/pacientes')->with('error', 'Paciente no encontrado');
        }

        $data = [
            'paciente' => $paciente,
            'servicios' => $serviciosModel->findAll()
        ];

        return view('cotizaciones/crear', $data);
    }
    public function guardar()
    {
        $db = \Config\Database::connect();
        $cotModel = new \App\Models\CotizacionesModel();
        $detModel = new \App\Models\CotizacionesDetallesModel();

        $data = $this->request->getPost();
        $id_usuario = session()->get('id');

        $db->transStart();

        // Calcular total backend por seguridad
        $totalGlobal = 0;
        if (isset($data['servicios'])) {
            for ($i = 0; $i < count($data['servicios']); $i++) {
                $totalGlobal += ($data['precios'][$i] * $data['cantidades'][$i]);
            }
        }

        $cotId = $cotModel->insert([
            'id_paciente' => $data['id_paciente'],
            'id_usuario' => $id_usuario,
            'fecha_emision' => date('Y-m-d H:i:s'),
            'fecha_vigencia' => $data['fecha_vigencia'],
            'total' => $totalGlobal,
            'estado' => 'pendiente',
            'observaciones' => $data['observaciones']
        ]);

        if (isset($data['servicios'])) {
            for ($i = 0; $i < count($data['servicios']); $i++) {
                $subtotal = $data['precios'][$i] * $data['cantidades'][$i];
                $detModel->insert([
                    'id_cotizacion' => $cotId,
                    'id_servicio' => $data['servicios'][$i],
                    'cantidad' => $data['cantidades'][$i],
                    'precio_unitario' => $data['precios'][$i],
                    'subtotal' => $subtotal
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false]);
        }

        // Registrar actividad en historial
        try {
            $historialModel = new \App\Models\HistorialActividadesModel();
            $historialModel->registrarActividad(
                $data['id_paciente'],
                'cotizacion',
                $cotId,
                'Nueva cotización creada por $' . number_format($totalGlobal, 2)
            );
        } catch (\Exception $e) {
            log_message('error', 'Error al registrar historial de cotización: ' . $e->getMessage());
        }

        return $this->response->setJSON(['success' => true, 'id_cotizacion' => $cotId]);
    }

    /**
     * Ver detalle de una cotización
     */
    public function ver($id)
    {
        $cotModel = new \App\Models\CotizacionesModel();
        $pacModel = new \App\Models\PacientesModel();
        $userModel = new \App\Models\UsuariosModel();
        
        $cotizacion = $cotModel->find($id);
        
        if (!$cotizacion) {
            return redirect()->to('/cotizaciones')->with('error', 'Cotización no encontrada');
        }
        
        $paciente = $pacModel->find($cotizacion['id_paciente']);
        $medico = $userModel->find($cotizacion['id_usuario']);

        $db = \Config\Database::connect();
        $detalles = $db->table('cotizaciones_detalles')
            ->select('cotizaciones_detalles.*, servicios.nombre')
            ->join('servicios', 'servicios.id = cotizaciones_detalles.id_servicio')
            ->where('id_cotizacion', $id)
            ->get()->getResultArray();

        $navigationData = Navigation::prepareNavigationData('cotizaciones', [
            'subtitle' => 'Detalle de cotización'
        ]);

        $data = array_merge($navigationData, [
            'cotizacion' => $cotizacion,
            'paciente' => $paciente,
            'medico' => $medico,
            'detalles' => $detalles
        ]);

        return view('cotizaciones/ver', $data);
    }

    /**
     * Vista para seleccionar paciente antes de crear cotización
     */
    public function nueva()
    {
        $pacientesModel = new PacientesModel();
        
        $navigationData = Navigation::prepareNavigationData('cotizaciones', [
            'subtitle' => 'Nueva cotización'
        ]);
        
        $data = array_merge($navigationData, [
            'pacientes' => $pacientesModel->findAll()
        ]);
        
        return view('cotizaciones/form', $data);
    }

    public function imprimir($id)
    {
        $cotModel = new \App\Models\CotizacionesModel();
        $pacModel = new \App\Models\PacientesModel();
        $userModel = new \App\Models\UsuariosModel();
        
        $cotizacion = $cotModel->find($id);
        $paciente = $pacModel->find($cotizacion['id_paciente']);
        $medico = $userModel->find($cotizacion['id_usuario']);

        $db = \Config\Database::connect();
        $detalles = $db->table('cotizaciones_detalles')
            ->select('cotizaciones_detalles.*, servicios.nombre')
            ->join('servicios', 'servicios.id = cotizaciones_detalles.id_servicio')
            ->where('id_cotizacion', $id)
            ->get()->getResultArray();

        $html = view('cotizaciones/pdf_template', [
            'cotizacion' => $cotizacion,
            'paciente' => $paciente,
            'medico' => $medico,
            'detalles' => $detalles
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Cotizacion-{$id}.pdf", ["Attachment" => false]);
    }
}
