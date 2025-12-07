<?php

namespace App\Models;

use CodeIgniter\Model;

class PresupuestosDetallesModel extends Model
{
    protected $table = 'presupuestos_detalles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_presupuesto', 'id_servicio', 'descripcion', 'cantidad', 
        'precio_unitario', 'descuento_porcentaje', 'subtotal'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validaciones
    protected $validationRules = [
        'id_presupuesto' => 'required|integer',
        'id_servicio' => 'required|integer',
        'cantidad' => 'required|decimal|greater_than[0]',
        'precio_unitario' => 'required|decimal|greater_than[0]',
        'descuento_porcentaje' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'subtotal' => 'required|decimal',
    ];

    // Obtener detalles de un presupuesto
    public function getDetallesByPresupuesto($id_presupuesto)
    {
        return $this->select('presupuestos_detalles.*, servicios.nombre as servicio_nombre')
                    ->join('servicios', 'servicios.id = presupuestos_detalles.id_servicio')
                    ->where('presupuestos_detalles.id_presupuesto', $id_presupuesto)
                    ->findAll();
    }

    // Calcular subtotal automáticamente
    public function calculateSubtotal($cantidad, $precio_unitario, $descuento_porcentaje = 0)
    {
        $subtotal = $cantidad * $precio_unitario;
        $descuento = $subtotal * ($descuento_porcentaje / 100);
        return $subtotal - $descuento;
    }

    // Guardar detalle con cálculo automático
    public function saveDetalle($data)
    {
        // Calcular subtotal solo si no se proporciona
        // Si viene desde el controlador con subtotal ya calculado, se usa ese
        if (!isset($data['subtotal']) || empty($data['subtotal'])) {
            if (isset($data['cantidad']) && isset($data['precio_unitario'])) {
                $data['subtotal'] = $this->calculateSubtotal(
                    $data['cantidad'],
                    $data['precio_unitario'],
                    $data['descuento_porcentaje'] ?? 0
                );
            }
        }
        
        return $this->insert($data);
    }

    // Actualizar detalle con cálculo automático
    public function updateDetalle($id, $data)
    {
        // Calcular subtotal si no se proporciona
        if (!isset($data['subtotal'])) {
            $data['subtotal'] = $this->calculateSubtotal(
                $data['cantidad'],
                $data['precio_unitario'],
                $data['descuento_porcentaje'] ?? 0
            );
        }
        
        return $this->update($id, $data);
    }

    // Eliminar todos los detalles de un presupuesto
    public function deleteByPresupuesto($id_presupuesto)
    {
        return $this->where('id_presupuesto', $id_presupuesto)->delete();
    }
}
