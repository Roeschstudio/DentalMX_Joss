<?php

namespace App\Controllers;

use App\Libraries\StringValidator;
use App\Libraries\DentalLogger;
use App\Models\Patient;
use App\Models\PacientesModel;
use App\Models\DatosGeneralesModel;
use App\Models\AntecedentesFamiliaresModel;
use App\Models\AntecedentesPatologicosModel;
use App\Models\HistorialBucodentalModel;
use App\Models\NotasEvolucionModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\I18n\Time;

class Pacientes extends BaseController
{
    protected $patientModel;
    protected $userModel;
    protected $generalDataModel;
    protected $AFModel;
    protected $APModel;
    protected $HBModel;
    protected $NEModel;
    protected $dentalLogger;

    public function __construct()
    {
        $this->patientModel = new Patient();
        $this->userModel = new PacientesModel(); // Keep for backward compatibility
        $this->generalDataModel = new DatosGeneralesModel();
        $this->AFModel = new AntecedentesFamiliaresModel();
        $this->APModel = new AntecedentesPatologicosModel();
        $this->HBModel = new HistorialBucodentalModel();
        $this->NEModel = new NotasEvolucionModel();
    }

    /**
     * Get logger instance (lazy initialization)
     */
    private function getLogger()
    {
        if ($this->dentalLogger === null) {
            $this->dentalLogger = new DentalLogger();
        }
        return $this->dentalLogger;
    }

    public function getAll()
    {
        try {
            log_message('info', 'Obteniendo lista de pacientes');
            
            $data["data"] = $this->patientModel->getAllPatients();
            
            log_message('info', 'Pacientes obtenidos exitosamente: ' . count($data["data"]));
            
            return $this->response
                ->setStatusCode(200)
                ->setJSON($data);
                
        } catch (DatabaseException $e) {
            log_message('error', 'Error de base de datos obteniendo pacientes: ' . $e->getMessage());
            
            return $this->responderJson(500, true, 'Error de base de datos al obtener pacientes');
            
        } catch (\Exception $e) {
            log_message('critical', 'Error inesperado obteniendo pacientes: ' . $e->getMessage() . 
                      ' en ' . $e->getFile() . ':' . $e->getLine());
            
            return $this->responderJson(500, true, 'Error interno del servidor');
        }
    }    

    public function guardar()
    {
        $method = $this->request->getMethod();
        
        if ($method !== "POST" && $method !== "PUT") {
            return $this->responderJson(405, true, 'Método no permitido');
        }

        try {
            log_message('info', 'Iniciando guardado de paciente');
            
            $datos = $this->request->getJSON(true);
            
            // Validar estructura de datos
            if (empty($datos) || !is_array($datos)) {
                return $this->responderJson(400, true, 'Datos inválidos o incompletos');
            }
            
            // Validaciones básicas
            if (!isset($datos['nombre']) || !isset($datos['primer_apellido']) || 
                !isset($datos['celular']) || !isset($datos['domicilio'])) {
                
                return $this->responderJson(400, true, 'Faltan campos obligatorios');
            }
            
            // Validaciones con StringValidator
            if (!StringValidator::isValidString($datos['nombre']) ||
                !StringValidator::isValidString($datos['primer_apellido']) ||
                !StringValidator::isValidString($datos['celular']) ||
                !StringValidator::isValidString($datos['domicilio'])) {
                
                return $this->responderJson(400, true, 'Parámetros inválidos');
            }
            
            // Sanitizar datos
            $datosSanitizados = [
                'nombre' => trim(strip_tags($datos['nombre'])),
                'primer_apellido' => trim(strip_tags($datos['primer_apellido'])),
                'segundo_apellido' => isset($datos['segundo_apellido']) ? trim(strip_tags($datos['segundo_apellido'])) : null,
                'email' => isset($datos['email']) ? filter_var(trim($datos['email']), FILTER_SANITIZE_EMAIL) : null,
                'celular' => preg_replace('/[^0-9]/', '', $datos['celular']),
                'domicilio' => trim(strip_tags($datos['domicilio'])),
                'nacionalidad' => isset($datos['nacionalidad']) ? trim(strip_tags($datos['nacionalidad'])) : null
            ];
            
            // Validar email si se proporciona
            if (!empty($datosSanitizados['email']) && !filter_var($datosSanitizados['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->responderJson(400, true, 'El correo electrónico no es válido');
            }
            
            // Validar teléfono
            if (strlen($datosSanitizados['celular']) !== 10) {
                return $this->responderJson(400, true, 'El teléfono debe tener 10 dígitos');
            }
            
            // Determinar si es creación o edición
            $esEdicion = isset($datos['id']) && !empty($datos['id']);
            $pacienteId = $datos['id'] ?? null;
            
            $result = $this->patientModel->saveDataWithMapping($datosSanitizados);

            if ($result['success']) {
                // Registrar operación CRUD
                $accion = $esEdicion ? 'ACTUALIZO' : 'CREO';
                $this->getLogger()->crud($accion, 'paciente', $result['id'] ?? $pacienteId, $datosSanitizados);
                
                // Registrar auditoría
                $this->getLogger()->audit(
                    "PACIENTE_{$accion}",
                    'pacientes',
                    $result['id'] ?? $pacienteId,
                    $datosSanitizados
                );
                
                $this->getLogger()->app("Paciente {$accion} exitosamente");
                
                return $this->responderJson(200, false, $result['message'], $result);
            } else {
                $this->getLogger()->app("Error guardando paciente: " . $result['message'], 'warning');
                return $this->responderJson(400, true, $result['message']);
            }
            
        } catch (DatabaseException $e) {
            $this->getLogger()->database($e->getMessage());
            
            // Verificar si es error de duplicidad
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                $this->getLogger()->security('INTENTO_DUPLICIDAD_PACIENTE', $datos ?? []);
                return $this->responderJson(409, true, 'El paciente ya existe o hay datos duplicados');
            }
            
            return $this->responderJson(500, true, 'Error de base de datos al guardar paciente');
            
        } catch (\Exception $e) {
            $this->getLogger()->exception($e, 'guardando paciente');
            return $this->responderJson(500, true, 'Error interno del servidor al guardar paciente');
        }
    }

    public function borrar()
    {
        $method = $this->request->getMethod();
        
        if ($method !== "DELETE") {
            return $this->responderJson(405, true, 'Método no permitido');
        }

        try {
            log_message('info', 'Iniciando eliminación de paciente');
            
            $datos = $this->request->getJSON(true);

            if (!isset($datos['id']) || empty($datos['id'])) {
                return $this->responderJson(400, true, 'ID de paciente no proporcionado');
            }
            
            // Validar que el ID sea numérico
            if (!is_numeric($datos['id'])) {
                return $this->responderJson(400, true, 'ID de paciente inválido');
            }
            
            // Verificar que el paciente existe antes de eliminar
            $paciente = $this->patientModel->find($datos['id']);
            if (!$paciente) {
                $this->getLogger()->security('INTENTO_ELIMINAR_PACIENTE_INEXISTENTE', $datos);
                return $this->responderJson(404, true, 'Paciente no encontrado');
            }
            
            if ($this->patientModel->deletePatient($datos['id'])['success']) {
                // Registrar eliminación
                $this->getLogger()->crud('ELIMINO', 'paciente', $datos['id'], $paciente);
                $this->getLogger()->audit('PACIENTE_ELIMINADO', 'pacientes', $datos['id'], $paciente);
                
                return $this->responderJson(200, false, 'Paciente eliminado exitosamente');
            } else {
                $this->getLogger()->app('Error eliminando paciente: ID ' . $datos['id'], 'warning');
                
                return $this->responderJson(400, true, 'Ocurrió un error al eliminar paciente');
            }
            
        } catch (DatabaseException $e) {
            $this->getLogger()->database($e->getMessage());
            
            // Verificar si es error de restricción de clave foránea
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                $this->getLogger()->security('INTENTO_ELIMINAR_PACIENTE_CON_RELACIONES', $datos ?? []);
                return $this->responderJson(409, true, 'No se puede eliminar el paciente porque tiene registros asociados');
            }
            
            return $this->responderJson(500, true, 'Error de base de datos al eliminar paciente');
            
        } catch (\Exception $e) {
            $this->getLogger()->exception($e, 'eliminando paciente');
            return $this->responderJson(500, true, 'Error interno del servidor al eliminar paciente');
        }
    }

    // Método mejorado para guardar datos generales
    public function guardarDatosGenerales()
    {
        $method = $this->request->getMethod();
        
        if ($method !== "POST") {
            return $this->responderJson(405, true, 'Método no permitido');
        }

        try {
            log_message('info', 'Iniciando guardado de datos generales');
            
            $datos = $this->request->getJSON(true);
            
            if (empty($datos) || !is_array($datos)) {
                return $this->responderJson(400, true, 'Datos inválidos o incompletos');
            }
            
            $result = $this->generalDataModel->saveData($datos);

            if ($result['success']) {
                log_message('info', 'Datos generales guardados exitosamente');
                return $this->responderJson(200, false, $result['message'], ['id' => $result['id']]);
            } else {
                log_message('warning', 'Error guardando datos generales: ' . $result['message']);
                return $this->responderJson(400, true, $result['message']);
            }
            
        } catch (DatabaseException $e) {
            log_message('error', 'Error de base de datos guardando datos generales: ' . $e->getMessage());
            return $this->responderJson(500, true, 'Error de base de datos al guardar datos generales');
            
        } catch (\Exception $e) {
            log_message('critical', 'Error inesperado guardando datos generales: ' . $e->getMessage());
            return $this->responderJson(500, true, 'Error interno del servidor al guardar datos generales');
        }
    }

    // Método mejorado para obtener notas de evolución
    public function getAllNotasEvolucionByPaciente($id_paciente)
    {
        try {
            log_message('info', 'Obteniendo notas de evolución del paciente: ' . $id_paciente);
            
            // Validar ID
            if (empty($id_paciente) || !is_numeric($id_paciente)) {
                return $this->responderJson(400, true, 'ID de paciente inválido');
            }
            
            // Verificar que el paciente existe
            $paciente = $this->patientModel->find($id_paciente);
            if (!$paciente) {
                return $this->responderJson(404, true, 'Paciente no encontrado');
            }
            
            $data["data"] = $this->NEModel->getNotasEvolucionByPaciente($id_paciente);
            
            log_message('info', 'Notas de evolución obtenidas: ' . count($data["data"]));
            
            return $this->response
                ->setStatusCode(200)
                ->setJSON($data);
                
        } catch (DatabaseException $e) {
            log_message('error', 'Error de base de datos obteniendo notas de evolución: ' . $e->getMessage());
            return $this->responderJson(500, true, 'Error de base de datos al obtener notas de evolución');
            
        } catch (\Exception $e) {
            log_message('critical', 'Error inesperado obteniendo notas de evolución: ' . $e->getMessage());
            return $this->responderJson(500, true, 'Error interno del servidor al obtener notas de evolución');
        }
    }
}
