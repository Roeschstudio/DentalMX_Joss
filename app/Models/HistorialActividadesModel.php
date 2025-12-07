<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar el historial de actividades de los pacientes
 * 
 * Este modelo registra todas las actividades relacionadas con un paciente:
 * - Citas
 * - Recetas
 * - Presupuestos
 * - Cotizaciones
 * - Notas de evolución
 * - Tratamientos
 * - Pagos
 * - Odontogramas
 */
class HistorialActividadesModel extends Model
{
    protected $table = 'historial_actividades';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_paciente', 
        'id_usuario', 
        'tipo_actividad', 
        'id_referencia', 
        'descripcion', 
        'fecha_actividad'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'id_paciente' => 'required|integer|greater_than[0]',
        'id_usuario' => 'required|integer|greater_than[0]',
        'tipo_actividad' => 'required|in_list[cita,receta,presupuesto,cotizacion,nota_evolucion,tratamiento,pago,odontograma]',
        'id_referencia' => 'required|integer|greater_than[0]',
        'fecha_actividad' => 'required|valid_date[Y-m-d H:i:s]'
    ];
    
    protected $validationMessages = [
        'id_paciente' => [
            'required' => 'El ID del paciente es requerido',
            'integer' => 'El ID del paciente debe ser un número entero',
            'greater_than' => 'El ID del paciente debe ser mayor que 0'
        ],
        'id_usuario' => [
            'required' => 'El ID del usuario es requerido',
            'integer' => 'El ID del usuario debe ser un número entero',
            'greater_than' => 'El ID del usuario debe ser mayor que 0'
        ],
        'tipo_actividad' => [
            'required' => 'El tipo de actividad es requerido',
            'in_list' => 'Tipo de actividad no válido'
        ],
        'id_referencia' => [
            'required' => 'El ID de referencia es requerido',
            'integer' => 'El ID de referencia debe ser un número entero',
            'greater_than' => 'El ID de referencia debe ser mayor que 0'
        ],
        'fecha_actividad' => [
            'required' => 'La fecha de actividad es requerida',
            'valid_date' => 'La fecha de actividad debe tener un formato válido (YYYY-MM-DD HH:MM:SS)'
        ]
    ];

    /**
     * Tipos de actividad disponibles con su configuración
     */
    public static array $tiposActividad = [
        'cita' => [
            'label' => 'Cita',
            'icon' => '📅',
            'color' => 'primary',
            'class' => 'ds-badge--primary'
        ],
        'receta' => [
            'label' => 'Receta',
            'icon' => '💊',
            'color' => 'info',
            'class' => 'ds-badge--info'
        ],
        'presupuesto' => [
            'label' => 'Presupuesto',
            'icon' => '💰',
            'color' => 'warning',
            'class' => 'ds-badge--warning'
        ],
        'cotizacion' => [
            'label' => 'Cotización',
            'icon' => '📋',
            'color' => 'secondary',
            'class' => 'ds-badge--secondary'
        ],
        'nota_evolucion' => [
            'label' => 'Nota de Evolución',
            'icon' => '📝',
            'color' => 'success',
            'class' => 'ds-badge--success'
        ],
        'tratamiento' => [
            'label' => 'Tratamiento',
            'icon' => '🦷',
            'color' => 'primary',
            'class' => 'ds-badge--primary'
        ],
        'pago' => [
            'label' => 'Pago',
            'icon' => '💵',
            'color' => 'success',
            'class' => 'ds-badge--success'
        ],
        'odontograma' => [
            'label' => 'Odontograma',
            'icon' => '🦷',
            'color' => 'info',
            'class' => 'ds-badge--info'
        ]
    ];

    /**
     * Obtener timeline completo de un paciente
     */
    public function getTimelineByPaciente(int $id_paciente, int $limit = 50, int $offset = 0, array $filtros = []): array
    {
        $builder = $this->builder();
        
        $builder->select('historial_actividades.*, 
                        usuarios.nombre as medico_nombre, 
                        usuarios.email as medico_email')
                ->join('usuarios', 'usuarios.id = historial_actividades.id_usuario')
                ->where('historial_actividades.id_paciente', $id_paciente);
        
        // Aplicar filtros
        if (!empty($filtros['tipo_actividad'])) {
            if (is_array($filtros['tipo_actividad'])) {
                $builder->whereIn('historial_actividades.tipo_actividad', $filtros['tipo_actividad']);
            } else {
                $builder->where('historial_actividades.tipo_actividad', $filtros['tipo_actividad']);
            }
        }
        
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('historial_actividades.fecha_actividad >=', $filtros['fecha_inicio']);
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('historial_actividades.fecha_actividad <=', $filtros['fecha_fin']);
        }
        
        if (!empty($filtros['busqueda'])) {
            $builder->like('historial_actividades.descripcion', $filtros['busqueda']);
        }
        
        $builder->orderBy('historial_actividades.fecha_actividad', 'DESC')
                ->limit($limit, $offset);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Contar total de actividades (para paginación)
     */
    public function countTimelineByPaciente(int $id_paciente, array $filtros = []): int
    {
        $builder = $this->builder();
        
        $builder->where('id_paciente', $id_paciente);
        
        if (!empty($filtros['tipo_actividad'])) {
            if (is_array($filtros['tipo_actividad'])) {
                $builder->whereIn('tipo_actividad', $filtros['tipo_actividad']);
            } else {
                $builder->where('tipo_actividad', $filtros['tipo_actividad']);
            }
        }
        
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('fecha_actividad >=', $filtros['fecha_inicio']);
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('fecha_actividad <=', $filtros['fecha_fin']);
        }
        
        if (!empty($filtros['busqueda'])) {
            $builder->like('descripcion', $filtros['busqueda']);
        }
        
        return $builder->countAllResults();
    }

    /**
     * Obtener detalles de una actividad específica
     */
    public function getDetallesActividad(int $id_actividad): ?array
    {
        $builder = $this->builder();
        
        $builder->select('historial_actividades.*, 
                        usuarios.nombre as medico_nombre,
                        pacientes.nombre as paciente_nombre, 
                        pacientes.primer_apellido as paciente_apellido')
                ->join('usuarios', 'usuarios.id = historial_actividades.id_usuario')
                ->join('pacientes', 'pacientes.id = historial_actividades.id_paciente')
                ->where('historial_actividades.id', $id_actividad);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Obtener datos adicionales según tipo de actividad
     */
    public function getDatosActividad(string $tipo_actividad, int $id_referencia): ?array
    {
        switch ($tipo_actividad) {
            case 'cita':
                return $this->getCitaConDetalles($id_referencia);
                
            case 'receta':
                $recetaModel = new RecetasModel();
                return $recetaModel->find($id_referencia);
                
            case 'presupuesto':
            case 'cotizacion':
                $presupuestoModel = new PresupuestosModel();
                return $presupuestoModel->find($id_referencia);
                
            case 'nota_evolucion':
                $notasModel = new NotasEvolucionModel();
                return $notasModel->find($id_referencia);
                
            case 'tratamiento':
                $tratamientosModel = new TratamientosRealizadosModel();
                return $tratamientosModel->getTratamientoConDetalles($id_referencia);
                
            case 'pago':
                return $this->getPagoConDetalles($id_referencia);
                
            case 'odontograma':
                return $this->getOdontogramaConDetalles($id_referencia);
                
            default:
                return null;
        }
    }

    /**
     * Obtener cita con detalles
     */
    private function getCitaConDetalles(int $id_cita): ?array
    {
        $db = \Config\Database::connect();
        $cita = $db->table('citas')
                    ->where('id', $id_cita)
                    ->get()
                    ->getRowArray();
        
        if ($cita) {
            // Obtener información del paciente
            $paciente = $db->table('pacientes')
                          ->where('id', $cita['paciente_id'])
                          ->get()
                          ->getRowArray();
            
            // Obtener información del servicio
            $servicio = $db->table('servicios')
                          ->where('id', $cita['servicio_id'])
                          ->get()
                          ->getRowArray();
            
            $cita['paciente_nombre'] = $paciente['nombre'] ?? '';
            $cita['paciente_apellido'] = $paciente['primer_apellido'] ?? '';
            $cita['servicio_nombre'] = $servicio['nombre'] ?? '';
        }
        
        return $cita;
    }

    /**
     * Obtener pago con detalles (provisional)
     */
    private function getPagoConDetalles(int $id_pago): ?array
    {
        // Por ahora retornar datos básicos, ya que no hay tabla de pagos separada
        return [
            'id' => $id_pago,
            'tipo' => 'pago',
            'mensaje' => 'Módulo de pagos pendiente de implementación'
        ];
    }

    /**
     * Obtener odontograma con detalles
     */
    private function getOdontogramaConDetalles(int $id_odontograma): ?array
    {
        $db = \Config\Database::connect();
        return $db->table('historial_bucodental')
                    ->where('id', $id_odontograma)
                    ->get()
                    ->getRowArray();
    }

    /**
     * Registrar nueva actividad en el historial
     */
    public function registrarActividad(
        int $id_paciente, 
        string $tipo_actividad, 
        int $id_referencia, 
        ?string $descripcion = null, 
        ?string $fecha_actividad = null
    ): int|bool
    {
        $id_usuario = session()->get('id') ?? session()->get('usuario_id') ?? 1;
        
        $data = [
            'id_paciente' => $id_paciente,
            'id_usuario' => $id_usuario,
            'tipo_actividad' => $tipo_actividad,
            'id_referencia' => $id_referencia,
            'descripcion' => $descripcion,
            'fecha_actividad' => $fecha_actividad ?: date('Y-m-d H:i:s')
        ];
        
        if ($this->insert($data)) {
            return $this->getInsertID();
        }
        
        return false;
    }

    /**
     * Obtener actividades por tipo
     */
    public function getActividadesPorTipo(int $id_paciente, string $tipo_actividad, int $limit = 20): array
    {
        return $this->select('historial_actividades.*, usuarios.nombre as medico_nombre')
                    ->join('usuarios', 'usuarios.id = historial_actividades.id_usuario')
                    ->where('historial_actividades.id_paciente', $id_paciente)
                    ->where('historial_actividades.tipo_actividad', $tipo_actividad)
                    ->orderBy('historial_actividades.fecha_actividad', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Obtener estadísticas de actividades
     */
    public function getEstadisticas(int $id_paciente, ?string $fecha_inicio = null, ?string $fecha_fin = null): array
    {
        $builder = $this->builder();
        
        $builder->select('tipo_actividad, COUNT(*) as total, MAX(fecha_actividad) as ultima_fecha')
                ->where('id_paciente', $id_paciente);
        
        if ($fecha_inicio) {
            $builder->where('fecha_actividad >=', $fecha_inicio);
        }
        
        if ($fecha_fin) {
            $builder->where('fecha_actividad <=', $fecha_fin);
        }
        
        $builder->groupBy('tipo_actividad')
                ->orderBy('total', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Buscar actividades en el historial
     */
    public function buscarActividades(int $id_paciente, string $termino, int $limit = 20): array
    {
        return $this->select('historial_actividades.*, usuarios.nombre as medico_nombre')
                    ->join('usuarios', 'usuarios.id = historial_actividades.id_usuario')
                    ->where('historial_actividades.id_paciente', $id_paciente)
                    ->groupStart()
                    ->like('historial_actividades.descripcion', $termino)
                    ->orLike('historial_actividades.tipo_actividad', $termino)
                    ->groupEnd()
                    ->orderBy('historial_actividades.fecha_actividad', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Obtener actividades recientes (últimos N días)
     */
    public function getActividadesRecientes(int $id_paciente, int $dias = 7): array
    {
        $fecha_limite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        
        return $this->select('historial_actividades.*, usuarios.nombre as medico_nombre')
                    ->join('usuarios', 'usuarios.id = historial_actividades.id_usuario')
                    ->where('historial_actividades.id_paciente', $id_paciente)
                    ->where('historial_actividades.fecha_actividad >=', $fecha_limite)
                    ->orderBy('historial_actividades.fecha_actividad', 'DESC')
                    ->findAll();
    }

    /**
     * Eliminar actividad y sus adjuntos
     */
    public function eliminarActividad(int $id_actividad): bool
    {
        // Eliminar adjuntos primero (la FK debería manejar esto automáticamente con CASCADE)
        $adjuntosModel = new HistorialAdjuntosModel();
        $adjuntosModel->where('id_historial_actividad', $id_actividad)->delete();
        
        // Eliminar actividad
        return $this->delete($id_actividad);
    }

    /**
     * Obtener resumen de actividades para dashboard del paciente
     */
    public function getResumenDashboard(int $id_paciente): array
    {
        // Últimas 5 actividades
        $ultimas = $this->select('historial_actividades.*, usuarios.nombre as medico_nombre')
                        ->join('usuarios', 'usuarios.id = historial_actividades.id_usuario')
                        ->where('historial_actividades.id_paciente', $id_paciente)
                        ->orderBy('historial_actividades.fecha_actividad', 'DESC')
                        ->limit(5)
                        ->findAll();
        
        // Conteo por tipo
        $conteo = $this->builder()
                       ->select('tipo_actividad, COUNT(*) as total')
                       ->where('id_paciente', $id_paciente)
                       ->groupBy('tipo_actividad')
                       ->get()
                       ->getResultArray();
        
        // Estadísticas generales
        $totalActividades = array_sum(array_column($conteo, 'total'));
        
        return [
            'ultimas_actividades' => $ultimas,
            'conteo_por_tipo' => $conteo,
            'total_actividades' => $totalActividades
        ];
    }

    /**
     * Validar que la actividad exista y pertenezca al paciente
     */
    public function validarAccesoActividad(int $id_actividad, int $id_paciente): bool
    {
        $actividad = $this->where('id', $id_actividad)
                        ->where('id_paciente', $id_paciente)
                        ->first();
        
        return $actividad !== null;
    }

    /**
     * Obtener actividades para exportación
     */
    public function getActividadesParaExportar(
        int $id_paciente, 
        ?string $fecha_inicio = null, 
        ?string $fecha_fin = null, 
        array $tipos = []
    ): array
    {
        $builder = $this->builder();
        
        $builder->select('historial_actividades.*, 
                        usuarios.nombre as medico_nombre')
                ->join('usuarios', 'usuarios.id = historial_actividades.id_usuario')
                ->where('historial_actividades.id_paciente', $id_paciente);
        
        if ($fecha_inicio) {
            $builder->where('historial_actividades.fecha_actividad >=', $fecha_inicio);
        }
        
        if ($fecha_fin) {
            $builder->where('historial_actividades.fecha_actividad <=', $fecha_fin);
        }
        
        if (!empty($tipos)) {
            $builder->whereIn('historial_actividades.tipo_actividad', $tipos);
        }
        
        $builder->orderBy('historial_actividades.fecha_actividad', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener configuración del tipo de actividad
     */
    public static function getTipoActividad(string $tipo): array
    {
        return self::$tiposActividad[$tipo] ?? [
            'label' => ucfirst($tipo),
            'icon' => '📌',
            'color' => 'secondary',
            'class' => 'ds-badge--secondary'
        ];
    }
}
