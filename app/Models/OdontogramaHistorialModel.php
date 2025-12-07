<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar el historial de cambios del odontograma
 * 
 * Registra todos los cambios realizados en el odontograma para
 * mantener un seguimiento completo del tratamiento dental.
 */
class OdontogramaHistorialModel extends Model
{
    protected $table = 'odontograma_historial';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_odontograma',
        'numero_diente',
        'tipo_accion',
        'campo_modificado',
        'valor_anterior',
        'valor_nuevo',
        'descripcion_cambio',
        'usuario_modificacion',
        'fecha_modificacion'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    /**
     * Tipos de acción posibles
     */
    public static array $tiposAccion = [
        'creacion' => 'Creación',
        'modificacion' => 'Modificación',
        'modificacion_superficie' => 'Cambio de superficie',
        'modificacion_estado' => 'Cambio de estado',
        'eliminacion' => 'Eliminación',
        'tratamiento' => 'Tratamiento realizado',
        'diagnostico' => 'Diagnóstico agregado'
    ];

    /**
     * Registra un cambio en el historial
     */
    public function registrarCambio(array $datos): int|bool
    {
        $datos['fecha_modificacion'] = date('Y-m-d H:i:s');
        return $this->insert($datos);
    }

    /**
     * Obtiene el historial de un odontograma
     */
    public function getHistorialOdontograma(int $idOdontograma, int $limite = 50): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        
        return $builder->select('odontograma_historial.*, usuarios.nombre as nombre_usuario')
                       ->join('usuarios', 'usuarios.id = odontograma_historial.usuario_modificacion', 'left')
                       ->where('odontograma_historial.id_odontograma', $idOdontograma)
                       ->orderBy('odontograma_historial.fecha_modificacion', 'DESC')
                       ->limit($limite)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Obtiene el historial de un diente específico
     */
    public function getHistorialDiente(int $idOdontograma, int $numeroDiente, int $limite = 20): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        
        return $builder->select('odontograma_historial.*, usuarios.nombre as nombre_usuario')
                       ->join('usuarios', 'usuarios.id = odontograma_historial.usuario_modificacion', 'left')
                       ->where('odontograma_historial.id_odontograma', $idOdontograma)
                       ->where('odontograma_historial.numero_diente', $numeroDiente)
                       ->orderBy('odontograma_historial.fecha_modificacion', 'DESC')
                       ->limit($limite)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Obtiene el historial filtrado por tipo de acción
     */
    public function getHistorialPorTipo(int $idOdontograma, string $tipoAccion, int $limite = 50): array
    {
        return $this->where('id_odontograma', $idOdontograma)
                    ->where('tipo_accion', $tipoAccion)
                    ->orderBy('fecha_modificacion', 'DESC')
                    ->limit($limite)
                    ->findAll();
    }

    /**
     * Obtiene el historial entre dos fechas
     */
    public function getHistorialPorFechas(int $idOdontograma, string $fechaInicio, string $fechaFin): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        
        return $builder->select('odontograma_historial.*, usuarios.nombre as nombre_usuario')
                       ->join('usuarios', 'usuarios.id = odontograma_historial.usuario_modificacion', 'left')
                       ->where('odontograma_historial.id_odontograma', $idOdontograma)
                       ->where('DATE(odontograma_historial.fecha_modificacion) >=', $fechaInicio)
                       ->where('DATE(odontograma_historial.fecha_modificacion) <=', $fechaFin)
                       ->orderBy('odontograma_historial.fecha_modificacion', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Obtiene las fechas únicas de modificación para comparación
     */
    public function getFechasDisponibles(int $idOdontograma): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        
        return $builder->select('DATE(fecha_modificacion) as fecha, COUNT(*) as cantidad')
                       ->where('id_odontograma', $idOdontograma)
                       ->groupBy('DATE(fecha_modificacion)')
                       ->orderBy('fecha', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Obtiene el último cambio realizado en un diente
     */
    public function getUltimoCambioDiente(int $idOdontograma, int $numeroDiente): ?array
    {
        return $this->where('id_odontograma', $idOdontograma)
                    ->where('numero_diente', $numeroDiente)
                    ->orderBy('fecha_modificacion', 'DESC')
                    ->first();
    }

    /**
     * Obtiene estadísticas del historial
     */
    public function getEstadisticas(int $idOdontograma): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        
        $totalCambios = $builder->where('id_odontograma', $idOdontograma)->countAllResults(false);
        
        $porTipo = $builder->select('tipo_accion, COUNT(*) as cantidad')
                          ->where('id_odontograma', $idOdontograma)
                          ->groupBy('tipo_accion')
                          ->get()
                          ->getResultArray();
        
        $porDiente = $db->table($this->table)
                        ->select('numero_diente, COUNT(*) as cantidad')
                        ->where('id_odontograma', $idOdontograma)
                        ->groupBy('numero_diente')
                        ->orderBy('cantidad', 'DESC')
                        ->limit(10)
                        ->get()
                        ->getResultArray();
        
        return [
            'total_cambios' => $totalCambios,
            'por_tipo' => $porTipo,
            'dientes_mas_modificados' => $porDiente
        ];
    }

    /**
     * Formatea el historial para mostrar en la vista
     */
    public function formatearHistorial(array $historial): array
    {
        $formateado = [];
        
        foreach ($historial as $item) {
            $formateado[] = [
                'id' => $item['id'],
                'fecha' => date('d/m/Y H:i', strtotime($item['fecha_modificacion'])),
                'diente' => $item['numero_diente'],
                'accion' => self::$tiposAccion[$item['tipo_accion']] ?? $item['tipo_accion'],
                'campo' => $this->formatearCampo($item['campo_modificado']),
                'anterior' => $item['valor_anterior'],
                'nuevo' => $item['valor_nuevo'],
                'descripcion' => $item['descripcion_cambio'],
                'usuario' => $item['nombre_usuario'] ?? 'Sistema'
            ];
        }
        
        return $formateado;
    }

    /**
     * Formatea el nombre del campo para mostrar
     */
    private function formatearCampo(?string $campo): string
    {
        $campos = [
            'estado' => 'Estado del diente',
            'sup_oclusal' => 'Superficie Oclusal',
            'sup_vestibular' => 'Superficie Vestibular',
            'sup_lingual' => 'Superficie Lingual',
            'sup_mesial' => 'Superficie Mesial',
            'sup_distal' => 'Superficie Distal',
            'movilidad' => 'Movilidad',
            'sensibilidad' => 'Sensibilidad',
            'diagnosticos' => 'Diagnósticos',
            'tratamientos_realizados' => 'Tratamientos realizados',
            'tratamientos_pendientes' => 'Tratamientos pendientes',
            'condiciones_especiales' => 'Condiciones especiales',
            'hallazgos' => 'Hallazgos',
            'notas' => 'Notas'
        ];
        
        return $campos[$campo] ?? $campo ?? 'General';
    }
}
