<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PacientesModel extends Model
{
    protected $table      = 'pacientes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    //protected $allowedFields = ['name', 'email'];
    protected $allowedFields = [
        'nombre',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'nacionalidad',
        'domicilio',
        'telefono',
        'celular',
        'email'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // Soft deletes desactivados; sin campo deleted_at en BD

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
            $mensaje = 'Paciente guardado exitosamente';
            if (!$success) {
                $mensaje = 'Ocurrió un error al guardar';
            }
            return [
                'success' => $success,
                'message' => $mensaje
            ];
        } catch (DatabaseException $e) {
            $this->logResult(false, $data, 'Database error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->logResult(false, $data, $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteUser($id)
    {
        $logger = service('logger');
        try {

            $success = $this->delete($id);

            $this->logResult($success, 'Paciente eliminado con éxito: ' . $id);

            $mensaje = 'Paciente eliminado exitosamente';
            if (!$success) {
                $mensaje = 'Ocurrió un error al eliminar';
            }
            return [
                'success' => $success,
                'message' => $mensaje
            ];
        } catch (DatabaseException $e) {
            $logger->error('Failed to Delete Pacient: ' . $id . ' Error: ' . $e);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $logger->error('Failed to Delete Pacient: ' . $id . ' Error: ' . $e);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function logResult($success, $data, $error = null)
    {
        $logger = service('logger');
        if (isset($datos['id_usuario'])) {
            unset($data['password']);
        }
        if ($success) {
            $logger->info('User save successfully: ' . json_encode($data));
        } else {
            $logger->error('Failed to save User: ' . json_encode($data) . ' Error: ' . $error);
        }
    }
    public function getPaginated($perPage)
    {
        return $this->paginate($perPage);
    }

    public function getPaginationData()
    {
        return $this->pager;
    }

    public function getHistorial($id)
    {
        // Placeholder - implement real logic later
        return [];
    }

    public function getCitas($id)
    {
        // Placeholder - implement real logic later
        return [];
    }

    public function getRecetas($id)
    {
        // Placeholder - implement real logic later
        return [];
    }
}
