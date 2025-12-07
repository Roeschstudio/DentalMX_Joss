<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar excepciones de horario
 * 
 * Maneja días no disponibles, vacaciones, etc.
 */
class DoctorExceptionModel extends Model
{
    protected $table            = 'doctor_exceptions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'usuario_id',
        'fecha',
        'motivo',
        'todo_el_dia',
        'hora_inicio',
        'hora_fin'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'usuario_id'   => 'required|integer',
        'fecha'        => 'required|valid_date[Y-m-d]',
        'todo_el_dia'  => 'in_list[0,1]',
    ];

    /**
     * Verificar si existe excepción para una fecha
     */
    public function tieneExcepcion(int $usuarioId, string $fecha): bool
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('fecha', $fecha)
                    ->countAllResults() > 0;
    }

    /**
     * Obtener excepción para una fecha específica
     */
    public function getExcepcion(int $usuarioId, string $fecha): ?array
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('fecha', $fecha)
                    ->first();
    }

    /**
     * Obtener excepciones en un rango de fechas
     */
    public function getExcepcionesEnRango(int $usuarioId, string $fechaInicio, string $fechaFin): array
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('fecha >=', $fechaInicio)
                    ->where('fecha <=', $fechaFin)
                    ->orderBy('fecha', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener todas las excepciones de un doctor
     */
    public function getExcepcionesDoctor(int $usuarioId): array
    {
        return $this->where('usuario_id', $usuarioId)
                    ->orderBy('fecha', 'DESC')
                    ->findAll();
    }

    /**
     * Obtener excepciones futuras
     */
    public function getExcepcionesFuturas(int $usuarioId): array
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('fecha >=', date('Y-m-d'))
                    ->orderBy('fecha', 'ASC')
                    ->findAll();
    }

    /**
     * Verificar si el doctor no está disponible por excepción en fecha/hora específicas
     */
    public function noDisponiblePorExcepcion(int $usuarioId, string $fecha, ?string $hora = null): bool
    {
        $excepcion = $this->getExcepcion($usuarioId, $fecha);

        if (!$excepcion) {
            return false;
        }

        // Si es todo el día, no está disponible
        if ($excepcion['todo_el_dia']) {
            return true;
        }

        // Si es parcial y se proporciona hora, verificar el rango
        if ($hora !== null && !$excepcion['todo_el_dia']) {
            $horaCheck = strtotime($hora);
            $horaInicio = strtotime($excepcion['hora_inicio']);
            $horaFin = strtotime($excepcion['hora_fin']);

            return $horaCheck >= $horaInicio && $horaCheck < $horaFin;
        }

        return false;
    }

    /**
     * Agregar excepción
     */
    public function agregarExcepcion(int $usuarioId, array $datos): bool
    {
        $data = [
            'usuario_id'   => $usuarioId,
            'fecha'        => $datos['fecha'],
            'motivo'       => $datos['motivo'] ?? null,
            'todo_el_dia'  => isset($datos['todo_el_dia']) ? 1 : 0,
        ];

        // Si no es todo el día, agregar horas
        if (!$data['todo_el_dia']) {
            $data['hora_inicio'] = $datos['hora_inicio'] ?? null;
            $data['hora_fin']    = $datos['hora_fin'] ?? null;
        }

        return $this->insert($data) !== false;
    }

    /**
     * Eliminar excepción verificando que pertenece al usuario
     */
    public function eliminarExcepcion(int $id, int $usuarioId): bool
    {
        return $this->where('id', $id)
                    ->where('usuario_id', $usuarioId)
                    ->delete();
    }
}
