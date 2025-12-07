<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar los tratamientos realizados a los pacientes
 * 
 * Permite el seguimiento detallado de tratamientos incluyendo:
 * - Estado del tratamiento
 * - Costos y pagos
 * - Dientes y superficies tratadas
 * - Fechas de inicio y fin
 */
class TratamientosRealizadosModel extends Model
{
    protected $table = 'tratamientos_realizados';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_paciente', 
        'id_servicio', 
        'id_usuario', 
        'diente', 
        'superficie',
        'estado', 
        'fecha_inicio', 
        'fecha_fin', 
        'observaciones', 
        'costo', 
        'pagado'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'id_paciente' => 'required|integer|greater_than[0]',
        'id_servicio' => 'required|integer|greater_than[0]',
        'id_usuario' => 'required|integer|greater_than[0]',
        'diente' => 'permit_empty|string|max_length[5]',
        'superficie' => 'permit_empty|in_list[vestibular,lingual,oclusal,mesial,distal,incisal,palatino,bucal]',
        'estado' => 'required|in_list[iniciado,en_progreso,completado,suspendido,cancelado]',
        'fecha_inicio' => 'required|valid_date[Y-m-d]',
        'fecha_fin' => 'permit_empty|valid_date[Y-m-d]',
        'costo' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'pagado' => 'permit_empty|decimal|greater_than_equal_to[0]'
    ];
    
    protected $validationMessages = [
        'id_paciente' => [
            'required' => 'El ID del paciente es requerido',
            'integer' => 'El ID del paciente debe ser un número entero',
            'greater_than' => 'El ID del paciente debe ser mayor que 0'
        ],
        'id_servicio' => [
            'required' => 'El ID del servicio es requerido',
            'integer' => 'El ID del servicio debe ser un número entero',
            'greater_than' => 'El ID del servicio debe ser mayor que 0'
        ],
        'estado' => [
            'required' => 'El estado es requerido',
            'in_list' => 'Estado no válido'
        ],
        'fecha_inicio' => [
            'required' => 'La fecha de inicio es requerida',
            'valid_date' => 'Formato de fecha inválido'
        ]
    ];

    /**
     * Estados de tratamiento con configuración visual
     */
    public static array $estados = [
        'iniciado' => [
            'label' => 'Iniciado',
            'icon' => '🔵',
            'color' => 'info',
            'class' => 'ds-badge--info'
        ],
        'en_progreso' => [
            'label' => 'En Progreso',
            'icon' => '🟡',
            'color' => 'warning',
            'class' => 'ds-badge--warning'
        ],
        'completado' => [
            'label' => 'Completado',
            'icon' => '🟢',
            'color' => 'success',
            'class' => 'ds-badge--success'
        ],
        'suspendido' => [
            'label' => 'Suspendido',
            'icon' => '🟠',
            'color' => 'secondary',
            'class' => 'ds-badge--secondary'
        ],
        'cancelado' => [
            'label' => 'Cancelado',
            'icon' => '🔴',
            'color' => 'danger',
            'class' => 'ds-badge--danger'
        ]
    ];

    /**
     * Superficies dentales disponibles
     */
    public static array $superficies = [
        'vestibular' => 'Vestibular',
        'lingual' => 'Lingual',
        'oclusal' => 'Oclusal',
        'mesial' => 'Mesial',
        'distal' => 'Distal',
        'incisal' => 'Incisal',
        'palatino' => 'Palatino',
        'bucal' => 'Bucal'
    ];

    /**
     * Obtener tratamientos de un paciente
     */
    public function getTratamientosByPaciente(int $id_paciente, ?string $estado = null, int $limit = 50): array
    {
        $builder = $this->builder();
        
        $builder->select('tratamientos_realizados.*, 
                        servicios.nombre as servicio_nombre,
                        servicios.descripcion as servicio_descripcion,
                        servicios.precio_base as servicio_precio,
                        usuarios.nombre as medico_nombre,
                        usuarios.primer_apellido as medico_apellido')
                ->join('servicios', 'servicios.id = tratamientos_realizados.id_servicio')
                ->join('usuarios', 'usuarios.id = tratamientos_realizados.id_usuario')
                ->where('tratamientos_realizados.id_paciente', $id_paciente);
        
        if ($estado) {
            $builder->where('tratamientos_realizados.estado', $estado);
        }
        
        $builder->orderBy('tratamientos_realizados.fecha_inicio', 'DESC')
                ->limit($limit);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener tratamiento con detalles completos
     */
    public function getTratamientoConDetalles(int $id_tratamiento): ?array
    {
        $builder = $this->builder();
        
        $builder->select('tratamientos_realizados.*, 
                        servicios.nombre as servicio_nombre,
                        servicios.descripcion as servicio_descripcion,
                        servicios.precio_base as servicio_precio,
                        usuarios.nombre as medico_nombre,
                        usuarios.primer_apellido as medico_apellido,
                        pacientes.nombre as paciente_nombre,
                        pacientes.primer_apellido as paciente_apellido')
                ->join('servicios', 'servicios.id = tratamientos_realizados.id_servicio')
                ->join('usuarios', 'usuarios.id = tratamientos_realizados.id_usuario')
                ->join('pacientes', 'pacientes.id = tratamientos_realizados.id_paciente')
                ->where('tratamientos_realizados.id', $id_tratamiento);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Obtener tratamientos activos de un paciente
     */
    public function getTratamientosActivos(int $id_paciente): array
    {
        return $this->select('tratamientos_realizados.*, 
                            servicios.nombre as servicio_nombre,
                            servicios.precio_base as servicio_precio')
                    ->join('servicios', 'servicios.id = tratamientos_realizados.id_servicio')
                    ->where('tratamientos_realizados.id_paciente', $id_paciente)
                    ->whereIn('tratamientos_realizados.estado', ['iniciado', 'en_progreso'])
                    ->orderBy('tratamientos_realizados.fecha_inicio', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener tratamientos pendientes (no completados ni cancelados)
     */
    public function getTratamientosPendientes(int $id_paciente): array
    {
        return $this->select('tratamientos_realizados.*, 
                            servicios.nombre as servicio_nombre,
                            servicios.precio_base as servicio_precio')
                    ->join('servicios', 'servicios.id = tratamientos_realizados.id_servicio')
                    ->where('tratamientos_realizados.id_paciente', $id_paciente)
                    ->whereNotIn('tratamientos_realizados.estado', ['completado', 'cancelado'])
                    ->orderBy('tratamientos_realizados.fecha_inicio', 'ASC')
                    ->findAll();
    }

    /**
     * Actualizar estado de un tratamiento
     */
    public function actualizarEstado(int $id_tratamiento, string $nuevo_estado, ?string $fecha_fin = null): bool
    {
        $data = ['estado' => $nuevo_estado];
        
        if ($nuevo_estado === 'completado' && $fecha_fin) {
            $data['fecha_fin'] = $fecha_fin;
        } elseif ($nuevo_estado === 'completado' && !$fecha_fin) {
            $data['fecha_fin'] = date('Y-m-d');
        }
        
        return $this->update($id_tratamiento, $data);
    }

    /**
     * Registrar pago parcial o total
     */
    public function registrarPago(int $id_tratamiento, float $monto): bool
    {
        $tratamiento = $this->find($id_tratamiento);
        
        if (!$tratamiento) {
            return false;
        }
        
        $nuevo_pagado = (float)$tratamiento['pagado'] + $monto;
        
        return $this->update($id_tratamiento, ['pagado' => $nuevo_pagado]);
    }

    /**
     * Obtener resumen financiero de un paciente
     */
    public function getResumenFinanciero(int $id_paciente): array
    {
        $builder = $this->builder();
        
        $builder->select('
                        SUM(costo) as costo_total,
                        SUM(pagado) as total_pagado,
                        SUM(costo - pagado) as saldo_pendiente,
                        COUNT(*) as total_tratamientos,
                        SUM(CASE WHEN estado = "completado" THEN 1 ELSE 0 END) as completados,
                        SUM(CASE WHEN estado IN ("iniciado", "en_progreso") THEN 1 ELSE 0 END) as activos')
                ->where('id_paciente', $id_paciente)
                ->where('estado !=', 'cancelado');
        
        $resultado = $builder->get()->getRowArray();
        
        return [
            'costo_total' => (float)($resultado['costo_total'] ?? 0),
            'total_pagado' => (float)($resultado['total_pagado'] ?? 0),
            'saldo_pendiente' => (float)($resultado['saldo_pendiente'] ?? 0),
            'total_tratamientos' => (int)($resultado['total_tratamientos'] ?? 0),
            'completados' => (int)($resultado['completados'] ?? 0),
            'activos' => (int)($resultado['activos'] ?? 0),
            'porcentaje_pagado' => $resultado['costo_total'] > 0 
                ? round(($resultado['total_pagado'] / $resultado['costo_total']) * 100, 2) 
                : 0
        ];
    }

    /**
     * Obtener estadísticas de tratamientos
     */
    public function getEstadisticas(int $id_paciente): array
    {
        $builder = $this->builder();
        
        $builder->select('estado, COUNT(*) as total')
                ->where('id_paciente', $id_paciente)
                ->groupBy('estado');
        
        $porEstado = $builder->get()->getResultArray();
        
        // Tratamientos por servicio
        $porServicio = $this->builder()
                           ->select('servicios.nombre as servicio, COUNT(*) as total')
                           ->join('servicios', 'servicios.id = tratamientos_realizados.id_servicio')
                           ->where('tratamientos_realizados.id_paciente', $id_paciente)
                           ->groupBy('servicios.id')
                           ->orderBy('total', 'DESC')
                           ->limit(5)
                           ->get()
                           ->getResultArray();
        
        return [
            'por_estado' => $porEstado,
            'por_servicio' => $porServicio
        ];
    }

    /**
     * Obtener tratamientos por diente
     */
    public function getTratamientosPorDiente(int $id_paciente, string $diente): array
    {
        return $this->select('tratamientos_realizados.*, servicios.nombre as servicio_nombre')
                    ->join('servicios', 'servicios.id = tratamientos_realizados.id_servicio')
                    ->where('tratamientos_realizados.id_paciente', $id_paciente)
                    ->where('tratamientos_realizados.diente', $diente)
                    ->orderBy('tratamientos_realizados.fecha_inicio', 'DESC')
                    ->findAll();
    }

    /**
     * Crear nuevo tratamiento y registrar en historial
     */
    public function crearTratamiento(array $data): int|bool
    {
        // Agregar usuario actual si no está especificado
        if (!isset($data['id_usuario'])) {
            $data['id_usuario'] = session()->get('id') ?? session()->get('usuario_id') ?? 1;
        }
        
        // Agregar fecha de inicio si no está especificada
        if (!isset($data['fecha_inicio'])) {
            $data['fecha_inicio'] = date('Y-m-d');
        }
        
        // Agregar estado inicial si no está especificado
        if (!isset($data['estado'])) {
            $data['estado'] = 'iniciado';
        }
        
        if ($this->insert($data)) {
            $id_tratamiento = $this->getInsertID();
            
            // Registrar en historial de actividades
            $historialModel = new HistorialActividadesModel();
            $servicio = (new ServiciosModel())->find($data['id_servicio']);
            
            $descripcion = 'Tratamiento iniciado: ' . ($servicio['nombre'] ?? 'Sin nombre');
            if (!empty($data['diente'])) {
                $descripcion .= ' - Diente: ' . $data['diente'];
            }
            
            $historialModel->registrarActividad(
                $data['id_paciente'],
                'tratamiento',
                $id_tratamiento,
                $descripcion
            );
            
            return $id_tratamiento;
        }
        
        return false;
    }

    /**
     * Obtener configuración visual del estado
     */
    public static function getEstado(string $estado): array
    {
        return self::$estados[$estado] ?? [
            'label' => ucfirst($estado),
            'icon' => '⚪',
            'color' => 'secondary',
            'class' => 'ds-badge--secondary'
        ];
    }

    /**
     * Obtener tratamientos para calendario
     */
    public function getTratamientosParaCalendario(int $id_paciente, string $fecha_inicio, string $fecha_fin): array
    {
        return $this->select('tratamientos_realizados.*, servicios.nombre as servicio_nombre')
                    ->join('servicios', 'servicios.id = tratamientos_realizados.id_servicio')
                    ->where('tratamientos_realizados.id_paciente', $id_paciente)
                    ->where('tratamientos_realizados.fecha_inicio >=', $fecha_inicio)
                    ->where('tratamientos_realizados.fecha_inicio <=', $fecha_fin)
                    ->orderBy('tratamientos_realizados.fecha_inicio', 'ASC')
                    ->findAll();
    }
}
