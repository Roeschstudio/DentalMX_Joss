<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar los odontogramas de pacientes
 * 
 * Un odontograma representa el estado dental general de un paciente
 * y puede ser de tipo permanente (adulto) o deciduo (infantil).
 * 
 * Sistema FDI (Federación Dental Internacional):
 * - Adultos: Cuadrantes 1-4 (18-11, 21-28, 31-38, 41-48)
 * - Infantiles: Cuadrantes 5-8 (55-51, 61-65, 75-71, 81-85)
 */
class OdontogramaModel extends Model
{
    protected $table = 'odontogramas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_paciente',
        'tipo_dentadura',
        'observaciones_generales',
        'estado_general'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'id_paciente' => 'required|integer|greater_than[0]',
        'tipo_dentadura' => 'required|in_list[permanente,decidua,mixta]',
        'estado_general' => 'permit_empty|in_list[bueno,regular,malo]'
    ];

    protected $validationMessages = [
        'id_paciente' => [
            'required' => 'El ID del paciente es requerido',
            'integer' => 'El ID del paciente debe ser un número entero'
        ],
        'tipo_dentadura' => [
            'required' => 'El tipo de dentadura es requerido',
            'in_list' => 'Tipo de dentadura no válido'
        ]
    ];

    /**
     * Dientes adultos según Sistema FDI
     * Cuadrante 1: Superior Derecho (18-11)
     * Cuadrante 2: Superior Izquierdo (21-28)
     * Cuadrante 3: Inferior Izquierdo (31-38)
     * Cuadrante 4: Inferior Derecho (41-48)
     */
    public static array $dientesAdultos = [
        'superior' => [
            'derecho' => [18, 17, 16, 15, 14, 13, 12, 11],
            'izquierdo' => [21, 22, 23, 24, 25, 26, 27, 28]
        ],
        'inferior' => [
            'derecho' => [48, 47, 46, 45, 44, 43, 42, 41],
            'izquierdo' => [31, 32, 33, 34, 35, 36, 37, 38]
        ]
    ];

    /**
     * Dientes infantiles según Sistema FDI
     * Cuadrante 5: Superior Derecho (55-51)
     * Cuadrante 6: Superior Izquierdo (61-65)
     * Cuadrante 7: Inferior Izquierdo (71-75)
     * Cuadrante 8: Inferior Derecho (81-85)
     */
    public static array $dientesInfantiles = [
        'superior' => [
            'derecho' => [55, 54, 53, 52, 51],
            'izquierdo' => [61, 62, 63, 64, 65]
        ],
        'inferior' => [
            'derecho' => [85, 84, 83, 82, 81],
            'izquierdo' => [71, 72, 73, 74, 75]
        ]
    ];

    /**
     * Superficies/caras de un diente
     */
    public static array $superficies = [
        'oclusal' => 'Oclusal/Incisal',
        'vestibular' => 'Vestibular',
        'lingual' => 'Lingual/Palatino',
        'mesial' => 'Mesial',
        'distal' => 'Distal'
    ];

    /**
     * Obtiene el odontograma activo de un paciente
     */
    public function getOdontogramaPaciente(int $idPaciente): ?array
    {
        return $this->where('id_paciente', $idPaciente)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    /**
     * Obtiene o crea el odontograma de un paciente
     */
    public function getOrCreateOdontograma(int $idPaciente, string $tipoDentadura = 'permanente'): array
    {
        $odontograma = $this->getOdontogramaPaciente($idPaciente);
        
        if (!$odontograma) {
            $id = $this->insert([
                'id_paciente' => $idPaciente,
                'tipo_dentadura' => $tipoDentadura,
                'estado_general' => 'bueno'
            ]);
            $odontograma = $this->find($id);
        }
        
        return $odontograma;
    }

    /**
     * Obtiene odontograma completo con todos los dientes y sus estados
     */
    public function getOdontogramaCompleto(int $idPaciente): array
    {
        $odontograma = $this->getOrCreateOdontograma($idPaciente);
        
        $dienteModel = new OdontogramaDienteModel();
        $dientes = $dienteModel->getDientesPorOdontograma($odontograma['id']);
        
        // Organizar dientes por número para fácil acceso
        $dientesPorNumero = [];
        foreach ($dientes as $diente) {
            $dientesPorNumero[$diente['numero_diente']] = $diente;
        }
        
        return [
            'odontograma' => $odontograma,
            'dientes' => $dientesPorNumero,
            'estructura_adultos' => self::$dientesAdultos,
            'estructura_infantiles' => self::$dientesInfantiles,
            'superficies' => self::$superficies
        ];
    }

    /**
     * Obtiene los tipos de estado disponibles desde el catálogo
     */
    public function getEstadosDisponibles(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('catalogos_odontologicos');
        
        return $builder->where('tipo', 'superficie_estado')
                       ->where('activo', 1)
                       ->orderBy('orden', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Obtiene colores por estado desde el catálogo
     */
    public function getColoresEstados(): array
    {
        $estados = $this->getEstadosDisponibles();
        $colores = [];
        
        foreach ($estados as $estado) {
            $colores[$estado['codigo']] = [
                'nombre' => $estado['nombre'],
                'color' => $estado['color_hex'],
                'icono' => $estado['icono']
            ];
        }
        
        return $colores;
    }

    /**
     * Obtiene el historial de cambios del odontograma
     */
    public function getHistorial(int $idOdontograma, int $limite = 20): array
    {
        $historialModel = new OdontogramaHistorialModel();
        return $historialModel->getHistorialOdontograma($idOdontograma, $limite);
    }

    /**
     * Obtiene información de un diente específico
     */
    public function getNombreDiente(int $numeroDiente): string
    {
        $cuadrante = (int) floor($numeroDiente / 10);
        $posicion = $numeroDiente % 10;
        
        $cuadrantes = [
            1 => 'Superior Derecho',
            2 => 'Superior Izquierdo',
            3 => 'Inferior Izquierdo',
            4 => 'Inferior Derecho',
            5 => 'Superior Derecho (Infantil)',
            6 => 'Superior Izquierdo (Infantil)',
            7 => 'Inferior Izquierdo (Infantil)',
            8 => 'Inferior Derecho (Infantil)'
        ];
        
        $tiposDiente = [
            1 => 'Incisivo Central',
            2 => 'Incisivo Lateral',
            3 => 'Canino',
            4 => 'Primer Premolar',
            5 => 'Segundo Premolar',
            6 => 'Primer Molar',
            7 => 'Segundo Molar',
            8 => 'Tercer Molar'
        ];
        
        $cuadranteNombre = $cuadrantes[$cuadrante] ?? 'Desconocido';
        $tipoDiente = $tiposDiente[$posicion] ?? 'Desconocido';
        
        return "{$tipoDiente} ({$cuadranteNombre})";
    }
}
