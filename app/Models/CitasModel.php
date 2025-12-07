<?php

namespace App\Models;

use CodeIgniter\Model;

class CitasModel extends Model
{
    protected $table = 'citas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_paciente', 'id_usuario', 'id_servicio', 'titulo', 'descripcion', 
        'fecha_inicio', 'fecha_fin', 'estado', 'tipo_cita', 'color', 
        'notas', 'recordatorio_enviado'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;

    // Validaciones
    protected $validationRules = [
        'id_paciente' => 'required|integer',
        'id_usuario' => 'required|integer',
        'titulo' => 'required|max_length[255]',
        'fecha_inicio' => 'required',
        'fecha_fin' => 'required',
        'estado' => 'required|in_list[programada,confirmada,en_progreso,completada,cancelada]',
        'tipo_cita' => 'required|in_list[consulta,tratamiento,revision,urgencia]',
        'color' => 'permit_empty|max_length[7]',
    ];

    protected $validationMessages = [
        'id_paciente' => [
            'required' => 'El paciente es obligatorio',
            'integer' => 'El ID del paciente debe ser un número entero',
        ],
        'id_usuario' => [
            'required' => 'El doctor es obligatorio',
            'integer' => 'El ID del doctor debe ser un número entero',
        ],
        'titulo' => [
            'required' => 'El título de la cita es obligatorio',
            'max_length' => 'El título no puede exceder 255 caracteres',
        ],
        'fecha_inicio' => [
            'required' => 'La fecha de inicio es obligatoria',
        ],
        'fecha_fin' => [
            'required' => 'La fecha de fin es obligatoria',
        ],
        'estado' => [
            'required' => 'El estado de la cita es obligatorio',
            'in_list' => 'El estado debe ser: programada, confirmada, en_progreso, completada o cancelada',
        ],
        'tipo_cita' => [
            'required' => 'El tipo de cita es obligatorio',
            'in_list' => 'El tipo debe ser: consulta, tratamiento, revisión o urgencia',
        ],
    ];

    // Colores por tipo de cita
    protected $coloresTipo = [
        'consulta' => '#5ccdde',
        'tratamiento' => '#ff6b6b',
        'revision' => '#4ecdc4',
        'urgencia' => '#ff9f43'
    ];

    // Colores por estado
    protected $coloresEstado = [
        'programada' => '#5ccdde',
        'confirmada' => '#4ecdc4',
        'en_progreso' => '#ff9f43',
        'completada' => '#2ecc71',
        'cancelada' => '#e74c3c'
    ];

    /**
     * Obtener colores por tipo de cita
     */
    public function getColoresPorTipo(): array
    {
        return $this->coloresTipo;
    }

    /**
     * Obtener colores por estado
     */
    public function getColoresPorEstado(): array
    {
        return $this->coloresEstado;
    }

    /**
     * Obtener citas por usuario y rango de fechas
     */
    public function getCitasByUsuarioAndFecha($id_usuario, $fecha_inicio, $fecha_fin)
    {
        return $this->select('citas.*, 
                             pacientes.nombre as paciente_nombre, 
                             pacientes.primer_apellido as paciente_apellido,
                             pacientes.segundo_apellido as paciente_segundo_apellido,
                             pacientes.telefono as paciente_telefono,
                             pacientes.email as paciente_email,
                             usuarios.nombre as doctor_nombre,
                             servicios.nombre as servicio_nombre')
                    ->join('pacientes', 'pacientes.id = citas.id_paciente')
                    ->join('usuarios', 'usuarios.id = citas.id_usuario')
                    ->join('servicios', 'servicios.id = citas.id_servicio', 'left')
                    ->where('citas.id_usuario', $id_usuario)
                    ->where('citas.fecha_inicio >=', $fecha_inicio)
                    ->where('citas.fecha_fin <=', $fecha_fin)
                    ->where('citas.deleted_at IS NULL')
                    ->findAll();
    }

    /**
     * Obtener todas las citas en un rango de fechas
     */
    public function getCitasByRango($fecha_inicio, $fecha_fin, $filtros = [])
    {
        $builder = $this->select('citas.*, 
                                 pacientes.nombre as paciente_nombre, 
                                 pacientes.primer_apellido as paciente_apellido,
                                 pacientes.segundo_apellido as paciente_segundo_apellido,
                                 pacientes.telefono as paciente_telefono,
                                 pacientes.email as paciente_email,
                                 usuarios.nombre as doctor_nombre,
                                 servicios.nombre as servicio_nombre')
                        ->join('pacientes', 'pacientes.id = citas.id_paciente')
                        ->join('usuarios', 'usuarios.id = citas.id_usuario')
                        ->join('servicios', 'servicios.id = citas.id_servicio', 'left')
                        ->where('citas.fecha_inicio >=', $fecha_inicio)
                        ->where('citas.fecha_fin <=', $fecha_fin)
                        ->where('citas.deleted_at IS NULL');
        
        // Aplicar filtros
        if (!empty($filtros['estado'])) {
            $builder->where('citas.estado', $filtros['estado']);
        }
        
        if (!empty($filtros['tipo_cita'])) {
            $builder->where('citas.tipo_cita', $filtros['tipo_cita']);
        }
        
        if (!empty($filtros['id_paciente'])) {
            $builder->where('citas.id_paciente', $filtros['id_paciente']);
        }
        
        if (!empty($filtros['id_usuario'])) {
            $builder->where('citas.id_usuario', $filtros['id_usuario']);
        }
        
        return $builder->orderBy('citas.fecha_inicio', 'ASC')->findAll();
    }

    /**
     * Obtener citas por paciente
     */
    public function getCitasByPaciente($id_paciente)
    {
        return $this->select('citas.*, 
                             usuarios.nombre as doctor_nombre,
                             servicios.nombre as servicio_nombre')
                    ->join('usuarios', 'usuarios.id = citas.id_usuario')
                    ->join('servicios', 'servicios.id = citas.id_servicio', 'left')
                    ->where('citas.id_paciente', $id_paciente)
                    ->where('citas.deleted_at IS NULL')
                    ->orderBy('citas.fecha_inicio', 'DESC')
                    ->findAll();
    }

    /**
     * Obtener cita con todas las relaciones
     */
    public function getCitaWithRelations($id)
    {
        return $this->select('citas.*, 
                             pacientes.nombre as paciente_nombre, 
                             pacientes.primer_apellido as paciente_apellido,
                             pacientes.segundo_apellido as paciente_segundo_apellido,
                             pacientes.telefono as paciente_telefono,
                             pacientes.email as paciente_email,
                             usuarios.nombre as doctor_nombre,
                             servicios.nombre as servicio_nombre,
                             servicios.duracion as servicio_duracion')
                    ->join('pacientes', 'pacientes.id = citas.id_paciente')
                    ->join('usuarios', 'usuarios.id = citas.id_usuario')
                    ->join('servicios', 'servicios.id = citas.id_servicio', 'left')
                    ->where('citas.id', $id)
                    ->first();
    }

    /**
     * Verificar disponibilidad de horario
     */
    public function verificarDisponibilidad($id_usuario, $fecha_inicio, $fecha_fin, $excluir_id = null)
    {
        $builder = $this->where('id_usuario', $id_usuario)
                        ->where('estado !=', 'cancelada')
                        ->where('deleted_at IS NULL')
                        ->groupStart()
                            ->where('fecha_inicio <', $fecha_fin)
                            ->where('fecha_fin >', $fecha_inicio)
                        ->groupEnd();
        
        if ($excluir_id) {
            $builder->where('id !=', $excluir_id);
        }
        
        $conflictos = $builder->findAll();
        
        return empty($conflictos);
    }

    /**
     * Obtener citas conflictivas
     */
    public function getCitasConflictivas($id_usuario, $fecha_inicio, $fecha_fin, $excluir_id = null)
    {
        $builder = $this->select('citas.*, pacientes.nombre as paciente_nombre, pacientes.primer_apellido')
                        ->join('pacientes', 'pacientes.id = citas.id_paciente')
                        ->where('citas.id_usuario', $id_usuario)
                        ->where('citas.estado !=', 'cancelada')
                        ->where('citas.deleted_at IS NULL')
                        ->groupStart()
                            ->where('fecha_inicio <', $fecha_fin)
                            ->where('fecha_fin >', $fecha_inicio)
                        ->groupEnd();
        
        if ($excluir_id) {
            $builder->where('citas.id !=', $excluir_id);
        }
        
        return $builder->findAll();
    }

    /**
     * Cambiar estado de cita
     */
    public function cambiarEstado($id, $nuevo_estado)
    {
        $cita = $this->find($id);
        if (!$cita) {
            return ['success' => false, 'error' => 'Cita no encontrada'];
        }
        
        // Validar transiciones de estado permitidas
        $transiciones = [
            'programada' => ['confirmada', 'cancelada'],
            'confirmada' => ['en_progreso', 'cancelada'],
            'en_progreso' => ['completada', 'cancelada'],
            'completada' => [],
            'cancelada' => ['programada'] // Reactivar cita
        ];
        
        $estado_actual = $cita['estado'];
        
        if (!in_array($nuevo_estado, $transiciones[$estado_actual])) {
            return [
                'success' => false, 
                'error' => "No se puede cambiar de '$estado_actual' a '$nuevo_estado'"
            ];
        }
        
        $this->update($id, ['estado' => $nuevo_estado]);
        
        return ['success' => true, 'estado_anterior' => $estado_actual, 'estado_nuevo' => $nuevo_estado];
    }

    /**
     * Verificar si una cita puede ser editada
     */
    public function puedeEditarse($id, $id_usuario = null, $rol = null)
    {
        $cita = $this->find($id);
        if (!$cita) {
            return false;
        }
        
        // Estados que no permiten edición
        $estados_no_editables = ['completada', 'cancelada'];
        
        // Si es admin o rol superior, puede editar casi todo
        if ($rol >= 3) {
            return !in_array($cita['estado'], $estados_no_editables);
        }
        
        // Si es doctor normal, solo puede editar sus citas y estados no finales
        if ($cita['id_usuario'] != $id_usuario) {
            return false;
        }
        
        return !in_array($cita['estado'], $estados_no_editables);
    }

    /**
     * Obtener citas del día
     */
    public function getCitasDelDia($fecha = null, $id_usuario = null)
    {
        $fecha = $fecha ?? date('Y-m-d');
        $inicio = $fecha . ' 00:00:00';
        $fin = $fecha . ' 23:59:59';
        
        $builder = $this->select('citas.*, 
                                 pacientes.nombre as paciente_nombre, 
                                 pacientes.primer_apellido as paciente_apellido,
                                 servicios.nombre as servicio_nombre')
                        ->join('pacientes', 'pacientes.id = citas.id_paciente')
                        ->join('servicios', 'servicios.id = citas.id_servicio', 'left')
                        ->where('citas.fecha_inicio >=', $inicio)
                        ->where('citas.fecha_inicio <=', $fin)
                        ->where('citas.deleted_at IS NULL');
        
        if ($id_usuario) {
            $builder->where('citas.id_usuario', $id_usuario);
        }
        
        return $builder->orderBy('citas.fecha_inicio', 'ASC')->findAll();
    }

    /**
     * Obtener estadísticas de citas
     */
    public function getEstadisticas($id_usuario = null, $fecha_inicio = null, $fecha_fin = null)
    {
        $fecha_inicio = $fecha_inicio ?? date('Y-m-01');
        $fecha_fin = $fecha_fin ?? date('Y-m-t');
        
        $builder = $this->where('fecha_inicio >=', $fecha_inicio . ' 00:00:00')
                        ->where('fecha_inicio <=', $fecha_fin . ' 23:59:59')
                        ->where('deleted_at IS NULL');
        
        if ($id_usuario) {
            $builder->where('id_usuario', $id_usuario);
        }
        
        $citas = $builder->findAll();
        
        $stats = [
            'total' => count($citas),
            'programadas' => 0,
            'confirmadas' => 0,
            'en_progreso' => 0,
            'completadas' => 0,
            'canceladas' => 0,
            'por_tipo' => [
                'consulta' => 0,
                'tratamiento' => 0,
                'revision' => 0,
                'urgencia' => 0
            ]
        ];
        
        foreach ($citas as $cita) {
            $stats[$cita['estado'] . 's']++;
            if (isset($stats['por_tipo'][$cita['tipo_cita']])) {
                $stats['por_tipo'][$cita['tipo_cita']]++;
            }
        }
        
        return $stats;
    }

    /**
     * Actualizar fechas de cita (para drag & drop)
     */
    public function actualizarFechas($id, $fecha_inicio, $fecha_fin, $id_usuario = null, $rol = null)
    {
        $cita = $this->find($id);
        if (!$cita) {
            return ['success' => false, 'error' => 'Cita no encontrada'];
        }
        
        // Verificar permisos
        if ($rol < 3 && $cita['id_usuario'] != $id_usuario) {
            return ['success' => false, 'error' => 'No tiene permisos para modificar esta cita'];
        }
        
        // Verificar si puede editarse
        if (!$this->puedeEditarse($id, $id_usuario, $rol)) {
            return ['success' => false, 'error' => 'La cita no puede ser modificada en su estado actual'];
        }
        
        // Verificar disponibilidad
        if (!$this->verificarDisponibilidad($cita['id_usuario'], $fecha_inicio, $fecha_fin, $id)) {
            return ['success' => false, 'error' => 'El horario seleccionado no está disponible'];
        }
        
        $this->update($id, [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
        
        return ['success' => true];
    }
}
