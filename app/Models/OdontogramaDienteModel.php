<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar los dientes individuales del odontograma
 * 
 * Cada diente tiene 5 superficies (oclusal, vestibular, lingual, mesial, distal)
 * y puede tener diferentes estados, diagnósticos y tratamientos.
 * 
 * Sistema FDI (Federación Dental Internacional):
 * - Los números que terminan en 8 son terceros molares
 * - Los números que terminan en 3 son caninos
 * - Cuadrante 1 (18): Superior Derecho
 * - Cuadrante 2 (28): Superior Izquierdo
 * - Cuadrante 3 (38): Inferior Izquierdo
 * - Cuadrante 4 (48): Inferior Derecho
 */
class OdontogramaDienteModel extends Model
{
    protected $table = 'odontograma_dientes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_odontograma',
        'numero_diente',
        'estado',
        'sup_oclusal',
        'sup_vestibular',
        'sup_lingual',
        'sup_mesial',
        'sup_distal',
        'movilidad',
        'sensibilidad',
        'diagnosticos',
        'tratamientos_realizados',
        'tratamientos_pendientes',
        'condiciones_especiales',
        'hallazgos',
        'notas'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Estados posibles de una superficie (códigos del catálogo)
     */
    public static array $estadosSuperficie = [
        'S001' => ['nombre' => 'Sano', 'color' => '#4CAF50'],
        'S002' => ['nombre' => 'Cariado', 'color' => '#F44336'],
        'S003' => ['nombre' => 'Obturado', 'color' => '#2196F3'],
        'S004' => ['nombre' => 'Fracturado', 'color' => '#FF9800'],
        'S005' => ['nombre' => 'Desgastado', 'color' => '#9E9E9E'],
        'S006' => ['nombre' => 'Corona', 'color' => '#FFD700'],
        'S007' => ['nombre' => 'Incrustación', 'color' => '#FF6B35'],
        'S008' => ['nombre' => 'Sellante', 'color' => '#9C27B0'],
        'S009' => ['nombre' => 'Erosionado', 'color' => '#795548'],
        'S010' => ['nombre' => 'Pigmentado', 'color' => '#607D8B']
    ];

    /**
     * Estados generales del diente
     */
    public static array $estadosDiente = [
        'presente' => 'Presente',
        'ausente' => 'Ausente',
        'extraido' => 'Extraído',
        'impactado' => 'Impactado',
        'erupcion' => 'En erupción',
        'supernumerario' => 'Supernumerario',
        'corona' => 'Con corona',
        'implante' => 'Implante',
        'endodoncia' => 'Endodoncia',
        'protesis' => 'Parte de prótesis'
    ];

    /**
     * Condiciones especiales del diente
     */
    public static array $condicionesEspeciales = [
        'movilidad_grado_1' => 'Movilidad Grado I',
        'movilidad_grado_2' => 'Movilidad Grado II',
        'movilidad_grado_3' => 'Movilidad Grado III',
        'sensibilidad_frio' => 'Sensibilidad al frío',
        'sensibilidad_calor' => 'Sensibilidad al calor',
        'sensibilidad_percusion' => 'Sensibilidad a la percusión',
        'absceso' => 'Absceso',
        'fistula' => 'Fístula',
        'recesion_gingival' => 'Recesión gingival',
        'bolsa_periodontal' => 'Bolsa periodontal'
    ];

    /**
     * Obtiene todos los dientes de un odontograma
     */
    public function getDientesPorOdontograma(int $idOdontograma): array
    {
        return $this->where('id_odontograma', $idOdontograma)
                    ->orderBy('numero_diente', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene o crea un diente específico
     */
    public function getOrCreateDiente(int $idOdontograma, int $numeroDiente): array
    {
        $diente = $this->where('id_odontograma', $idOdontograma)
                       ->where('numero_diente', $numeroDiente)
                       ->first();
        
        if (!$diente) {
            $id = $this->insert([
                'id_odontograma' => $idOdontograma,
                'numero_diente' => $numeroDiente,
                'estado' => 'presente',
                'sup_oclusal' => 'S001',
                'sup_vestibular' => 'S001',
                'sup_lingual' => 'S001',
                'sup_mesial' => 'S001',
                'sup_distal' => 'S001'
            ]);
            $diente = $this->find($id);
        }
        
        return $diente;
    }

    /**
     * Actualiza el estado de una superficie específica
     */
    public function actualizarSuperficie(int $idOdontograma, int $numeroDiente, string $superficie, string $estado, ?int $idUsuario = null): bool
    {
        $superficiesValidas = ['sup_oclusal', 'sup_vestibular', 'sup_lingual', 'sup_mesial', 'sup_distal'];
        
        if (!in_array($superficie, $superficiesValidas)) {
            return false;
        }
        
        $diente = $this->getOrCreateDiente($idOdontograma, $numeroDiente);
        $valorAnterior = $diente[$superficie] ?? 'S001';
        
        $result = $this->update($diente['id'], [
            $superficie => $estado
        ]);
        
        if ($result && $idUsuario) {
            // Registrar en historial
            $historialModel = new OdontogramaHistorialModel();
            $historialModel->registrarCambio([
                'id_odontograma' => $idOdontograma,
                'numero_diente' => $numeroDiente,
                'tipo_accion' => 'modificacion_superficie',
                'campo_modificado' => $superficie,
                'valor_anterior' => $valorAnterior,
                'valor_nuevo' => $estado,
                'descripcion_cambio' => "Superficie {$superficie} actualizada de {$valorAnterior} a {$estado}",
                'usuario_modificacion' => $idUsuario
            ]);
        }
        
        return $result;
    }

    /**
     * Actualiza el estado general de un diente
     */
    public function actualizarEstadoDiente(int $idOdontograma, int $numeroDiente, string $estado, ?int $idUsuario = null): bool
    {
        if (!isset(self::$estadosDiente[$estado])) {
            return false;
        }
        
        $diente = $this->getOrCreateDiente($idOdontograma, $numeroDiente);
        $valorAnterior = $diente['estado'] ?? 'presente';
        
        $result = $this->update($diente['id'], [
            'estado' => $estado
        ]);
        
        if ($result && $idUsuario) {
            $historialModel = new OdontogramaHistorialModel();
            $historialModel->registrarCambio([
                'id_odontograma' => $idOdontograma,
                'numero_diente' => $numeroDiente,
                'tipo_accion' => 'modificacion_estado',
                'campo_modificado' => 'estado',
                'valor_anterior' => $valorAnterior,
                'valor_nuevo' => $estado,
                'descripcion_cambio' => "Estado del diente {$numeroDiente} cambiado de {$valorAnterior} a {$estado}",
                'usuario_modificacion' => $idUsuario
            ]);
        }
        
        return $result;
    }

    /**
     * Actualiza múltiples superficies de un diente a la vez
     */
    public function actualizarDienteCompleto(int $idOdontograma, int $numeroDiente, array $datos, ?int $idUsuario = null): bool
    {
        $diente = $this->getOrCreateDiente($idOdontograma, $numeroDiente);
        
        $camposPermitidos = [
            'estado', 'sup_oclusal', 'sup_vestibular', 'sup_lingual', 
            'sup_mesial', 'sup_distal', 'movilidad', 'sensibilidad',
            'diagnosticos', 'tratamientos_realizados', 'tratamientos_pendientes',
            'condiciones_especiales', 'hallazgos', 'notas'
        ];
        
        $datosActualizar = [];
        $cambios = [];
        
        foreach ($datos as $campo => $valor) {
            if (in_array($campo, $camposPermitidos)) {
                $valorAnterior = $diente[$campo] ?? null;
                if ($valorAnterior !== $valor) {
                    $datosActualizar[$campo] = $valor;
                    $cambios[] = [
                        'campo' => $campo,
                        'anterior' => $valorAnterior,
                        'nuevo' => $valor
                    ];
                }
            }
        }
        
        if (empty($datosActualizar)) {
            return true; // No hay cambios
        }
        
        $result = $this->update($diente['id'], $datosActualizar);
        
        if ($result && $idUsuario && !empty($cambios)) {
            $historialModel = new OdontogramaHistorialModel();
            foreach ($cambios as $cambio) {
                $historialModel->registrarCambio([
                    'id_odontograma' => $idOdontograma,
                    'numero_diente' => $numeroDiente,
                    'tipo_accion' => 'modificacion',
                    'campo_modificado' => $cambio['campo'],
                    'valor_anterior' => $cambio['anterior'],
                    'valor_nuevo' => $cambio['nuevo'],
                    'descripcion_cambio' => "Campo {$cambio['campo']} actualizado",
                    'usuario_modificacion' => $idUsuario
                ]);
            }
        }
        
        return $result;
    }

    /**
     * Obtiene el resumen de estados del odontograma
     */
    public function getResumenEstados(int $idOdontograma): array
    {
        $dientes = $this->getDientesPorOdontograma($idOdontograma);
        
        $resumen = [
            'total_dientes' => count($dientes),
            'presentes' => 0,
            'ausentes' => 0,
            'con_caries' => 0,
            'obturados' => 0,
            'con_tratamiento_pendiente' => 0
        ];
        
        foreach ($dientes as $diente) {
            if ($diente['estado'] === 'presente') {
                $resumen['presentes']++;
            } elseif (in_array($diente['estado'], ['ausente', 'extraido'])) {
                $resumen['ausentes']++;
            }
            
            // Contar superficies con caries
            $superficies = ['sup_oclusal', 'sup_vestibular', 'sup_lingual', 'sup_mesial', 'sup_distal'];
            foreach ($superficies as $sup) {
                if ($diente[$sup] === 'S002') {
                    $resumen['con_caries']++;
                    break;
                }
                if ($diente[$sup] === 'S003') {
                    $resumen['obturados']++;
                    break;
                }
            }
            
            if (!empty($diente['tratamientos_pendientes'])) {
                $resumen['con_tratamiento_pendiente']++;
            }
        }
        
        return $resumen;
    }

    /**
     * Obtiene el color para una superficie según su estado
     */
    public function getColorSuperficie(string $codigo): string
    {
        return self::$estadosSuperficie[$codigo]['color'] ?? '#FFFFFF';
    }

    /**
     * Obtiene el nombre del estado de una superficie
     */
    public function getNombreEstado(string $codigo): string
    {
        return self::$estadosSuperficie[$codigo]['nombre'] ?? 'Desconocido';
    }
}
