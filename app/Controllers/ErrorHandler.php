<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class ErrorHandler extends BaseController
{
    /**
     * Maneja errores 404 - Página no encontrada
     */
    public function show404()
    {
        // Loggear el error 404
        log_message('warning', '404 Page Not Found: ' . current_url());
        
        // Establecer código de estado HTTP
        $this->response->setStatusCode(404);
        
        // Cargar vista personalizada
        return view('errors/error_custom', [
            'error_code' => 404,
            'error_title' => 'Página No Encontrada',
            'error_message' => 'La página que estás buscando no existe o ha sido movida.',
            'show_back_button' => true,
            'back_url' => base_url('/')
        ]);
    }
    
    /**
     * Maneja errores 500 - Error del servidor
     */
    public function show500()
    {
        // Loggear el error 500
        log_message('error', '500 Internal Server Error: ' . current_url());
        
        // Establecer código de estado HTTP
        $this->response->setStatusCode(500);
        
        // Cargar vista personalizada
        return view('errors/error_custom', [
            'error_code' => 500,
            'error_title' => 'Error del Servidor',
            'error_message' => 'Ha ocurrido un error inesperado. Por favor, intenta nuevamente más tarde.',
            'show_back_button' => true,
            'back_url' => base_url('/')
        ]);
    }
    
    /**
     * Maneja errores 403 - Acceso prohibido
     */
    public function show403()
    {
        // Loggear el error 403
        log_message('warning', '403 Forbidden: ' . current_url() . ' - User: ' . session()->get('email'));
        
        // Establecer código de estado HTTP
        $this->response->setStatusCode(403);
        
        // Cargar vista personalizada
        return view('errors/error_custom', [
            'error_code' => 403,
            'error_title' => 'Acceso Prohibido',
            'error_message' => 'No tienes permisos para acceder a esta página.',
            'show_back_button' => true,
            'back_url' => base_url('/')
        ]);
    }
    
    /**
     * Maneja errores de validación
     */
    public function showValidationErrors($errors = [])
    {
        // Loggear errores de validación
        log_message('warning', 'Validation Errors: ' . json_encode($errors));
        
        // Cargar vista personalizada
        return view('errors/validation_errors', [
            'errors' => $errors,
            'error_title' => 'Errores de Validación',
            'error_message' => 'Por favor, corrige los siguientes errores:'
        ]);
    }
    
    /**
     * Maneja errores de AJAX
     */
    public function handleAjaxError($errorCode = 500, $errorMessage = 'Error interno del servidor')
    {
        // Loggear error de AJAX
        log_message('error', 'AJAX Error: ' . $errorMessage . ' - URL: ' . current_url());
        
        // Responder con JSON de error
        return $this->responderJson($errorCode, true, $errorMessage);
    }
    
    /**
     * Método genérico para manejar excepciones en páginas de error
     */
    public function handlePageException(\Exception $exception)
    {
        // Loggear la excepción completa
        log_message('critical', 'Exception: ' . $exception->getMessage() . 
                   ' in ' . $exception->getFile() . ':' . $exception->getLine() .
                   ' - Stack: ' . $exception->getTraceAsString());
        
        // En producción, mostrar mensaje genérico
        if (ENVIRONMENT === 'production') {
            return $this->show500();
        }
        
        // En desarrollo, mostrar detalles de la excepción
        return view('errors/exception_debug', [
            'exception' => $exception,
            'error_code' => 500,
            'error_title' => 'Error del Servidor',
            'error_message' => $exception->getMessage()
        ]);
    }
}
