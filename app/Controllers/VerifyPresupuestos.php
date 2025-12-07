<?php

namespace App\Controllers;

use App\Models\PresupuestosModel;
use App\Models\PresupuestosDetallesModel;
use CodeIgniter\Controller;

class VerifyPresupuestos extends Controller
{
    public function index()
    {
        echo "Verifying Presupuestos Module...\n";

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
                echo "SUCCESS: Budget created with ID: $id\n";
            } else {
                echo "ERROR: Failed to create budget.\n";
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
                echo "SUCCESS: Detail added with ID: $detalleId\n";
            } else {
                echo "ERROR: Failed to add detail.\n";
                print_r($detallesModel->errors());
            }

            // 3. Retrieve budget with details
            $budget = $presupuestosModel->getPresupuestoWithDetalles($id);
            if ($budget && !empty($budget['detalles'])) {
                echo "SUCCESS: Retrieved budget with details.\n";
            } else {
                echo "ERROR: Failed to retrieve budget or details.\n";
            }

            // 4. Delete test budget
            $presupuestosModel->delete($id);
            echo "SUCCESS: Test budget deleted (soft delete).\n";

        } catch (\Exception $e) {
            echo "EXCEPTION: " . $e->getMessage() . "\n";
        }
    }
}
