<?php

namespace App\Controllers;

use App\Models\OdontogramaModel;
use App\Models\OdontogramaDienteModel;
use App\Models\OdontogramaHistorialModel;
use App\Models\PacientesModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador para gestionar el odontograma dental
 * 
 * Implementa el Sistema FDI (Federación Dental Internacional):
 * - Adultos: Cuadrantes 1-4 (18-11, 21-28, 31-38, 41-48)
 * - Infantiles: Cuadrantes 5-8 (55-51, 61-65, 75-71, 81-85)
 */
class OdontogramaController extends BaseController
{
    protected OdontogramaModel $odontogramaModel;
    protected OdontogramaDienteModel $dienteModel;
    protected OdontogramaHistorialModel $historialModel;
    protected PacientesModel $pacientesModel;

    public function __construct()
    {
        $this->odontogramaModel = new OdontogramaModel();
        $this->dienteModel = new OdontogramaDienteModel();
        $this->historialModel = new OdontogramaHistorialModel();
        $this->pacientesModel = new PacientesModel();
    }

    /**
     * Muestra el odontograma de un paciente
     */
    public function index(int $idPaciente)
    {
        $paciente = $this->pacientesModel->find($idPaciente);
        
        if (!$paciente) {
            return redirect()->to('/pacientes')->with('error', 'Paciente no encontrado');
        }

        $odontogramaData = $this->odontogramaModel->getOdontogramaCompleto($idPaciente);
        $estados = $this->odontogramaModel->getEstadosDisponibles();
        $colores = $this->odontogramaModel->getColoresEstados();
        $historial = $this->historialModel->getHistorialOdontograma($odontogramaData['odontograma']['id'], 10);
        $historialFormateado = $this->historialModel->formatearHistorial($historial);
        $resumen = $this->dienteModel->getResumenEstados($odontogramaData['odontograma']['id']);

        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('odontograma', [
            'subtitle' => 'Odontograma de ' . $paciente['nombre']
        ]);

        $data = array_merge($navigationData, [
            'paciente' => $paciente,
            'odontograma' => $odontogramaData['odontograma'],
            'dientes' => $odontogramaData['dientes'],
            'estructuraAdultos' => $odontogramaData['estructura_adultos'],
            'estructuraInfantiles' => $odontogramaData['estructura_infantiles'],
            'superficies' => $odontogramaData['superficies'],
            'estados' => $estados,
            'colores' => $colores,
            'historial' => $historialFormateado,
            'resumen' => $resumen,
            'estadosDiente' => OdontogramaDienteModel::$estadosDiente,
            'condicionesEspeciales' => OdontogramaDienteModel::$condicionesEspeciales
        ]);

        return view('odontograma/index', $data);
    }

    /**
     * API: Obtiene el odontograma completo de un paciente
     */
    public function getOdontograma(int $idPaciente): ResponseInterface
    {
        try {
            $paciente = $this->pacientesModel->find($idPaciente);
            
            if (!$paciente) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ])->setStatusCode(404);
            }

            $odontogramaData = $this->odontogramaModel->getOdontogramaCompleto($idPaciente);
            $colores = $this->odontogramaModel->getColoresEstados();
            $resumen = $this->dienteModel->getResumenEstados($odontogramaData['odontograma']['id']);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'odontograma' => $odontogramaData['odontograma'],
                    'dientes' => $odontogramaData['dientes'],
                    'colores' => $colores,
                    'resumen' => $resumen
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo odontograma: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener el odontograma'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Obtiene información de un diente específico
     */
    public function getDiente(int $idPaciente, int $numeroDiente): ResponseInterface
    {
        try {
            $odontograma = $this->odontogramaModel->getOdontogramaPaciente($idPaciente);
            
            if (!$odontograma) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Odontograma no encontrado'
                ])->setStatusCode(404);
            }

            $diente = $this->dienteModel->getOrCreateDiente($odontograma['id'], $numeroDiente);
            $historial = $this->historialModel->getHistorialDiente($odontograma['id'], $numeroDiente, 10);
            $historialFormateado = $this->historialModel->formatearHistorial($historial);
            $nombreDiente = $this->odontogramaModel->getNombreDiente($numeroDiente);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'diente' => $diente,
                    'nombre' => $nombreDiente,
                    'historial' => $historialFormateado,
                    'estadosDiente' => OdontogramaDienteModel::$estadosDiente,
                    'estadosSuperficie' => OdontogramaDienteModel::$estadosSuperficie
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo diente: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener información del diente'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Actualiza una superficie de un diente
     */
    public function actualizarSuperficie(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición no válida'
            ])->setStatusCode(403);
        }

        $idPaciente = $this->request->getPost('id_paciente');
        $numeroDiente = $this->request->getPost('numero_diente');
        $superficie = $this->request->getPost('superficie');
        $estado = $this->request->getPost('estado');

        // Validaciones
        if (!$idPaciente || !$numeroDiente || !$superficie || !$estado) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos requeridos'
            ])->setStatusCode(400);
        }

        try {
            $odontograma = $this->odontogramaModel->getOrCreateOdontograma($idPaciente);
            $idUsuario = session()->get('id') ?? 1;

            $result = $this->dienteModel->actualizarSuperficie(
                $odontograma['id'],
                $numeroDiente,
                'sup_' . $superficie,
                $estado,
                $idUsuario
            );

            if ($result) {
                $colores = $this->odontogramaModel->getColoresEstados();
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Superficie actualizada correctamente',
                    'data' => [
                        'numero_diente' => $numeroDiente,
                        'superficie' => $superficie,
                        'estado' => $estado,
                        'color' => $colores[$estado]['color'] ?? '#FFFFFF'
                    ]
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se pudo actualizar la superficie'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error actualizando superficie: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar la superficie'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Actualiza el estado general de un diente
     */
    public function actualizarEstadoDiente(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición no válida'
            ])->setStatusCode(403);
        }

        $idPaciente = $this->request->getPost('id_paciente');
        $numeroDiente = $this->request->getPost('numero_diente');
        $estado = $this->request->getPost('estado');

        if (!$idPaciente || !$numeroDiente || !$estado) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos requeridos'
            ])->setStatusCode(400);
        }

        try {
            $odontograma = $this->odontogramaModel->getOrCreateOdontograma($idPaciente);
            $idUsuario = session()->get('id') ?? 1;

            $result = $this->dienteModel->actualizarEstadoDiente(
                $odontograma['id'],
                $numeroDiente,
                $estado,
                $idUsuario
            );

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Estado del diente actualizado correctamente',
                    'data' => [
                        'numero_diente' => $numeroDiente,
                        'estado' => $estado
                    ]
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se pudo actualizar el estado del diente'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error actualizando estado del diente: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el estado del diente'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Actualiza un diente completo
     */
    public function actualizarDiente(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición no válida'
            ])->setStatusCode(403);
        }

        $idPaciente = $this->request->getPost('id_paciente');
        $numeroDiente = $this->request->getPost('numero_diente');
        $datos = $this->request->getPost('datos');

        if (!$idPaciente || !$numeroDiente || !$datos) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos requeridos'
            ])->setStatusCode(400);
        }

        try {
            $odontograma = $this->odontogramaModel->getOrCreateOdontograma($idPaciente);
            $idUsuario = session()->get('id') ?? 1;

            // Si datos es string JSON, decodificarlo
            if (is_string($datos)) {
                $datos = json_decode($datos, true);
            }

            $result = $this->dienteModel->actualizarDienteCompleto(
                $odontograma['id'],
                $numeroDiente,
                $datos,
                $idUsuario
            );

            if ($result) {
                $diente = $this->dienteModel->getOrCreateDiente($odontograma['id'], $numeroDiente);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Diente actualizado correctamente',
                    'data' => $diente
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se pudo actualizar el diente'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error actualizando diente: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el diente'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Obtiene el historial de cambios
     */
    public function getHistorial(int $idPaciente): ResponseInterface
    {
        try {
            $odontograma = $this->odontogramaModel->getOdontogramaPaciente($idPaciente);
            
            if (!$odontograma) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Odontograma no encontrado'
                ])->setStatusCode(404);
            }

            $limite = $this->request->getGet('limite') ?? 50;
            $historial = $this->historialModel->getHistorialOdontograma($odontograma['id'], $limite);
            $historialFormateado = $this->historialModel->formatearHistorial($historial);

            return $this->response->setJSON([
                'success' => true,
                'data' => $historialFormateado
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo historial: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener el historial'
            ])->setStatusCode(500);
        }
    }

    /**
     * Vista del historial completo
     */
    public function historial(int $idPaciente)
    {
        $paciente = $this->pacientesModel->find($idPaciente);
        
        if (!$paciente) {
            return redirect()->to('/pacientes')->with('error', 'Paciente no encontrado');
        }

        $odontograma = $this->odontogramaModel->getOdontogramaPaciente($idPaciente);
        
        if (!$odontograma) {
            return redirect()->to("/odontograma/{$idPaciente}")->with('info', 'No hay historial disponible');
        }

        $historial = $this->historialModel->getHistorialOdontograma($odontograma['id'], 100);
        $historialFormateado = $this->historialModel->formatearHistorial($historial);
        $estadisticas = $this->historialModel->getEstadisticas($odontograma['id']);
        $fechasDisponibles = $this->historialModel->getFechasDisponibles($odontograma['id']);

        $navigationData = Navigation::prepareNavigationData('odontograma_historial', [
            'subtitle' => 'Historial de Odontograma'
        ]);

        $data = array_merge($navigationData, [
            'paciente' => $paciente,
            'odontograma' => $odontograma,
            'historial' => $historialFormateado,
            'estadisticas' => $estadisticas,
            'fechasDisponibles' => $fechasDisponibles
        ]);

        return view('odontograma/historial', $data);
    }

    /**
     * API: Obtiene los estados disponibles del catálogo
     */
    public function getEstados(): ResponseInterface
    {
        try {
            $estados = $this->odontogramaModel->getEstadosDisponibles();
            $colores = $this->odontogramaModel->getColoresEstados();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'estados' => $estados,
                    'colores' => $colores,
                    'estadosDiente' => OdontogramaDienteModel::$estadosDiente
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo estados: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener los estados'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Obtiene el resumen del odontograma
     */
    public function getResumen(int $idPaciente): ResponseInterface
    {
        try {
            $odontograma = $this->odontogramaModel->getOdontogramaPaciente($idPaciente);
            
            if (!$odontograma) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Odontograma no encontrado'
                ])->setStatusCode(404);
            }

            $resumen = $this->dienteModel->getResumenEstados($odontograma['id']);

            return $this->response->setJSON([
                'success' => true,
                'data' => $resumen
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo resumen: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener el resumen'
            ])->setStatusCode(500);
        }
    }

    /**
     * Obtiene datos del odontograma para el tab en historial clínica
     * (Para uso en AJAX desde historial_clinica_form.php)
     */
    public function getOdontogramaTab(int $idPaciente): ResponseInterface
    {
        try {
            $odontogramaData = $this->odontogramaModel->getOdontogramaCompleto($idPaciente);
            $colores = $this->odontogramaModel->getColoresEstados();
            $estados = $this->odontogramaModel->getEstadosDisponibles();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'odontograma' => $odontogramaData['odontograma'],
                    'dientes' => $odontogramaData['dientes'],
                    'colores' => $colores,
                    'estados' => $estados,
                    'estadosDiente' => OdontogramaDienteModel::$estadosDiente
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo datos para tab: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener datos del odontograma'
            ])->setStatusCode(500);
        }
    }
}
