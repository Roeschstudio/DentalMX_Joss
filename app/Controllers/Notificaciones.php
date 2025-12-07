<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Navigation;

/**
 * Controlador de Notificaciones
 */
class Notificaciones extends BaseController {
    
    /**
     * Página de notificaciones
     */
    public function index() {
        $navigationData = Navigation::prepareNavigationData('notificaciones', [
            'subtitle' => 'Centro de notificaciones'
        ]);
        
        $data = array_merge($navigationData, [
            'notificaciones' => $this->getNotificaciones()
        ]);
        
        return view('notificaciones/index', $data);
    }
    
    /**
     * Obtener notificaciones (simulado)
     */
    private function getNotificaciones(): array {
        // En producción, obtener de base de datos
        return [
            [
                'id' => 1,
                'tipo' => 'info',
                'titulo' => 'Bienvenido a Dental MX',
                'mensaje' => 'Sistema de gestión dental configurado correctamente.',
                'leida' => false,
                'fecha' => date('Y-m-d H:i:s')
            ]
        ];
    }
}
