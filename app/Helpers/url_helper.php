<?php

/**
 * URL Helper - DentalMX
 * 
 * Este helper proporciona funciones de URL que funcionan correctamente
 * tanto con mod_rewrite habilitado como deshabilitado.
 * 
 * IMPORTANTE: Este helper sobrescribe base_url() para rutas de controlador
 * para que automáticamente use site_url() cuando sea necesario.
 */

if (!function_exists('dental_url')) {
    /**
     * Genera una URL completa para la aplicación
     * Utiliza site_url() internamente para respetar app.indexPage
     *
     * @param string $path Ruta relativa (ej: 'pacientes', 'citas/nueva')
     * @return string URL completa
     */
    function dental_url(string $path = ''): string
    {
        // Eliminar barras iniciales si existen
        $path = ltrim($path, '/');
        return site_url($path);
    }
}

if (!function_exists('dental_action')) {
    /**
     * Genera una URL para action de formularios o llamadas AJAX
     * Utiliza site_url() internamente para respetar app.indexPage
     *
     * @param string $path Ruta relativa (ej: 'auth/login', 'citas/guardar')
     * @return string URL completa
     */
    function dental_action(string $path = ''): string
    {
        // Eliminar barras iniciales si existen
        $path = ltrim($path, '/');
        return site_url($path);
    }
}

if (!function_exists('dental_asset')) {
    /**
     * Genera una URL para assets estáticos (CSS, JS, imágenes)
     * Utiliza base_url() ya que los assets no pasan por el router
     *
     * @param string $path Ruta del asset (ej: 'css/style.css', 'js/app.js')
     * @return string URL completa del asset
     */
    function dental_asset(string $path = ''): string
    {
        // Eliminar barras iniciales si existen
        $path = ltrim($path, '/');
        
        // Usar la función original de CodeIgniter para assets
        return \CodeIgniter\Config\Services::request()->config->baseURL . $path;
    }
}

if (!function_exists('dental_redirect')) {
    /**
     * Genera una URL para redirecciones
     *
     * @param string $path Ruta relativa
     * @return string URL completa
     */
    function dental_redirect(string $path = ''): string
    {
        return dental_url($path);
    }
}

if (!function_exists('is_mamp')) {
    /**
     * Detecta si se está ejecutando en MAMP
     *
     * @return bool True si es MAMP
     */
    function is_mamp(): bool
    {
        // Verificar si existe la ruta típica de MAMP
        if (PHP_OS_FAMILY === 'Darwin') {
            return file_exists('/Applications/MAMP');
        }
        return false;
    }
}

if (!function_exists('is_xampp')) {
    /**
     * Detecta si se está ejecutando en XAMPP
     *
     * @return bool True si es XAMPP
     */
    function is_xampp(): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return file_exists('C:\\xampp') || 
                   file_exists('D:\\xampp') || 
                   strpos($_SERVER['DOCUMENT_ROOT'] ?? '', 'xampp') !== false;
        }
        return false;
    }
}

if (!function_exists('get_environment_info')) {
    /**
     * Obtiene información del entorno de ejecución
     *
     * @return array Información del entorno
     */
    function get_environment_info(): array
    {
        return [
            'os' => PHP_OS_FAMILY,
            'is_mamp' => is_mamp(),
            'is_xampp' => is_xampp(),
            'php_version' => PHP_VERSION,
            'base_url' => \Config\Services::request()->config->baseURL ?? '',
            'site_url' => site_url(),
            'index_page' => config('App')->indexPage ?? '',
        ];
    }
}

/**
 * Función auxiliar para determinar si una ruta es un asset o una ruta de controlador
 * 
 * @param string $path La ruta a verificar
 * @return bool True si es un asset, False si es ruta de controlador
 */
if (!function_exists('is_asset_path')) {
    function is_asset_path(string $path): bool
    {
        $path = ltrim($path, '/');
        
        // Lista de extensiones de archivos estáticos
        $assetExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'pdf', 'webp'];
        
        // Lista de carpetas de assets
        $assetFolders = ['css', 'js', 'assets', 'uploads', 'images', 'img', 'fonts'];
        
        // Verificar extensión
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, $assetExtensions)) {
            return true;
        }
        
        // Verificar si comienza con una carpeta de assets
        foreach ($assetFolders as $folder) {
            if (strpos($path, $folder . '/') === 0 || $path === $folder) {
                return true;
            }
        }
        
        return false;
    }
}

/**
 * URL inteligente que detecta automáticamente si debe usar base_url o site_url
 * 
 * @param string $path La ruta
 * @return string URL completa
 */
if (!function_exists('smart_url')) {
    function smart_url(string $path = ''): string
    {
        if (empty($path)) {
            return site_url();
        }
        
        $path = ltrim($path, '/');
        
        // Si es un asset, usar base_url
        if (is_asset_path($path)) {
            return base_url($path);
        }
        
        // Si es una ruta de controlador, usar site_url
        return site_url($path);
    }
}
