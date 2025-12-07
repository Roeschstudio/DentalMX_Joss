<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\Database\Exceptions\DatabaseException;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    protected function responderJson(int $statusCode, bool $error, string $message, array $extraData = []): \CodeIgniter\HTTP\ResponseInterface
    {
        try {
            $response = [
                'error' => $error,
                'status' => $error ? 'error' : 'success',
                'message' => $message,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // Combina los datos base con los extra
            $response = array_merge($response, $extraData);
            
            return $this->response
                ->setStatusCode($statusCode)
                ->setJSON($response)
                ->setContentType('application/json');
                
        } catch (\Exception $e) {
            log_message('error', 'Error generando respuesta JSON: ' . $e->getMessage());
            
            // Respuesta de emergencia
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'error' => true,
                    'status' => 'error',
                    'message' => 'Error interno del servidor'
                ]);
        }
    }
    
    /**
     * Maneja excepciones de manera consistente
     */
    protected function handleException(\Exception $e, string $context = ''): \CodeIgniter\HTTP\ResponseInterface
    {
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        
        // Loggear detalles completos
        log_message('critical', "Excepción en {$context}: {$message} en {$file}:{$line}");
        
        // En producción, no mostrar detalles del error
        if (ENVIRONMENT === 'production') {
            return $this->responderJson(500, true, 'Error interno del servidor');
        }
        
        // En desarrollo, mostrar información útil
        return $this->responderJson(500, true, $message, [
            'file' => $file,
            'line' => $line,
            'trace' => $e->getTraceAsString()
        ]);
    }
    
    /**
     * Maneja excepciones de base de datos
     */
    protected function handleDatabaseException(DatabaseException $e, string $context = ''): \CodeIgniter\HTTP\ResponseInterface
    {
        $message = $e->getMessage();
        
        log_message('error', "Error de base de datos en {$context}: {$message}");
        
        // Detectar tipos comunes de errores
        if (strpos($message, 'Duplicate') !== false) {
            return $this->responderJson(409, true, 'Registro duplicado');
        }
        
        if (strpos($message, 'foreign key constraint') !== false) {
            return $this->responderJson(409, true, 'No se puede eliminar debido a registros relacionados');
        }
        
        if (strpos($message, 'connection') !== false) {
            return $this->responderJson(503, true, 'Error de conexión a la base de datos');
        }
        
        return $this->responderJson(500, true, 'Error de base de datos');
    }
}
