<?php

namespace App\Libraries;

class DentalLogger
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    /**
     * Registra eventos de seguridad
     */
    public function security(string $action, array $context = [])
    {
        $logData = [
            'ip' => $this->getIpAddress(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'user' => $this->getCurrentUser(),
            'context' => json_encode($context)
        ];

        log_message('info', "[SECURITY] {$action} | IP: {$logData['ip']} | User: {$logData['user']} | Context: {$logData['context']}");
    }

    /**
     * Registra eventos de auditoría
     */
    public function audit(string $action, string $resource = null, $resourceId = null, array $changes = [])
    {
        $logData = [
            'user' => $this->getCurrentUser(),
            'ip' => $this->getIpAddress(),
            'resource' => $resource,
            'resource_id' => $resourceId,
            'changes' => json_encode($changes),
            'controller' => $this->getCurrentController(),
            'method' => $this->getCurrentMethod()
        ];

        log_message('info', "[AUDIT] {$action} | User: {$logData['user']} | Resource: {$resource}:{$resourceId} | IP: {$logData['ip']}");
    }

    /**
     * Registra operaciones CRUD
     */
    public function crud(string $action, string $resource, $resourceId = null, array $data = [])
    {
        $logData = [
            'user' => $this->getCurrentUser(),
            'ip' => $this->getIpAddress(),
            'resource' => $resource,
            'resource_id' => $resourceId,
            'data' => json_encode($this->sanitizeData($data)),
            'controller' => $this->getCurrentController()
        ];

        log_message('info', "[CRUD] {$action} {$resource} | ID: {$resourceId} | User: {$logData['user']} | IP: {$logData['ip']}");
    }

    /**
     * Registra errores de base de datos
     */
    public function database(string $error, string $query = null, array $params = [])
    {
        $logData = [
            'ip' => $this->getIpAddress(),
            'user' => $this->getCurrentUser(),
            'query' => $query,
            'params' => json_encode($params),
            'controller' => $this->getCurrentController()
        ];

        log_message('error', "[DATABASE] {$error} | User: {$logData['user']} | IP: {$logData['ip']}");
    }

    /**
     * Registra eventos de aplicación
     */
    public function app(string $message, string $level = 'info', array $context = [])
    {
        $logData = [
            'ip' => $this->getIpAddress(),
            'user' => $this->getCurrentUser(),
            'context' => json_encode($context),
            'controller' => $this->getCurrentController(),
            'method' => $this->getCurrentMethod()
        ];

        log_message($level, "[APP] {$message} | User: {$logData['user']} | IP: {$logData['ip']} | Context: {$logData['context']}");
    }

    /**
     * Registra intentos de login
     */
    public function loginAttempt(string $email, bool $success, string $reason = null)
    {
        $action = $success 
            ? "LOGIN_EXITOSO: {$email}" 
            : "LOGIN_FALLIDO: {$email} - {$reason}";

        $this->security($action, [
            'email' => $email,
            'success' => $success,
            'reason' => $reason
        ]);
    }

    /**
     * Registra accesos a recursos
     */
    public function resourceAccess(string $resource, $resourceId = null)
    {
        $this->audit("ACCESO_RECURSO", $resource, $resourceId);
    }

    /**
     * Registra cambios en datos
     */
    public function dataChange(string $action, string $resource, $resourceId, array $oldData = [], array $newData = [])
    {
        $changes = [];
        
        foreach ($newData as $key => $value) {
            if (!isset($oldData[$key]) || $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value
                ];
            }
        }

        $this->audit($action, $resource, $resourceId, $changes);
    }

    /**
     * Registra eventos de logout
     */
    public function logout()
    {
        $user = $this->getCurrentUser();
        $this->security("LOGOUT: {$user}");
    }

    /**
     * Registra errores de validación
     */
    public function validationError(array $errors, array $data = [])
    {
        $this->app("VALIDACION_FALLIDA", 'warning', [
            'errors' => $errors,
            'data' => $this->sanitizeData($data)
        ]);
    }

    /**
     * Registra excepciones
     */
    public function exception(\Exception $exception, string $context = '')
    {
        $this->app("EXCEPCION: " . $exception->getMessage(), 'critical', [
            'context' => $context,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => substr($exception->getTraceAsString(), 0, 500) // Limitar tamaño
        ]);
    }

    /**
     * Obtiene dirección IP del cliente
     */
    private function getIpAddress(): string
    {
        $request = service('request');
        return $request->getIPAddress();
    }

    /**
     * Obtiene usuario actual
     */
    private function getCurrentUser(): string
    {
        if (!$this->session) {
            return 'unknown (no session)';
        }
        return $this->session->get('email') ?? 'anonymous';
    }

    /**
     * Obtiene controller actual
     */
    private function getCurrentController(): string
    {
        $request = service('request');
        $uri = $request->getPath();
        $segments = explode('/', $uri);
        return $segments[0] ?? 'unknown';
    }

    /**
     * Obtiene método actual
     */
    private function getCurrentMethod(): string
    {
        $request = service('request');
        return $request->getMethod();
    }

    /**
     * Limpia datos sensibles para logging
     */
    private function sanitizeData(array $data): array
    {
        $sensitiveKeys = ['password', 'token', 'secret', 'key', 'api_key', 'access_token'];
        
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $data[$key] = '***REDACTED***';
            }
        }

        return $data;
    }
}
