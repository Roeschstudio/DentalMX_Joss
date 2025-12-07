<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class NotasEvolucionModel extends Model
{
    protected $table      = 'notas_evolucion';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_paciente',
        'fecha',
        'tratamiento_realizado',
        'total',
        'abono',
        'saldo',
        'firma'
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
            $mensaje = 'Nota guardada exitosamente';
            if (!$success) {
                $mensaje = 'Ocurrió un error al guardar';
            }
            return [
                'success' => $success,
                'message' => $mensaje
            ];
        } catch (DatabaseException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteNote($id)
    {
        $logger = service('logger');
        try {

            $success = $this->delete($id);

            $mensaje = 'Nota eliminada exitosamente';
            if (!$success) {
                $mensaje = 'Ocurrió un error al eliminar';
            }
            return [
                'success' => $success,
                'message' => $mensaje
            ];
        } catch (DatabaseException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getNotasEvolucionByPaciente($id_paciente)
    {
        return $this->db->table('notas_evolucion')
            ->where('id_paciente', $id_paciente)
            ->get()
            ->getResult();
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
}
