<?php

namespace App\Models;

use CodeIgniter\Model;

class Patient extends Model
{
    protected $table            = 'pacientes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nombre', 'primer_apellido', 'segundo_apellido', 'email', 'telefono', 'celular',
        'fecha_nacimiento', 'nacionalidad', 'domicilio'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // Soft deletes desactivados; sin campo deleted_at en BD

    protected $validationRules = [
        'nombre' => 'required|min_length[2]|max_length[100]',
        'primer_apellido' => 'required|min_length[2]|max_length[100]',
        'segundo_apellido' => 'permit_empty|max_length[100]',
        'email' => 'valid_email|max_length[100]|is_unique[pacientes.email,id,{id}]',
        'telefono' => 'max_length[20]',
        'celular' => 'permit_empty|max_length[20]',
        'fecha_nacimiento' => 'required|valid_date[Y-m-d]',
        'nacionalidad' => 'permit_empty|max_length[50]',
        'domicilio' => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'El email ya está registrado.',
        ],
    ];

    // Field mapping for backward compatibility with existing frontend
    private $fieldMapping = [
        'apellido' => 'primer_apellido',
        'direccion' => 'domicilio',
        'telefono' => 'celular'
    ];

    /**
     * Map old field names to new field names
     */
    public function mapFields(array $data): array
    {
        $mappedData = [];
        
        foreach ($data as $key => $value) {
            // If the field exists in mapping, use the new field name
            if (isset($this->fieldMapping[$key])) {
                $newKey = $this->fieldMapping[$key];
                
                // Handle combining first and last name
                if ($key === 'primer_apellido' && isset($data['segundo_apellido'])) {
                    $mappedData[$newKey] = trim($value . ' ' . $data['segundo_apellido']);
                } elseif ($key === 'segundo_apellido' && isset($mappedData['apellido'])) {
                    // Skip segundo_apellido if already combined with primer_apellido
                    continue;
                } else {
                    $mappedData[$newKey] = $value;
                }
            } else {
                // Keep the field as is if not in mapping
                $mappedData[$key] = $value;
            }
        }
        
        return $mappedData;
    }

    /**
     * Save data with field mapping
     */
    public function saveDataWithMapping(array $data): array
    {
        try {
            // Map fields from old structure to new structure
            $mappedData = $this->mapFields($data);
            
            // Set default values for required fields if not provided
            // No default values needed as all fields are optional except nombre and primer_apellido
            
            $success = $this->save($mappedData);
            
            return [
                'success' => $success,
                'message' => $success ? 'Paciente guardado exitosamente' : 'Ocurrió un error al guardar',
                'id' => $this->getInsertID() ?? $data['id'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all patients with field mapping for backward compatibility
     */
    public function getAllPatients()
    {
        $patients = $this->orderBy('id', 'DESC')->findAll();
        
        // Map fields back to old format for frontend compatibility
        $mappedPatients = [];
        foreach ($patients as $patient) {
            $mappedPatient = $patient;
            
            // Split apellido into primer_apellido and segundo_apellido for compatibility
            $apellidos = explode(' ', $patient['apellido'] ?? '', 2);
            $mappedPatient['primer_apellido'] = $apellidos[0] ?? '';
            $mappedPatient['segundo_apellido'] = $apellidos[1] ?? '';
            
            // Map other fields for compatibility
            $mappedPatient['domicilio'] = $patient['direccion'] ?? '';
            $mappedPatient['celular'] = $patient['telefono'] ?? '';
            
            $mappedPatients[] = $mappedPatient;
        }
        
        return $mappedPatients;
    }

    /**
     * Delete patient with error handling
     */
    public function deletePatient($id): array
    {
        try {
            $success = $this->delete($id);
            
            return [
                'success' => $success,
                'message' => $success ? 'Paciente eliminado exitosamente' : 'Ocurrió un error al eliminar'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene pacientes con búsqueda y filtros
     */
    public function getPatients($search = null, $estado = null, $perPage = 20)
    {
        // DEBUG: Log table name and soft delete configuration
        log_message('debug', 'Patient::getPatients() - Table: ' . $this->table);
        log_message('debug', 'Patient::getPatients() - Use soft deletes: ' . ($this->useSoftDeletes ? 'true' : 'false'));
        log_message('debug', 'Patient::getPatients() - Deleted field: ' . $this->deletedField);
        
        // DEBUG: Check if table exists
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        log_message('debug', 'Patient::getPatients() - Available tables: ' . implode(', ', $tables));
        log_message('debug', 'Patient::getPatients() - Table exists: ' . (in_array($this->table, $tables) ? 'true' : 'false'));
        
        // Aplicar búsqueda por nombre, email, teléfono
        if ($search) {
            $this->groupStart();
            $this->like('nombre', $search);
            $this->orLike('primer_apellido', $search);
            $this->orLike('segundo_apellido', $search);
            $this->orLike('CONCAT(primer_apellido, " ", segundo_apellido)', $search);
            $this->orLike('email', $search);
            $this->orLike('telefono', $search);
            $this->orLike('celular', $search);
            $this->groupEnd();
        }

        // No estado filter needed as it doesn't exist in database

        // Ordenar por primer_apellido y nombre
        $this->orderBy('primer_apellido', 'ASC');
        $this->orderBy('nombre', 'ASC');

        $patients = $this->paginate($perPage);
        
        // DEBUG: Log the structure of patient data
        if (!empty($patients)) {
            log_message('debug', 'Patient::getPatients() - First patient keys: ' . implode(', ', array_keys($patients[0])));
            log_message('debug', 'Patient::getPatients() - Checking for apellido key: ' . (isset($patients[0]['apellido']) ? 'EXISTS' : 'MISSING'));
            log_message('debug', 'Patient::getPatients() - Checking for primer_apellido key: ' . (isset($patients[0]['primer_apellido']) ? 'EXISTS' : 'MISSING'));
        }
        
        return [
            'patients' => $patients,
            'pager' => $this->pager
        ];
    }

    /**
     * Obtiene un paciente con sus relaciones (citas, recetas, presupuestos)
     */
    public function getPatientWithRelations($id)
    {
        $patient = $this->find($id);
        
        if (!$patient) {
            return null;
        }

        // Aquí se añadirán las relaciones en pasos futuros
        // Por ahora, solo devolvemos el paciente
        return $patient;
    }

    /**
     * Busca pacientes por término específico
     */
    public function searchPatients($term, $limit = 10)
    {
        return $this->select('id, nombre, primer_apellido, segundo_apellido, email, telefono, celular')
                    ->groupStart()
                    ->like('nombre', $term)
                    ->orLike('primer_apellido', $term)
                    ->orLike('segundo_apellido', $term)
                    ->orLike('CONCAT(primer_apellido, " ", segundo_apellido)', $term)
                    ->orLike('email', $term)
                    ->orLike('telefono', $term)
                    ->orLike('celular', $term)
                    ->groupEnd()
                    ->orderBy('primer_apellido', 'ASC')
                    ->orderBy('nombre', 'ASC')
                    ->limit($limit)
                    ->find();
    }
}
