<?php

namespace App\Libraries;

class EdgeCaseHandler
{
    protected $request;
    protected $response;
    
    public function __construct()
    {
        $this->request = service('request');
        $this->response = service('response');
    }
    
    /**
     * Valida y sanitiza datos de entrada para prevenir edge cases
     */
    public function validateInput($data, array $rules = [])
    {
        if (is_null($data)) {
            return null;
        }
        
        if (is_string($data)) {
            return $this->validateString($data, $rules);
        }
        
        if (is_array($data)) {
            return $this->validateArray($data, $rules);
        }
        
        return $data;
    }
    
    /**
     * Valida strings para edge cases comunes
     */
    private function validateString(string $data, array $rules): string
    {
        // Manejar strings vacíos o solo espacios
        if (empty(trim($data))) {
            return '';
        }
        
        // Normalizar caracteres especiales
        $data = $this->normalizeSpecialChars($data);
        
        // Validar longitud máxima para prevenir overflow
        $maxLength = $rules['max_length'] ?? 1000;
        if (strlen($data) > $maxLength) {
            throw new \InvalidArgumentException("El texto excede la longitud máxima de {$maxLength} caracteres");
        }
        
        // Validar caracteres peligrosos (XSS, SQL Injection)
        if (isset($rules['xss_protection']) && $rules['xss_protection']) {
            $data = $this->removeXSS($data);
        }
        
        // Validar caracteres no permitidos
        if (isset($rules['allowed_chars']) && $rules['allowed_chars']) {
            $pattern = '/[^' . $rules['allowed_chars'] . ']/';
            if (preg_match($pattern, $data)) {
                throw new \InvalidArgumentException('El texto contiene caracteres no permitidos');
            }
        }
        
        return trim($data);
    }
    
    /**
     * Valida arrays para edge cases
     */
    private function validateArray(array $data, array $rules): array
    {
        $validated = [];
        
        foreach ($data as $key => $value) {
            $fieldRules = $rules[$key] ?? [];
            
            try {
                $validated[$key] = $this->validateInput($value, $fieldRules);
            } catch (\Exception $e) {
                // Loggear error de validación pero continuar con otros campos
                log_message('warning', "Error validando campo {$key}: " . $e->getMessage());
                $validated[$key] = null;
            }
        }
        
        return $validated;
    }
    
    /**
     * Normaliza caracteres especiales y codificaciones
     */
    private function normalizeSpecialChars(string $data): string
    {
        // Convertir codificaciones problemáticas
        $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        
        // Normalizar espacios y saltos de línea
        $data = preg_replace('/\s+/', ' ', $data);
        $data = str_replace(["\r\n", "\r", "\n"], ' ', $data);
        
        // Normalizar comillas y apóstrofes
        $data = str_replace(['"', '\'', '`', '´', '¨'], ['"', "'", "'", "'", "'"], $data);
        
        // Normalizar guiones
        $data = str_replace(['–', '—', '−'], '-', $data);
        
        // Remover caracteres de control invisibles
        $data = preg_replace('/[\x00-\x1F\x7F]/', '', $data);
        
        return $data;
    }
    
    /**
     * Remueve posibles ataques XSS
     */
    private function removeXSS(string $data): string
    {
        // Patrones comunes de XSS
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
            '/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi',
            '/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/onfocus\s*=/i',
            '/onblur\s*=/i',
            '/onchange\s*=/i',
            '/onsubmit\s*=/i'
        ];
        
        foreach ($xssPatterns as $pattern) {
            $data = preg_replace($pattern, '', $data);
        }
        
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Valida y formatea valores decimales
     */
    public function validateDecimal($value, int $decimals = 2): ?float
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        
        // Limpiar el valor
        $value = preg_replace('/[^0-9.,-]/', '', $value);
        
        // Reemplazar múltiples puntos por uno solo
        $value = preg_replace('/\.+/', '.', $value);
        
        // Validar formato
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('El valor no es un número decimal válido');
        }
        
        $floatValue = (float) $value;
        
        return round($floatValue, $decimals);
    }
    
    /**
     * Valida fechas y horas
     */
    public function validateDateTime($date, string $format = 'Y-m-d H:i:s'): ?string
    {
        if (empty($date)) {
            return null;
        }
        
        // Manejar diferentes formatos de fecha
        $dateFormats = [
            'Y-m-d H:i:s',
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'm/d/Y',
            'Y/m/d',
            'H:i:s',
            'H:i'
        ];
        
        foreach ($dateFormats as $dateFormat) {
            $dateObj = \DateTime::createFromFormat($dateFormat, $date);
            if ($dateObj !== false) {
                // Validar rangos razonables
                $now = new \DateTime();
                $minDate = new \DateTime('1900-01-01');
                $maxDate = new \DateTime('2100-12-31');
                
                if ($dateObj < $minDate || $dateObj > $maxDate) {
                    throw new \InvalidArgumentException('La fecha está fuera del rango permitido (1900-2100)');
                }
                
                return $dateObj->format($format);
            }
        }
        
        throw new \InvalidArgumentException('Formato de fecha no válido');
    }
    
    /**
     * Valida tamaños de archivos
     */
    public function validateFileSize($file, int $maxSizeMB = 10): bool
    {
        if (!isset($file['size']) || $file['size'] === 0) {
            return false;
        }
        
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;
        
        if ($file['size'] > $maxSizeBytes) {
            throw new \InvalidArgumentException("El archivo excede el tamaño máximo de {$maxSizeMB}MB");
        }
        
        return true;
    }
    
    /**
     * Valida límites de sistema
     */
    public function checkSystemLimits(): array
    {
        $limits = [];
        
        // Verificar límite de memoria
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $memoryPercent = ($memoryUsage / $this->parseBytes($memoryLimit)) * 100;
        
        $limits['memory'] = [
            'limit' => $memoryLimit,
            'usage' => $this->formatBytes($memoryUsage),
            'percent' => round($memoryPercent, 2),
            'warning' => $memoryPercent > 80
        ];
        
        // Verificar tiempo de ejecución
        $maxExecutionTime = ini_get('max_execution_time');
        $currentTime = time() - $_SERVER['REQUEST_TIME'];
        
        $limits['execution_time'] = [
            'limit' => $maxExecutionTime,
            'current' => $currentTime,
            'warning' => $currentTime > ($maxExecutionTime * 0.8)
        ];
        
        // Verificar tamaño POST
        $postMaxSize = ini_get('post_max_size');
        $currentPostSize = strlen(serialize($_POST));
        
        $limits['post_size'] = [
            'limit' => $postMaxSize,
            'current' => $this->formatBytes($currentPostSize),
            'warning' => $currentPostSize > ($this->parseBytes($postMaxSize) * 0.8)
        ];
        
        return $limits;
    }
    
    /**
     * Maneja concurrencia simple
     */
    public function handleConcurrency(string $resource, callable $callback, int $timeout = 30)
    {
        $lockDir = WRITEPATH . 'locks';
        
        // Crear directorio de locks si no existe
        if (!is_dir($lockDir)) {
            mkdir($lockDir, 0755, true);
        }
        
        $lockFile = $lockDir . '/' . md5($resource) . '.lock';
        $lockTimeout = time() + $timeout;
        
        // Intentar adquirir bloqueo
        while (file_exists($lockFile) && time() < $lockTimeout) {
            usleep(100000); // Esperar 100ms
        }
        
        if (time() >= $lockTimeout) {
            throw new \RuntimeException('Tiempo de espera agotado. Por favor, intenta más tarde.');
        }
        
        try {
            // Crear archivo de bloqueo
            file_put_contents($lockFile, json_encode([
                'timestamp' => time(),
                'process_id' => getmypid(),
                'resource' => $resource
            ]));
            
            // Ejecutar callback
            $result = $callback();
            
            return $result;
            
        } finally {
            // Liberar bloqueo
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }
        }
    }
    
    /**
     * Convierte bytes a formato legible
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Convierte formato legible a bytes
     */
    private function parseBytes(string $value): int
    {
        $unit = strtoupper(substr($value, -1));
        $number = (int) substr($value, 0, -1);
        
        switch ($unit) {
            case 'G':
                return $number * 1024 * 1024 * 1024;
            case 'M':
                return $number * 1024 * 1024;
            case 'K':
                return $number * 1024;
            default:
                return (int) $value;
        }
    }
}
