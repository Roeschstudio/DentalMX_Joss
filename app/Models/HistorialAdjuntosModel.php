<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar los archivos adjuntos del historial de actividades
 * 
 * Permite adjuntar documentos, imágenes y otros archivos a las actividades
 * del historial del paciente.
 */
class HistorialAdjuntosModel extends Model
{
    protected $table = 'historial_adjuntos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_historial_actividad',
        'nombre_archivo',
        'ruta_archivo',
        'tipo_archivo',
        'tamanio_archivo',
        'descripcion'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = false; // Solo tiene created_at
    protected $createdField = 'created_at';
    
    protected $validationRules = [
        'id_historial_actividad' => 'required|integer|greater_than[0]',
        'nombre_archivo' => 'required|max_length[255]',
        'ruta_archivo' => 'required|max_length[500]',
        'tipo_archivo' => 'required|max_length[100]',
        'tamanio_archivo' => 'required|integer|greater_than[0]'
    ];
    
    protected $validationMessages = [
        'id_historial_actividad' => [
            'required' => 'El ID de la actividad es requerido',
            'integer' => 'El ID de la actividad debe ser un número entero'
        ],
        'nombre_archivo' => [
            'required' => 'El nombre del archivo es requerido',
            'max_length' => 'El nombre del archivo no puede exceder 255 caracteres'
        ],
        'ruta_archivo' => [
            'required' => 'La ruta del archivo es requerida',
            'max_length' => 'La ruta del archivo no puede exceder 500 caracteres'
        ],
        'tipo_archivo' => [
            'required' => 'El tipo de archivo es requerido'
        ],
        'tamanio_archivo' => [
            'required' => 'El tamaño del archivo es requerido',
            'integer' => 'El tamaño debe ser un número entero',
            'greater_than' => 'El tamaño debe ser mayor que 0'
        ]
    ];

    /**
     * Tipos de archivo permitidos con su configuración
     */
    public static array $tiposPermitidos = [
        'image/jpeg' => ['ext' => 'jpg', 'icon' => '🖼️', 'categoria' => 'imagen'],
        'image/png' => ['ext' => 'png', 'icon' => '🖼️', 'categoria' => 'imagen'],
        'image/gif' => ['ext' => 'gif', 'icon' => '🖼️', 'categoria' => 'imagen'],
        'image/webp' => ['ext' => 'webp', 'icon' => '🖼️', 'categoria' => 'imagen'],
        'application/pdf' => ['ext' => 'pdf', 'icon' => '📄', 'categoria' => 'documento'],
        'application/msword' => ['ext' => 'doc', 'icon' => '📝', 'categoria' => 'documento'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['ext' => 'docx', 'icon' => '📝', 'categoria' => 'documento'],
        'application/vnd.ms-excel' => ['ext' => 'xls', 'icon' => '📊', 'categoria' => 'documento'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['ext' => 'xlsx', 'icon' => '📊', 'categoria' => 'documento'],
        'text/plain' => ['ext' => 'txt', 'icon' => '📃', 'categoria' => 'documento'],
        'application/dicom' => ['ext' => 'dcm', 'icon' => '🩻', 'categoria' => 'medico'],
    ];

    /**
     * Directorio base para uploads
     */
    private string $uploadPath = WRITEPATH . 'uploads/historial/';

    /**
     * Obtener adjuntos de una actividad
     */
    public function getAdjuntosPorActividad(int $id_actividad): array
    {
        return $this->where('id_historial_actividad', $id_actividad)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Obtener adjuntos de múltiples actividades
     */
    public function getAdjuntosPorActividades(array $ids_actividades): array
    {
        if (empty($ids_actividades)) {
            return [];
        }
        
        return $this->whereIn('id_historial_actividad', $ids_actividades)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Subir y guardar un archivo adjunto
     */
    public function guardarAdjunto(int $id_actividad, $archivo, ?string $descripcion = null): array
    {
        // Verificar que es un archivo válido
        if (!$archivo->isValid()) {
            return [
                'success' => false,
                'error' => 'El archivo no es válido: ' . $archivo->getErrorString()
            ];
        }
        
        // Verificar tipo de archivo
        $mimeType = $archivo->getMimeType();
        if (!array_key_exists($mimeType, self::$tiposPermitidos)) {
            return [
                'success' => false,
                'error' => 'Tipo de archivo no permitido: ' . $mimeType
            ];
        }
        
        // Verificar tamaño (máximo 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($archivo->getSize() > $maxSize) {
            return [
                'success' => false,
                'error' => 'El archivo excede el tamaño máximo permitido (10MB)'
            ];
        }
        
        // Crear directorio si no existe
        $uploadDir = $this->uploadPath . date('Y/m/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generar nombre único
        $nombreOriginal = $archivo->getClientName();
        $extension = $archivo->getClientExtension();
        $nombreNuevo = uniqid('adj_') . '_' . time() . '.' . $extension;
        
        // Mover archivo
        try {
            $archivo->move($uploadDir, $nombreNuevo);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Error al guardar el archivo: ' . $e->getMessage()
            ];
        }
        
        // Guardar en base de datos
        $data = [
            'id_historial_actividad' => $id_actividad,
            'nombre_archivo' => $nombreOriginal,
            'ruta_archivo' => 'uploads/historial/' . date('Y/m/') . $nombreNuevo,
            'tipo_archivo' => $mimeType,
            'tamanio_archivo' => $archivo->getSize(),
            'descripcion' => $descripcion,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->insert($data)) {
            return [
                'success' => true,
                'id' => $this->getInsertID(),
                'data' => $data
            ];
        }
        
        // Si falla la inserción, eliminar el archivo
        @unlink($uploadDir . $nombreNuevo);
        
        return [
            'success' => false,
            'error' => 'Error al guardar en la base de datos'
        ];
    }

    /**
     * Eliminar un adjunto
     */
    public function eliminarAdjunto(int $id_adjunto): bool
    {
        $adjunto = $this->find($id_adjunto);
        
        if (!$adjunto) {
            return false;
        }
        
        // Eliminar archivo físico
        $rutaCompleta = WRITEPATH . $adjunto['ruta_archivo'];
        if (file_exists($rutaCompleta)) {
            @unlink($rutaCompleta);
        }
        
        // Eliminar registro
        return $this->delete($id_adjunto);
    }

    /**
     * Eliminar todos los adjuntos de una actividad
     */
    public function eliminarAdjuntosActividad(int $id_actividad): bool
    {
        $adjuntos = $this->getAdjuntosPorActividad($id_actividad);
        
        foreach ($adjuntos as $adjunto) {
            $rutaCompleta = WRITEPATH . $adjunto['ruta_archivo'];
            if (file_exists($rutaCompleta)) {
                @unlink($rutaCompleta);
            }
        }
        
        return $this->where('id_historial_actividad', $id_actividad)->delete();
    }

    /**
     * Obtener estadísticas de adjuntos de un paciente
     */
    public function getEstadisticasAdjuntos(int $id_paciente): array
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('historial_adjuntos ha')
                     ->select('
                        COUNT(*) as total_adjuntos,
                        SUM(ha.tamanio_archivo) as tamanio_total,
                        ha.tipo_archivo')
                     ->join('historial_actividades act', 'act.id = ha.id_historial_actividad')
                     ->where('act.id_paciente', $id_paciente)
                     ->groupBy('ha.tipo_archivo')
                     ->get()
                     ->getResultArray();
        
        $total = 0;
        $tamanioTotal = 0;
        $porTipo = [];
        
        foreach ($result as $row) {
            $total += (int)$row['total_adjuntos'];
            $tamanioTotal += (int)$row['tamanio_total'];
            $porTipo[$row['tipo_archivo']] = [
                'cantidad' => (int)$row['total_adjuntos'],
                'tamanio' => (int)$row['tamanio_total']
            ];
        }
        
        return [
            'total_adjuntos' => $total,
            'tamanio_total' => $tamanioTotal,
            'tamanio_formateado' => $this->formatearTamanio($tamanioTotal),
            'por_tipo' => $porTipo
        ];
    }

    /**
     * Formatear tamaño de archivo
     */
    public function formatearTamanio(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Obtener icono según tipo de archivo
     */
    public static function getIconoTipo(string $tipo_archivo): string
    {
        return self::$tiposPermitidos[$tipo_archivo]['icon'] ?? '📎';
    }

    /**
     * Verificar si un tipo de archivo está permitido
     */
    public static function tipoPermitido(string $tipo_archivo): bool
    {
        return array_key_exists($tipo_archivo, self::$tiposPermitidos);
    }

    /**
     * Obtener URL pública para descargar archivo
     */
    public function getUrlDescarga(int $id_adjunto): ?string
    {
        $adjunto = $this->find($id_adjunto);
        
        if (!$adjunto) {
            return null;
        }
        
        return site_url('historial/descargar-adjunto/' . $id_adjunto);
    }

    /**
     * Verificar si el archivo físico existe
     */
    public function archivoExiste(int $id_adjunto): bool
    {
        $adjunto = $this->find($id_adjunto);
        
        if (!$adjunto) {
            return false;
        }
        
        return file_exists(WRITEPATH . $adjunto['ruta_archivo']);
    }
}
