<?php

namespace App\Models;

use CodeIgniter\Model;

class PresupuestosModel extends Model
{
    protected $table = 'presupuestos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_paciente', 'id_usuario', 'folio', 'fecha_emision', 
        'fecha_vigencia', 'subtotal', 'iva', 'total', 'estado', 
        'observaciones'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;

    // Validaciones
    protected $validationRules = [
        'id_paciente' => 'required|integer',
        'id_usuario' => 'required|integer',
        'folio' => 'required|max_length[20]',
        'fecha_emision' => 'required|valid_date[Y-m-d H:i:s]',
        'fecha_vigencia' => 'required|valid_date[Y-m-d]',
        'subtotal' => 'required|decimal',
        'iva' => 'permit_empty|decimal',
        'total' => 'required|decimal',
        'estado' => 'required|in_list[borrador,pendiente,aprobado,rechazado,convertido]',
    ];

    protected $validationMessages = [
        'folio' => [
            'max_length' => 'El folio no puede exceder 20 caracteres',
        ],
        'estado' => [
            'in_list' => 'El estado debe ser uno de: borrador, pendiente, aprobado, rechazado, convertido',
        ],
    ];

    // Generar folio único
    public function generateFolio()
    {
        $year = date('Y');
        $count = $this->where('YEAR(fecha_emision)', $year)->countAllResults();
        return 'PRE-' . $year . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    // Obtener presupuestos con relaciones
    public function getPresupuestosWithRelations($estado = null)
    {
        $builder = $this->select('presupuestos.*, 
                                pacientes.nombre as paciente_nombre,
                                pacientes.primer_apellido as paciente_apellido,
                                usuarios.nombre as medico_nombre')
                         ->join('pacientes', 'pacientes.id = presupuestos.id_paciente')
                         ->join('usuarios', 'usuarios.id = presupuestos.id_usuario');
        
        if ($estado) {
            $builder->where('presupuestos.estado', $estado);
        }
        
        return $builder->orderBy('presupuestos.fecha_emision', 'DESC')
                       ->findAll();
    }

    // Obtener presupuesto con detalles
    public function getPresupuestoWithDetalles($id)
    {
        $presupuesto = $this->select('presupuestos.*, 
                                    pacientes.nombre as paciente_nombre,
                                    pacientes.primer_apellido as paciente_apellido,
                                    usuarios.nombre as medico_nombre')
                            ->join('pacientes', 'pacientes.id = presupuestos.id_paciente')
                            ->join('usuarios', 'usuarios.id = presupuestos.id_usuario')
                            ->find($id);
        
        if ($presupuesto) {
            $detallesModel = new PresupuestosDetallesModel();
            $presupuesto['detalles'] = $detallesModel->getDetallesByPresupuesto($id);
        }
        
        return $presupuesto;
    }

    // Obtener presupuestos por paciente
    public function getPresupuestosByPaciente($id_paciente)
    {
        return $this->select('presupuestos.*, usuarios.nombre as medico_nombre')
                    ->join('usuarios', 'usuarios.id = presupuestos.id_usuario')
                    ->where('presupuestos.id_paciente', $id_paciente)
                    ->orderBy('presupuestos.fecha_emision', 'DESC')
                    ->findAll();
    }

    // Cambiar estado de presupuesto
    public function cambiarEstado($id, $nuevoEstado)
    {
        return $this->update($id, ['estado' => $nuevoEstado]);
    }
}
