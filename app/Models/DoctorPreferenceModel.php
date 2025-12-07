<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar preferencias de doctor
 * 
 * Maneja configuraciones de duración de citas, descansos, etc.
 */
class DoctorPreferenceModel extends Model
{
    protected $table            = 'doctor_preferences';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'usuario_id',
        'duracion_cita',
        'tiempo_descanso',
        'citas_simultaneas'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'usuario_id'        => 'required|integer',
        'duracion_cita'     => 'required|integer|greater_than[0]',
        'tiempo_descanso'   => 'required|integer|greater_than_equal_to[0]',
        'citas_simultaneas' => 'required|integer|greater_than[0]',
    ];

    /**
     * Valores por defecto para preferencias
     */
    public static array $defaults = [
        'duracion_cita'     => 30,
        'tiempo_descanso'   => 15,
        'citas_simultaneas' => 1
    ];

    /**
     * Obtener preferencias de un doctor
     * Si no existen, retorna valores por defecto
     */
    public function getPreferencias(int $usuarioId): array
    {
        $preferencias = $this->where('usuario_id', $usuarioId)->first();

        if (!$preferencias) {
            return array_merge(self::$defaults, ['usuario_id' => $usuarioId]);
        }

        return $preferencias;
    }

    /**
     * Actualizar o crear preferencias
     */
    public function guardarPreferencias(int $usuarioId, array $datos): bool
    {
        $existente = $this->where('usuario_id', $usuarioId)->first();

        $data = [
            'duracion_cita'     => $datos['duracion_cita'] ?? self::$defaults['duracion_cita'],
            'tiempo_descanso'   => $datos['tiempo_descanso'] ?? self::$defaults['tiempo_descanso'],
            'citas_simultaneas' => $datos['citas_simultaneas'] ?? self::$defaults['citas_simultaneas'],
        ];

        if ($existente) {
            return $this->update($existente['id'], $data);
        } else {
            $data['usuario_id'] = $usuarioId;
            return $this->insert($data) !== false;
        }
    }

    /**
     * Obtener duración de cita de un doctor
     */
    public function getDuracionCita(int $usuarioId): int
    {
        $preferencias = $this->getPreferencias($usuarioId);
        return (int) $preferencias['duracion_cita'];
    }

    /**
     * Obtener tiempo de descanso entre citas
     */
    public function getTiempoDescanso(int $usuarioId): int
    {
        $preferencias = $this->getPreferencias($usuarioId);
        return (int) $preferencias['tiempo_descanso'];
    }
}
