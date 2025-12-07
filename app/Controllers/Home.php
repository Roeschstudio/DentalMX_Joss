<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Navigation;
use App\Models\Patient;
use App\Models\RecetasModel;

class Home extends BaseController {
    public function index() {
        // Preparar datos de navegación usando el controller Navigation
        $navigationData = Navigation::prepareNavigationData('dashboard', [
            'subtitle' => 'Panel principal de control'
        ]);
        
        // Datos adicionales para el dashboard con datos reales
        $data = array_merge($navigationData, [
            'stats' => $this->getDashboardStats(),
            'recentAppointments' => $this->getRecentAppointments(),
            'upcomingAppointments' => $this->getUpcomingAppointments()
        ]);
        
        return view('dashboard/index', $data);
    }
    
    private function getDashboardStats(): array {
        $db = \Config\Database::connect();
        
        // Contar pacientes activos
        $totalPatients = $db->table('pacientes')
            ->where('deleted_at IS NULL')
            ->countAllResults();
        
        // Contar citas totales (si existe la tabla)
        $totalAppointments = 0;
        $todayAppointments = 0;
        try {
            if ($db->tableExists('citas')) {
                $totalAppointments = $db->table('citas')->countAllResults();
                $todayAppointments = $db->table('citas')
                    ->where('DATE(fecha_inicio)', date('Y-m-d'))
                    ->countAllResults();
            }
        } catch (\Exception $e) {
            // Tabla no existe, dejar en 0
        }
        
        // Contar recetas pendientes
        $pendingPrescriptions = 0;
        try {
            if ($db->tableExists('recetas')) {
                // Contar todas las recetas sin eliminar
                $pendingPrescriptions = $db->table('recetas')
                    ->where('deleted_at IS NULL', null, false)
                    ->countAllResults();
            }
        } catch (\Exception $e) {
            // Tabla no existe, dejar en 0
        }
        
        return [
            'totalPatients' => $totalPatients,
            'totalAppointments' => $totalAppointments,
            'todayAppointments' => $todayAppointments,
            'pendingPrescriptions' => $pendingPrescriptions
        ];
    }
    
    private function getRecentAppointments(): array {
        $db = \Config\Database::connect();
        $appointments = [];
        
        try {
            if ($db->tableExists('citas')) {
                $query = $db->table('citas c')
                    ->select('c.*, p.nombre, p.primer_apellido, p.segundo_apellido')
                    ->join('pacientes p', 'p.id = c.id_paciente', 'left')
                    ->where('DATE(c.fecha_inicio) <=', date('Y-m-d'))
                    ->orderBy('c.fecha_inicio', 'DESC')
                    ->limit(5)
                    ->get();
                
                foreach ($query->getResultArray() as $row) {
                    $appointments[] = [
                        'patient' => trim(($row['nombre'] ?? '') . ' ' . ($row['primer_apellido'] ?? '') . ' ' . ($row['segundo_apellido'] ?? '')),
                        'time' => $row['fecha_inicio'] ?? '',
                        'status' => $row['estado'] ?? 'pendiente'
                    ];
                }
            }
        } catch (\Exception $e) {
            // Tabla no existe o error
        }
        
        return $appointments;
    }
    
    private function getUpcomingAppointments(): array {
        $db = \Config\Database::connect();
        $appointments = [];
        
        try {
            if ($db->tableExists('citas')) {
                $query = $db->table('citas c')
                    ->select('c.*, p.nombre, p.primer_apellido, p.segundo_apellido')
                    ->join('pacientes p', 'p.id = c.id_paciente', 'left')
                    ->where('DATE(c.fecha_inicio) >=', date('Y-m-d'))
                    ->where('c.estado !=', 'completada')
                    ->where('c.estado !=', 'cancelada')
                    ->orderBy('c.fecha_inicio', 'ASC')
                    ->limit(5)
                    ->get();
                
                foreach ($query->getResultArray() as $row) {
                    $fecha = date('Y-m-d', strtotime($row['fecha_inicio'] ?? ''));
                    $fechaFormatted = $fecha == date('Y-m-d') ? 'Hoy' : 
                        ($fecha == date('Y-m-d', strtotime('+1 day')) ? 'Mañana' : 
                        date('d/m/Y', strtotime($fecha)));
                    
                    $appointments[] = [
                        'patient' => trim(($row['nombre'] ?? '') . ' ' . ($row['primer_apellido'] ?? '') . ' ' . ($row['segundo_apellido'] ?? '')),
                        'time' => $row['fecha_inicio'] ?? '',
                        'date' => $fechaFormatted
                    ];
                }
            }
        } catch (\Exception $e) {
            // Tabla no existe o error
        }
        
        return $appointments;
    }
}
