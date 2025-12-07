<?php

namespace App\Controllers;

use App\Models\PacientesModel;
use App\Models\MedicamentosModel;
use App\Controllers\Navigation;

class Recetas extends BaseController
{
    public function index() {
        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('recetas', [
            'subtitle' => 'Historial de recetas médicas'
        ]);
        
        $data = array_merge($navigationData, [
            'recetas' => $this->getRecetasData()
        ]);
        
        return view('recetas/index', $data);
    }
    
    public function nueva() {
        // Preparar navegación para nueva receta
        $navigationData = Navigation::prepareNavigationData('receta_nueva', [
            'subtitle' => 'Emitir nueva receta médica'
        ]);
        
        $data = array_merge($navigationData, [
            'formAction' => base_url('/recetas/guardar'),
            'pacientes' => $this->getPacientesDisponibles(),
            'medicamentos' => $this->getMedicamentosDisponibles()
        ]);
        
        return view('recetas/form', $data);
    }

    private function getRecetasData(): array {
        $db = \Config\Database::connect();
        
        // Obtener recetas con información del paciente y médico
        $recetas = $db->table('recetas')
            ->select('recetas.*, 
                      pacientes.nombre as paciente_nombre, 
                      pacientes.primer_apellido as paciente_apellido,
                      usuarios.nombre as medico_nombre')
            ->join('pacientes', 'pacientes.id = recetas.id_paciente', 'left')
            ->join('usuarios', 'usuarios.id = recetas.id_usuario', 'left')
            ->where('recetas.deleted_at IS NULL')
            ->orderBy('recetas.fecha', 'DESC')
            ->get()
            ->getResultArray();
        
        // Agregar conteo de medicamentos para cada receta
        foreach ($recetas as &$receta) {
            $count = $db->table('recetas_detalles')
                ->where('id_receta', $receta['id'])
                ->countAllResults();
            $receta['total_medicamentos'] = $count;
        }
        
        return $recetas;
    }
    
    private function getPacientesDisponibles(): array {
        $pacientesModel = new PacientesModel();
        return $pacientesModel->findAll();
    }
    
    private function getMedicamentosDisponibles(): array {
        $medicamentosModel = new MedicamentosModel();
        return $medicamentosModel->findAll();
    }

    public function crear($id_paciente)
    {
        $pacientesModel = new PacientesModel();
        $medicamentosModel = new MedicamentosModel();

        $paciente = $pacientesModel->find($id_paciente);
        if (!$paciente) {
            return redirect()->to('/pacientes')->with('error', 'Paciente no encontrado');
        }

        $data = [
            'paciente' => $paciente,
            'medicamentos' => $medicamentosModel->findAll()
        ];

        return view('recetas/crear', $data);
    }

    public function guardar()
    {
        try {
            $db = \Config\Database::connect();
            $recetasModel = new \App\Models\RecetasModel();
            $detallesModel = new \App\Models\RecetasDetallesModel();

            $data = $this->request->getPost();
            $id_usuario = session()->get('id'); // Del usuario logueado

            // Validar que el usuario esté logueado
            if (!$id_usuario) {
                return $this->response->setJSON(['success' => false, 'message' => 'Usuario no autenticado']);
            }

            // Verificar que el usuario exista en la base de datos
            $usuariosModel = new \App\Models\UsuariosModel();
            $usuario = $usuariosModel->find($id_usuario);
            
            if (!$usuario) {
                log_message('warning', 'Usuario en sesión no existe en BD. ID: ' . $id_usuario);
                // Usar NULL para id_usuario si no existe (requiere que la columna sea nullable)
                $id_usuario = null;
            }

            // Log para debugging
            log_message('info', 'Guardando receta - Usuario: ' . ($id_usuario ?? 'NULL') . ', Paciente: ' . ($data['id_paciente'] ?? 'N/A'));

            $db->transStart();

            // 1. Guardar Cabecera
            $folio = 'REC-' . time(); // Generación simple de folio
            $recetaData = [
                'id_paciente' => $data['id_paciente'],
                'id_usuario' => $id_usuario,
                'folio' => $folio,
                'fecha' => date('Y-m-d H:i:s'),
                'notas_adicionales' => $data['notas_adicionales'] ?? ''
            ];

            log_message('info', 'Datos de receta: ' . json_encode($recetaData));

            $recetaId = $recetasModel->insert($recetaData);

            if (!$recetaId) {
                $error = $recetasModel->errors();
                log_message('error', 'Error al insertar receta: ' . json_encode($error));
                throw new \Exception('Error al guardar la receta: ' . json_encode($error));
            }

            // 2. Guardar Detalles
            if (isset($data['medicamentos']) && is_array($data['medicamentos'])) {
                for ($i = 0; $i < count($data['medicamentos']); $i++) {
                    $detalleData = [
                        'id_receta' => $recetaId,
                        'id_medicamento' => $data['medicamentos'][$i],
                        'dosis' => $data['dosis'][$i],
                        'duracion' => $data['duracion'][$i],
                        'cantidad' => $data['cantidad'][$i]
                    ];

                    log_message('info', 'Insertando detalle: ' . json_encode($detalleData));

                    $detalleId = $detallesModel->insert($detalleData);
                    
                    if (!$detalleId) {
                        $error = $detallesModel->errors();
                        log_message('error', 'Error al insertar detalle: ' . json_encode($error));
                        throw new \Exception('Error al guardar detalle de medicamento: ' . json_encode($error));
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'Transacción fallida');
                return $this->response->setJSON(['success' => false, 'message' => 'Error en la transacción']);
            }

            // Registrar actividad en el historial del paciente
            $historialModel = new \App\Models\HistorialActividadesModel();
            $historialModel->registrarActividad(
                $data['id_paciente'],           // id_paciente
                'receta',                        // tipo_actividad
                $recetaId,                      // id_referencia
                'Receta emitida - Folio: ' . $folio  // descripción
            );

            log_message('info', 'Receta guardada exitosamente - ID: ' . $recetaId);
            return $this->response->setJSON(['success' => true, 'id_receta' => $recetaId]);

        } catch (\Exception $e) {
            log_message('error', 'Excepción al guardar receta: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Ver detalles de una receta
     */
    public function ver($id)
    {
        $recetasModel = new \App\Models\RecetasModel();
        $pacientesModel = new \App\Models\PacientesModel();
        $usuariosModel = new \App\Models\UsuariosModel();

        $receta = $recetasModel->find($id);
        
        if (!$receta) {
            return redirect()->to('/recetas')->with('error', 'Receta no encontrada');
        }

        $paciente = $pacientesModel->find($receta['id_paciente']);
        $medico = $usuariosModel->find($receta['id_usuario']);
        
        // Obtener detalles con nombres de medicamentos
        $db = \Config\Database::connect();
        $detalles = $db->table('recetas_detalles')
            ->select('recetas_detalles.*, medicamentos.nombre_comercial, medicamentos.sustancia_activa')
            ->join('medicamentos', 'medicamentos.id = recetas_detalles.id_medicamento')
            ->where('id_receta', $id)
            ->get()->getResultArray();

        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('receta_detalle', [
            'subtitle' => 'Detalles de receta - Folio: ' . $receta['folio']
        ]);

        $data = array_merge($navigationData, [
            'receta' => $receta,
            'paciente' => $paciente,
            'medico' => $medico,
            'detalles' => $detalles
        ]);

        return view('recetas/ver', $data);
    }

    public function imprimir($id)
    {
        $recetasModel = new \App\Models\RecetasModel();
        $detallesModel = new \App\Models\RecetasDetallesModel();
        $pacientesModel = new \App\Models\PacientesModel();
        $medicamentosModel = new \App\Models\MedicamentosModel();
        $usuariosModel = new \App\Models\UsuariosModel();

        $receta = $recetasModel->find($id);
        $paciente = $pacientesModel->find($receta['id_paciente']);
        $medico = $usuariosModel->find($receta['id_usuario']);
        
        // Obtener detalles con nombres de medicamentos (Join manual o consulta)
        $db = \Config\Database::connect();
        $detalles = $db->table('recetas_detalles')
            ->select('recetas_detalles.*, medicamentos.nombre_comercial, medicamentos.sustancia_activa')
            ->join('medicamentos', 'medicamentos.id = recetas_detalles.id_medicamento')
            ->where('id_receta', $id)
            ->get()->getResultArray();

        $html = view('recetas/pdf_template', [
            'receta' => $receta,
            'paciente' => $paciente,
            'medico' => $medico,
            'detalles' => $detalles
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Receta-{$receta['folio']}.pdf", ["Attachment" => false]);
    }
}
