<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PresupuestosModel;
use App\Models\PresupuestosDetallesModel;

class VerifyPresupuestos extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'verify:presupuestos';
    protected $description = 'Verifies Presupuestos module functionality';

    public function run(array $params)
    {
        CLI::write('Verifying Presupuestos Module...', 'yellow');

        $presupuestosModel = new PresupuestosModel();
        $detallesModel = new PresupuestosDetallesModel();

        // 1. Create a dummy budget
        $data = [
            'id_paciente' => 1, // Assuming patient 1 exists
            'id_usuario' => 1,  // Assuming user 1 exists
            'folio' => $presupuestosModel->generateFolio(),
            'fecha_emision' => date('Y-m-d H:i:s'),
            'fecha_vigencia' => date('Y-m-d', strtotime('+30 days')),
            'subtotal' => 100.00,
            'iva' => 16.00,
            'total' => 116.00,
            'estado' => 'borrador',
            'observaciones' => 'Test budget'
        ];

        try {
            $id = $presupuestosModel->insert($data);
            if ($id) {
                CLI::write("SUCCESS: Budget created with ID: $id", 'green');
            } else {
                CLI::error("ERROR: Failed to create budget.");
                print_r($presupuestosModel->errors());
                return;
            }

            // 2. Add details
            $detalleData = [
                'id_presupuesto' => $id,
                'id_servicio' => 1, // Assuming service 1 exists
                'descripcion' => 'Test Service',
                'cantidad' => 1,
                'precio_unitario' => 100.00,
                'descuento_porcentaje' => 0,
                'subtotal' => 100.00
            ];

            $detalleId = $detallesModel->insert($detalleData);
            if ($detalleId) {
                CLI::write("SUCCESS: Detail added with ID: $detalleId", 'green');
            } else {
                CLI::error("ERROR: Failed to add detail.");
                print_r($detallesModel->errors());
            }

            // 3. Retrieve budget with details
            $budget = $presupuestosModel->getPresupuestoWithDetalles($id);
            if ($budget && !empty($budget['detalles'])) {
                CLI::write("SUCCESS: Retrieved budget with details.", 'green');
            } else {
                CLI::error("ERROR: Failed to retrieve budget or details.");
            }

            // 4. Delete test budget
            $presupuestosModel->delete($id);
            CLI::write("SUCCESS: Test budget deleted (soft delete).", 'green');

        } catch (\Exception $e) {
            CLI::error("EXCEPTION: " . $e->getMessage());
        }
    }
}
