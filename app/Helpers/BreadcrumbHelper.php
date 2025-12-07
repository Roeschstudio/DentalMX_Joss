<?php

namespace App\Helpers;

class BreadcrumbHelper {
    private array $items = [];
    
    public function __construct() {
        // Siempre agregar Dashboard como primer elemento
        $this->addItem('Dashboard', base_url('/'));
    }
    
    /**
     * Agrega un item al breadcrumb
     * @param string $title Título del item
     * @param string|null $url URL del item (null para item actual)
     * @return self
     */
    public function addItem(string $title, ?string $url = null): self {
        $this->items[] = [
            'title' => $title,
            'url' => $url,
            'active' => is_null($url)
        ];
        return $this;
    }
    
    /**
     * Genera breadcrumbs automáticamente basado en la URL actual
     * @param string $currentUrl URL actual
     * @param string $currentPage Título de la página actual
     * @return array
     */
    public static function generateFromUrl(string $currentUrl, string $currentPage = ''): array {
        $breadcrumb = new self();
        
        // Mapeo de URLs a títulos
        $urlMap = [
            'pacientes' => 'Pacientes',
            'citas' => 'Citas',
            'recetas' => 'Recetas',
            'cotizaciones' => 'Presupuestos',
            'medicamentos' => 'Medicamentos',
            'servicios' => 'Servicios',
            'configuracion' => 'Ajustes',
            'agenda' => 'Horario',
            'calendario' => 'Calendario'
        ];
        
        // Extraer segmentos de la URL
        $segments = explode('/', trim($currentUrl, '/'));
        $accumulatedPath = '';
        
        foreach ($segments as $segment) {
            if (empty($segment)) continue;
            
            $accumulatedPath .= '/' . $segment;
            $title = $urlMap[$segment] ?? ucfirst($segment);
            
            // Si es el último segmento, marcar como activo
            if ($segment === end($segments)) {
                $breadcrumb->addItem($currentPage ?: $title);
            } else {
                $breadcrumb->addItem($title, base_url($accumulatedPath));
            }
        }
        
        return $breadcrumb->getItems();
    }
    
    /**
     * Obtiene todos los items del breadcrumb
     * @return array
     */
    public function getItems(): array {
        return $this->items;
    }
    
    /**
     * Renderiza el breadcrumb como HTML
     * @return string
     */
    public function render(): string {
        $html = '<nav aria-label="breadcrumb">';
        $html .= '<ol class="ds-breadcrumb">';
        
        foreach ($this->items as $index => $item) {
            $html .= '<li class="ds-breadcrumb__item">';
            
            if ($item['active']) {
                $html .= '<span class="ds-breadcrumb__current">' . esc($item['title']) . '</span>';
            } else {
                $html .= '<a href="' . esc($item['url']) . '" class="ds-breadcrumb__link">' . esc($item['title']) . '</a>';
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ol>';
        $html .= '</nav>';
        
        return $html;
    }
    
    /**
     * Crea una instancia para una página específica
     * @param string $page Página actual
     * @param array $context Contexto adicional (ej: ID de paciente)
     * @return self
     */
    public static function forPage(string $page, array $context = []): self {
        $breadcrumb = new self();
        
        switch ($page) {
            case 'pacientes':
                $breadcrumb->addItem('Pacientes');
                break;
                
            case 'paciente_detalle':
                $breadcrumb->addItem('Pacientes', base_url('/pacientes'));
                if (isset($context['paciente_nombre'])) {
                    $breadcrumb->addItem($context['paciente_nombre']);
                }
                break;
                
            case 'paciente_editar':
                $breadcrumb->addItem('Pacientes', base_url('/pacientes'));
                if (isset($context['paciente_nombre'])) {
                    $breadcrumb->addItem($context['paciente_nombre'], base_url('/pacientes/ver/' . ($context['paciente_id'] ?? '')));
                }
                $breadcrumb->addItem('Editar');
                break;
                
            case 'citas':
                $breadcrumb->addItem('Citas');
                break;
                
            case 'cita_nueva':
                $breadcrumb->addItem('Citas', base_url('/citas'));
                $breadcrumb->addItem('Nueva Cita');
                break;
                
            case 'recetas':
                $breadcrumb->addItem('Recetas');
                break;
                
            case 'receta_nueva':
                $breadcrumb->addItem('Recetas', base_url('/recetas'));
                $breadcrumb->addItem('Nueva Receta');
                break;
                
            case 'cotizaciones':
                $breadcrumb->addItem('Presupuestos');
                break;
                
            case 'configuracion':
                $breadcrumb->addItem('Ajustes');
                break;
                
            default:
                $breadcrumb->addItem(ucfirst($page));
                break;
        }
        
        return $breadcrumb;
    }
}
