<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class DatosGeneralesModel extends Model
{
    protected $table      = 'datos_generales';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    //protected $allowedFields = ['name', 'email'];
    protected $allowedFields = [
        'id_paciente',
        'edad',
        'sexo',
        'peso',
        'tipo_sangre',
        'fecha_nacimiento',
        'estado_civil',
        'ocupacion',
        'lugar_trabajo',
        'cuenta_seguro',
        'seguro',
        'nombre_contacto_emergencia',
        'embarazo',
        'meses_embarazo',
        'ginecologo',
        'telefono_ginecologo',
        'lugar_control'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getAllPacients()
    {
        $users = $this
            ->orderBy('id', 'DESC')
            ->findAll();
        return $users;
    }

    public function saveData($data)
    {
        try {
            $success = $this->save($data);
             $this->logResult($success, $data);
            $mensaje = 'Guardado exitoso';
            $insertId = null;

            if ($success) {
                // Solo obtener el ID si fue inserción nueva
                if (!isset($data['id']) || empty($data['id'])) {
                    $insertId = $this->getInsertID(); // ← Aquí se obtiene el último ID insertado
                } else {
                    $insertId = $data['id']; // ← En caso de actualización, devuelve el mismo ID
                }
            } else {
                $mensaje = 'Ocurrió un error al guardar';
            }

            return [
                'success' => $success,
                'message' => $mensaje,
                'id' => $insertId
            ];
        } catch (DatabaseException $e) {
            $this->logResult(false, $data, 'Database error: ' . $e->getMessage());
            $mensaje = $e->getMessage();
            if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
                $mensaje = 'El paciente seleccionado no existe. Por favor verifica el ID.';
            }
            return [
                'success' => false,
                'message' => $mensaje
            ];
        } catch (\Exception $e) {
            $this->logResult(false, $data, $e->getMessage());
            $mensaje = $e->getMessage();
            if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
                $mensaje = 'El paciente seleccionado no existe. Por favor verifica el ID.';
            }
            return [
                'success' => false,
                'message' => $mensaje
            ];
        }
    }

    private function logResult($success, $data, $error = null)
    {
        $logger = service('logger');

        if ($success) {
            $logger->info(' save successfully: ' . json_encode($data));
        } else {
            $logger->error('Failed to save : ' . json_encode($data) . ' Error: ' . $error);
        }
    }
}
