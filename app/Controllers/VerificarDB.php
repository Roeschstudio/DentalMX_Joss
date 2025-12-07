<?php

namespace App\Controllers;

class VerificarDB extends BaseController
{
    public function medicamentos()
    {
        $db = \Config\Database::connect();
        
        // Verificar si la tabla existe
        if (!$db->tableExists('medicamentos')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La tabla medicamentos NO existe'
            ]);
        }
        
        // Obtener la estructura de la tabla
        $query = $db->query("DESCRIBE medicamentos");
        $fields = $query->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'La tabla medicamentos existe correctamente',
            'estructura' => $fields
        ]);
    }
    
    public function dropMedicamentos()
    {
        $db = \Config\Database::connect();
        
        if ($db->tableExists('medicamentos')) {
            $forge = \Config\Database::forge();
            $forge->dropTable('medicamentos', true);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tabla medicamentos eliminada correctamente'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'La tabla medicamentos no existe'
        ]);
    }

    public function servicios()
    {
        $db = \Config\Database::connect();
        
        // Verificar si la tabla existe
        if (!$db->tableExists('servicios')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La tabla servicios NO existe'
            ]);
        }
        
        // Obtener la estructura de la tabla
        $query = $db->query("DESCRIBE servicios");
        $fields = $query->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'La tabla servicios existe correctamente',
            'estructura' => $fields
        ]);
    }
}
