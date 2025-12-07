<?php

namespace App\Controllers;

use App\Models\ServiciosModel;
use App\Controllers\Navigation;

class Servicios extends BaseController
{
    public function index()
    {
        $model = new ServiciosModel();
        
        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('servicios', [
            'subtitle' => 'Catálogo de servicios'
        ]);
        
        $data = array_merge($navigationData, [
            'servicios' => $model->findAll()
        ]);
        
        return view('catalogos/servicios', $data);
    }

    public function save()
    {
        $model = new ServiciosModel();
        
        // Support JSON or Form Data
        $data = $this->request->getJSON(true);
        if (!$data) {
            $data = $this->request->getPost();
        }

        if ($model->save($data)) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'errors' => $model->errors()]);
    }

    public function delete()
    {
        $model = new ServiciosModel();
        
        // Support JSON or Form Data
        $data = $this->request->getJSON(true);
        $id = $data['id'] ?? $this->request->getPost('id');

        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false]);
    }
}
