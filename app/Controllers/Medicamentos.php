<?php

namespace App\Controllers;

use App\Models\MedicamentosModel;
use App\Controllers\Navigation;

class Medicamentos extends BaseController
{
    public function index()
    {
        $model = new MedicamentosModel();
        
        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('medicamentos', [
            'subtitle' => 'Catálogo de medicamentos'
        ]);
        
        $data = array_merge($navigationData, [
            'medicamentos' => $model->findAll()
        ]);
        
        return view('catalogos/medicamentos', $data);
    }

    public function save()
    {
        $model = new MedicamentosModel();
        $data = $this->request->getJSON(true); // Usar getJSON porque el frontend envía application/json
        
        if (!$data) {
             $data = $this->request->getPost(); // Fallback por si acaso
        }

        if ($model->save($data)) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'errors' => $model->errors()]);
        }
    }

    public function delete()
    {
        $model = new MedicamentosModel();
        $data = $this->request->getJSON(true);
        $id = $data['id'] ?? $this->request->getPost('id');
        
        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }
}
