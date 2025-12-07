<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\BreadcrumbHelper;

/**
 * Navigation Controller
 * 
 * Gestiona toda la lógica de navegación, breadcrumbs y menús de la aplicación
 * 
 * @package App\Controllers
 */
class Navigation extends BaseController {
    /**
     * Genera breadcrumbs para una página específica con contexto
     * 
     * @param string $page Identificador de la página
     * @param array $context Datos contextuales (ID, nombre, etc.)
     * @return array
     */
    public static function generateBreadcrumbs(string $page, array $context = []): array {
        $breadcrumb = new BreadcrumbHelper();
        
        switch ($page) {
            case 'dashboard':
                // Dashboard no necesita breadcrumbs adicionales
                break;
                
            case 'pacientes':
                $breadcrumb->addItem('Pacientes');
                break;
                
            case 'pacientes_lista':
                $breadcrumb->addItem('Pacientes', base_url('/pacientes'));
                $breadcrumb->addItem('Lista');
                break;
                
            case 'paciente_ver':
                $breadcrumb->addItem('Pacientes', base_url('/pacientes'));
                if (isset($context['paciente'])) {
                    $paciente = $context['paciente'];
                    $nombreCompleto = trim(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellido'] ?? ''));
                    $breadcrumb->addItem($nombreCompleto ?: 'Paciente');
                } else {
                    $breadcrumb->addItem('Ver Paciente');
                }
                break;
                
            case 'paciente_editar':
                $breadcrumb->addItem('Pacientes', base_url('/pacientes'));
                if (isset($context['paciente'])) {
                    $paciente = $context['paciente'];
                    $nombreCompleto = trim(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellido'] ?? ''));
                    $breadcrumb->addItem($nombreCompleto ?: 'Paciente', 
                        base_url('/pacientes/ver/' . ($paciente['id'] ?? '')));
                }
                $breadcrumb->addItem('Editar');
                break;
                
            case 'paciente_nuevo':
                $breadcrumb->addItem('Pacientes', base_url('/pacientes'));
                $breadcrumb->addItem('Nuevo Paciente');
                break;
                
            case 'citas':
                $breadcrumb->addItem('Citas');
                break;
                
            case 'cita_nueva':
                $breadcrumb->addItem('Citas', base_url('/citas'));
                $breadcrumb->addItem('Nueva Cita');
                break;
                
            case 'cita_ver':
                $breadcrumb->addItem('Citas', base_url('/citas'));
                if (isset($context['cita'])) {
                    $cita = $context['cita'];
                    $fecha = date('d/m/Y', strtotime($cita['fecha'] ?? 'now'));
                    $hora = date('H:i', strtotime($cita['hora'] ?? 'now'));
                    $breadcrumb->addItem("Cita del {$fecha} a las {$hora}");
                } else {
                    $breadcrumb->addItem('Ver Cita');
                }
                break;
                
            case 'cita_editar':
                $breadcrumb->addItem('Citas', base_url('/citas'));
                if (isset($context['cita'])) {
                    $cita = $context['cita'];
                    $fecha = date('d/m/Y', strtotime($cita['fecha'] ?? 'now'));
                    $hora = date('H:i', strtotime($cita['hora'] ?? 'now'));
                    $breadcrumb->addItem("Cita del {$fecha} a las {$hora}", 
                        base_url('/citas/ver/' . ($cita['id'] ?? '')));
                }
                $breadcrumb->addItem('Editar');
                break;
                
            case 'calendario':
                $breadcrumb->addItem('Citas', base_url('/citas'));
                $breadcrumb->addItem('Calendario');
                break;
                
            case 'recetas':
                $breadcrumb->addItem('Recetas');
                break;
                
            case 'receta_nueva':
                $breadcrumb->addItem('Recetas', base_url('/recetas'));
                $breadcrumb->addItem('Nueva Receta');
                break;
                
            case 'receta_ver':
                $breadcrumb->addItem('Recetas', base_url('/recetas'));
                if (isset($context['receta'])) {
                    $receta = $context['receta'];
                    $fecha = date('d/m/Y', strtotime($receta['fecha_emision'] ?? 'now'));
                    $breadcrumb->addItem("Receta del {$fecha}");
                } else {
                    $breadcrumb->addItem('Ver Receta');
                }
                break;
                
            case 'cotizaciones':
                $breadcrumb->addItem('Presupuestos');
                break;
                
            case 'cotizacion_nueva':
                $breadcrumb->addItem('Presupuestos', base_url('/cotizaciones'));
                $breadcrumb->addItem('Nuevo Presupuesto');
                break;
                
            case 'cotizacion_ver':
                $breadcrumb->addItem('Presupuestos', base_url('/cotizaciones'));
                if (isset($context['cotizacion'])) {
                    $cotizacion = $context['cotizacion'];
                    $fecha = date('d/m/Y', strtotime($cotizacion['fecha_creacion'] ?? 'now'));
                    $breadcrumb->addItem("Presupuesto #{$cotizacion['id']} del {$fecha}");
                } else {
                    $breadcrumb->addItem('Ver Presupuesto');
                }
                break;
                
            case 'medicamentos':
                $breadcrumb->addItem('Medicamentos');
                break;
                
            case 'medicamento_nuevo':
                $breadcrumb->addItem('Medicamentos', base_url('/medicamentos'));
                $breadcrumb->addItem('Nuevo Medicamento');
                break;
                
            case 'medicamento_editar':
                $breadcrumb->addItem('Medicamentos', base_url('/medicamentos'));
                if (isset($context['medicamento'])) {
                    $breadcrumb->addItem($context['medicamento']['nombre'] ?? 'Medicamento', 
                        base_url('/medicamentos/ver/' . ($context['medicamento']['id'] ?? '')));
                }
                $breadcrumb->addItem('Editar');
                break;
                
            case 'servicios':
                $breadcrumb->addItem('Servicios');
                break;
                
            case 'servicio_nuevo':
                $breadcrumb->addItem('Servicios', base_url('/servicios'));
                $breadcrumb->addItem('Nuevo Servicio');
                break;
                
            case 'servicio_editar':
                $breadcrumb->addItem('Servicios', base_url('/servicios'));
                if (isset($context['servicio'])) {
                    $breadcrumb->addItem($context['servicio']['nombre'] ?? 'Servicio', 
                        base_url('/servicios/ver/' . ($context['servicio']['id'] ?? '')));
                }
                $breadcrumb->addItem('Editar');
                break;
                
            case 'agenda':
                $breadcrumb->addItem('Horario');
                break;
                
            case 'agenda_nueva':
                $breadcrumb->addItem('Horario', base_url('/agenda'));
                $breadcrumb->addItem('Configurar Horario');
                break;
                
            case 'agenda_excepciones':
                $breadcrumb->addItem('Horario', base_url('/agenda'));
                $breadcrumb->addItem('Excepciones');
                break;
                
            case 'agenda_preview':
                $breadcrumb->addItem('Horario', base_url('/agenda'));
                $breadcrumb->addItem('Vista Previa');
                break;
                
            case 'calendario':
                $breadcrumb->addItem('Horario', base_url('/agenda'));
                $breadcrumb->addItem('Calendario');
                break;
                
            case 'ajustes':
                $breadcrumb->addItem('Ajustes');
                break;
                
            case 'notificaciones':
                $breadcrumb->addItem('Notificaciones');
                break;
                
            case 'ayuda':
                $breadcrumb->addItem('Centro de Ayuda');
                break;
                
            case 'configuracion':
                $breadcrumb->addItem('Ajustes');
                break;
                
            case 'configuracion_perfil':
                $breadcrumb->addItem('Ajustes', base_url('/configuracion'));
                $breadcrumb->addItem('Mi Perfil');
                break;
                
            case 'configuracion_sistema':
                $breadcrumb->addItem('Ajustes', base_url('/configuracion'));
                $breadcrumb->addItem('Configuración del Sistema');
                break;
                
            case 'perfil':
                $breadcrumb->addItem('Mi Perfil');
                break;
                
            default:
                // Para páginas no definidas, generar desde URL
                return BreadcrumbHelper::generateFromUrl(current_url(), $context['title'] ?? '');
        }
        
        return $breadcrumb->getItems();
    }
    
    /**
     * Determina la página actual basada en la URL y parámetros
     * 
     * @return string Identificador de la página
     */
    public static function getCurrentPage(): string {
        $request = service('request');
        $uri = $request->uri;
        $segments = $uri->getSegments();
        $method = $request->getMethod();
        
        // Si es la página principal
        if (empty($segments)) {
            return 'dashboard';
        }
        
        $firstSegment = $segments[0] ?? '';
        $secondSegment = $segments[1] ?? '';
        $thirdSegment = $segments[2] ?? '';
        
        // Analizar rutas específicas
        switch ($firstSegment) {
            case 'pacientes':
                if (is_numeric($secondSegment)) {
                    if ($thirdSegment === 'editar' || $method === 'POST') {
                        return 'paciente_editar';
                    } else {
                        return 'paciente_ver';
                    }
                } elseif ($secondSegment === 'nuevo' || $secondSegment === 'crear') {
                    return 'paciente_nuevo';
                }
                return 'pacientes';
                
            case 'citas':
                if ($secondSegment === 'nueva' || $secondSegment === 'crear') {
                    return 'cita_nueva';
                } elseif ($secondSegment === 'calendario') {
                    return 'calendario';
                } elseif (is_numeric($secondSegment)) {
                    if ($thirdSegment === 'editar' || $method === 'POST') {
                        return 'cita_editar';
                    } else {
                        return 'cita_ver';
                    }
                }
                return 'citas';
                
            case 'recetas':
                if ($secondSegment === 'nueva' || $secondSegment === 'crear') {
                    return 'receta_nueva';
                } elseif (is_numeric($secondSegment)) {
                    return 'receta_ver';
                }
                return 'recetas';
                
            case 'cotizaciones':
                if ($secondSegment === 'nueva' || $secondSegment === 'crear') {
                    return 'cotizacion_nueva';
                } elseif (is_numeric($secondSegment)) {
                    return 'cotizacion_ver';
                }
                return 'cotizaciones';
                
            case 'medicamentos':
                if ($secondSegment === 'nuevo' || $secondSegment === 'crear') {
                    return 'medicamento_nuevo';
                } elseif (is_numeric($secondSegment)) {
                    if ($thirdSegment === 'editar' || $method === 'POST') {
                        return 'medicamento_editar';
                    }
                }
                return 'medicamentos';
                
            case 'servicios':
                if ($secondSegment === 'nuevo' || $secondSegment === 'crear') {
                    return 'servicio_nuevo';
                } elseif (is_numeric($secondSegment)) {
                    if ($thirdSegment === 'editar' || $method === 'POST') {
                        return 'servicio_editar';
                    }
                }
                return 'servicios';
                
            case 'agenda':
                return 'agenda';
                
            case 'configuracion':
                if ($secondSegment === 'perfil') {
                    return 'configuracion_perfil';
                } elseif ($secondSegment === 'sistema') {
                    return 'configuracion_sistema';
                }
                return 'configuracion';
                
            case 'perfil':
                return 'perfil';
                
            default:
                return $firstSegment;
        }
    }
    
    /**
     * Obtiene el título de la página actual
     * 
     * @param string $page Identificador de página
     * @param array $context Contexto adicional
     * @return string
     */
    public static function getPageTitle(string $page, array $context = []): string {
        $titles = [
            'dashboard' => 'Dashboard',
            'pacientes' => 'Pacientes',
            'paciente_ver' => 'Detalle del Paciente',
            'paciente_editar' => 'Editar Paciente',
            'paciente_nuevo' => 'Nuevo Paciente',
            'citas' => 'Citas',
            'cita_ver' => 'Detalle de Cita',
            'cita_editar' => 'Editar Cita',
            'cita_nueva' => 'Nueva Cita',
            'calendario' => 'Calendario de Citas',
            'recetas' => 'Recetas',
            'receta_ver' => 'Detalle de Receta',
            'receta_nueva' => 'Nueva Receta',
            'cotizaciones' => 'Presupuestos',
            'cotizacion_ver' => 'Detalle del Presupuesto',
            'cotizacion_nueva' => 'Nuevo Presupuesto',
            'medicamentos' => 'Medicamentos',
            'medicamento_editar' => 'Editar Medicamento',
            'medicamento_nuevo' => 'Nuevo Medicamento',
            'servicios' => 'Servicios',
            'servicio_editar' => 'Editar Servicio',
            'servicio_nuevo' => 'Nuevo Servicio',
            'agenda' => 'Horario de Atención',
            'configuracion' => 'Ajustes',
            'configuracion_perfil' => 'Mi Perfil',
            'configuracion_sistema' => 'Configuración del Sistema',
            'perfil' => 'Mi Perfil'
        ];
        
        $title = $titles[$page] ?? ucfirst($page);
        
        // Aplicar contexto si es necesario
        if (isset($context['entity_name']) && in_array($page, ['paciente_ver', 'cita_ver', 'receta_ver'])) {
            $title = $context['entity_name'];
        }
        
        return $title;
    }
    
    /**
     * Prepara los datos de navegación para una vista
     * 
     * @param string $page Página actual
     * @param array $context Contexto adicional
     * @return array
     */
    public static function prepareNavigationData(string $page = null, array $context = []): array {
        if ($page === null) {
            $page = self::getCurrentPage();
        }
        
        return [
            'currentPage' => $page,
            'pageTitle' => self::getPageTitle($page, $context),
            'breadcrumb' => self::generateBreadcrumbs($page, $context),
            'pageSubtitle' => $context['subtitle'] ?? null
        ];
    }
    
    /**
     * Obtiene elementos de navegación para el menú principal
     * 
     * @return array
     */
    public static function getMainMenuItems(): array {
        return [
            [
                'title' => 'Dashboard',
                'url' => base_url('/'),
                'icon' => '🏠',
                'section' => 'main',
                'active_pages' => ['dashboard']
            ],
            [
                'title' => 'Pacientes',
                'url' => base_url('/pacientes'),
                'icon' => '👥',
                'section' => 'clinical',
                'active_pages' => ['pacientes', 'paciente_ver', 'paciente_editar', 'paciente_nuevo']
            ],
            [
                'title' => 'Citas',
                'url' => base_url('/citas'),
                'icon' => '📅',
                'section' => 'clinical',
                'active_pages' => ['citas', 'cita_ver', 'cita_editar', 'cita_nueva', 'calendario'],
                'submenu' => [
                    ['title' => 'Ver Citas', 'url' => base_url('/citas')],
                    ['title' => 'Calendario', 'url' => base_url('/citas/calendario')],
                    ['title' => 'Nueva Cita', 'url' => base_url('/citas/nueva')]
                ]
            ],
            [
                'title' => 'Recetas',
                'url' => base_url('/recetas'),
                'icon' => '📋',
                'section' => 'clinical',
                'active_pages' => ['recetas', 'receta_ver', 'receta_nueva']
            ],
            [
                'title' => 'Presupuestos',
                'url' => base_url('/cotizaciones'),
                'icon' => '💵',
                'section' => 'admin',
                'active_pages' => ['cotizaciones', 'cotizacion_ver', 'cotizacion_nueva']
            ],
            [
                'title' => 'Horario',
                'url' => base_url('/agenda'),
                'icon' => '🕐',
                'section' => 'admin',
                'active_pages' => ['agenda']
            ],
            [
                'title' => 'Medicamentos',
                'url' => base_url('/medicamentos'),
                'icon' => '💊',
                'section' => 'catalogs',
                'active_pages' => ['medicamentos', 'medicamento_editar', 'medicamento_nuevo']
            ],
            [
                'title' => 'Servicios',
                'url' => base_url('/servicios'),
                'icon' => '⚙️',
                'section' => 'catalogs',
                'active_pages' => ['servicios', 'servicio_editar', 'servicio_nuevo']
            ],
            [
                'title' => 'Ajustes',
                'url' => base_url('/configuracion'),
                'icon' => '⚙️',
                'section' => 'system',
                'active_pages' => ['configuracion', 'configuracion_perfil', 'configuracion_sistema']
            ]
        ];
    }
    
    /**
     * Determina si un item del menú está activo
     * 
     * @param array $menuItem Item del menú
     * @param string $currentPage Página actual
     * @return bool
     */
    public static function isMenuItemActive(array $menuItem, string $currentPage): bool {
        return in_array($currentPage, $menuItem['active_pages'] ?? []);
    }
}
