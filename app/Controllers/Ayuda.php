<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Navigation;

class Ayuda extends BaseController
{
    public function index()
    {
        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('ayuda', [
            'subtitle' => 'Centro de ayuda y documentación'
        ]);
        
        $data = array_merge($navigationData, [
            'pageTitle' => 'Centro de Ayuda'
        ]);
        
        return view('ayuda/index', $data);
    }
}
