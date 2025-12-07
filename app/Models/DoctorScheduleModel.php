<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar horarios de doctores
 * 
 * Maneja la configuración semanal de horarios de atención
 */
class DoctorScheduleModel extends Model
{
    protected $table            = 'doctor_schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'usuario_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'activo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'usuario_id'  => 'required|integer',
        'dia_semana'  => 'required|integer|greater_than[0]|less_than[8]',
        'hora_inicio' => 'required',
        'hora_fin'    => 'required',
    ];

    protected $validationMessages = [
        'dia_semana' => [
            'greater_than' => 'El día debe estar entre 1 (Lunes) y 7 (Domingo)',
            'less_than'    => 'El día debe estar entre 1 (Lunes) y 7 (Domingo)',
        ],
    ];

    /**
     * Nombres de los días de la semana
     */
    public static array $diasSemana = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo'
    ];

    /**
     * Obtener horario por doctor y día
     */
    public function getHorarioPorDoctorYDia(int $usuarioId, int $diaSemana): ?array
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('dia_semana', $diaSemana)
                    ->where('activo', 1)
                    ->first();
    }

    /**
     * Obtener todos los horarios de un doctor
     */
    public function getHorariosDoctor(int $usuarioId): array
    {
        return $this->where('usuario_id', $usuarioId)
                    ->orderBy('dia_semana', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener horarios formateados con nombres de días
     */
    public function getHorariosFormateados(int $usuarioId): array
    {
        $horarios = $this->getHorariosDoctor($usuarioId);
        $resultado = [];

        // Inicializar todos los días
        foreach (self::$diasSemana as $num => $nombre) {
            $resultado[$num] = [
                'dia_num'     => $num,
                'dia'         => $nombre,
                'hora_inicio' => '',
                'hora_fin'    => '',
                'activo'      => false
            ];
        }

        // Llenar con datos existentes
        foreach ($horarios as $horario) {
            $dia = $horario['dia_semana'];
            $resultado[$dia] = [
                'id'          => $horario['id'],
                'dia_num'     => $dia,
                'dia'         => self::$diasSemana[$dia],
                'hora_inicio' => substr($horario['hora_inicio'], 0, 5),
                'hora_fin'    => substr($horario['hora_fin'], 0, 5),
                'activo'      => (bool) $horario['activo']
            ];
        }

        return $resultado;
    }

    /**
     * Guardar horarios completos de un doctor
     * Reemplaza todos los horarios existentes
     */
    public function guardarHorariosCompletos(int $usuarioId, array $horarios): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Eliminar horarios existentes
            $this->where('usuario_id', $usuarioId)->delete();

            // Insertar nuevos horarios
            foreach ($horarios as $diaSemana => $horario) {
                if (!empty($horario['hora_inicio']) && !empty($horario['hora_fin'])) {
                    $this->insert([
                        'usuario_id'  => $usuarioId,
                        'dia_semana'  => $diaSemana,
                        'hora_inicio' => $horario['hora_inicio'],
                        'hora_fin'    => $horario['hora_fin'],
                        'activo'      => isset($horario['activo']) ? 1 : 0
                    ]);
                }
            }

            $db->transComplete();
            return $db->transStatus();

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error guardando horarios: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el doctor está disponible en un día/hora específicos
     */
    public function estaDisponible(int $usuarioId, int $diaSemana, string $hora): bool
    {
        $horario = $this->getHorarioPorDoctorYDia($usuarioId, $diaSemana);

        if (!$horario || !$horario['activo']) {
            return false;
        }

        $horaCheck = strtotime($hora);
        $horaInicio = strtotime($horario['hora_inicio']);
        $horaFin = strtotime($horario['hora_fin']);

        return $horaCheck >= $horaInicio && $horaCheck < $horaFin;
    }
}
