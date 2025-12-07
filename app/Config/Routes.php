<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 * 
 * DENTAL MX - Sistema de Rutas Unificado
 * Todas las rutas están en español
 * Fecha: 2025-12-03
 */

// ============================================
// RUTAS PÚBLICAS (Sin autenticación)
// ============================================
$routes->get('/login', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/auth/logout', 'Auth::logout');

// Rutas de Error
$routes->get('/error/404', 'ErrorHandler::show404');
$routes->get('/error/500', 'ErrorHandler::show500');
$routes->get('/error/403', 'ErrorHandler::show403');

// ============================================
// RUTAS PROTEGIDAS (Requieren autenticación)
// ============================================
$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // ----------------------------------------
    // DASHBOARD / INICIO
    // ----------------------------------------
    $routes->get('/', 'Home::index');
    $routes->get('/inicio', 'Home::index');
    
    // ----------------------------------------
    // PACIENTES (Gestión completa)
    // ----------------------------------------
    $routes->get('/pacientes', 'PatientController::index');
    $routes->get('/pacientes/nuevo', 'PatientController::create');
    $routes->post('/pacientes', 'PatientController::store');
    $routes->get('/pacientes/(:num)', 'PatientController::show/$1');
    $routes->get('/pacientes/(:num)/pdf', 'PatientController::pdf/$1');
    $routes->get('/pacientes/(:num)/editar', 'PatientController::edit/$1');
    $routes->put('/pacientes/(:num)', 'PatientController::update/$1');
    $routes->post('/pacientes/(:num)/actualizar', 'PatientController::update/$1');
    $routes->delete('/pacientes/(:num)', 'PatientController::delete/$1');
    $routes->get('/pacientes/(:num)/eliminar', 'PatientController::delete/$1');
    
    // API Pacientes (para AJAX)
    $routes->get('/pacientes/listar', 'Pacientes::getAll');
    $routes->post('/pacientes/borrar', 'Pacientes::borrar');
    $routes->post('/pacientes/guardar/datos_generales', 'Pacientes::guardarDatosGenerales');
    $routes->post('/pacientes/guardar/antecedentes_familiares', 'Pacientes::guardarAntecedentesFamiliares');
    $routes->post('/pacientes/guardar/antecedentes_patologicos', 'Pacientes::guardarAntecedentesPatologicos');
    $routes->post('/pacientes/guardar/historial_bucodental', 'Pacientes::guardarHistorialBucoDental');
    $routes->get('/pacientes/notas_evolucion/(:any)', 'Pacientes::getAllNotasEvolucionByPaciente/$1');
    $routes->post('/pacientes/guardar/notas_evolucion', 'Pacientes::guardarNotaEvolucion');
    
    // ----------------------------------------
    // CITAS / AGENDA
    // ----------------------------------------
    $routes->get('/citas', 'Citas::index');
    $routes->get('/citas/nueva', 'Citas::nueva');
    $routes->get('/citas/calendario', 'Citas::calendario');
    $routes->post('/citas/guardar', 'Citas::guardar');
    $routes->get('/citas/(:num)', 'Citas::ver/$1');
    $routes->get('/citas/(:num)/editar', 'Citas::editar/$1');
    $routes->post('/citas/(:num)/actualizar', 'Citas::actualizar/$1');
    $routes->get('/citas/(:num)/eliminar', 'Citas::eliminar/$1');
    $routes->post('/citas/(:num)/cambiar-estado', 'Citas::cambiarEstado/$1');
    
    // API endpoints para FullCalendar y AJAX
    $routes->get('/citas/api/getCitas', 'Citas::getCitas');
    $routes->get('/citas/api/getCita/(:num)', 'Citas::getCita/$1');
    $routes->post('/citas/api/store', 'Citas::store');
    $routes->post('/citas/api/update/(:num)', 'Citas::update/$1');
    $routes->post('/citas/api/actualizarFecha/(:num)', 'Citas::actualizarFecha/$1');
    $routes->post('/citas/api/delete/(:num)', 'Citas::delete/$1');
    $routes->get('/citas/api/disponibilidad', 'Citas::verificarDisponibilidad');
    $routes->get('/citas/api/buscarPacientes', 'Citas::buscarPacientes');
    $routes->get('/citas/api/estadisticas', 'Citas::estadisticas');
    
    // ----------------------------------------
    // HORARIO / AGENDA MÉDICA
    // ----------------------------------------
    $routes->get('/horario', 'Agenda::index');
    $routes->get('/agenda', 'Agenda::index');
    $routes->get('/agenda/nueva', 'Agenda::nueva');
    $routes->get('/agenda/calendario', 'Agenda::calendario');
    $routes->post('/agenda/guardar', 'Agenda::guardar');
    $routes->get('/agenda/excepciones', 'Agenda::excepciones');
    $routes->post('/agenda/guardar-excepcion', 'Agenda::guardarExcepcion');
    $routes->get('/agenda/eliminar-excepcion/(:num)', 'Agenda::eliminarExcepcion/$1');
    $routes->get('/agenda/preview', 'Agenda::preview');
    $routes->get('/agenda/horarios-disponibles', 'Agenda::getHorariosDisponiblesApi');
    
    // ----------------------------------------
    // RECETAS
    // ----------------------------------------
    $routes->get('/recetas', 'Recetas::index');
    $routes->get('/recetas/nueva', 'Recetas::nueva');
    $routes->get('/recetas/crear/(:num)', 'Recetas::crear/$1');
    $routes->post('/recetas/guardar', 'Recetas::guardar');
    $routes->get('/recetas/(:num)', 'Recetas::ver/$1');
    $routes->get('/recetas/imprimir/(:num)', 'Recetas::imprimir/$1');
    
    // ----------------------------------------
    // CATÁLOGOS
    // ----------------------------------------
    
    // Medicamentos
    $routes->get('/medicamentos', 'Medicamentos::index');
    $routes->post('/medicamentos/guardar', 'Medicamentos::save');
    $routes->post('/medicamentos/save', 'Medicamentos::save');
    $routes->post('/medicamentos/eliminar', 'Medicamentos::delete');
    $routes->post('/medicamentos/delete', 'Medicamentos::delete');
    
    // Servicios
    $routes->get('/servicios', 'Servicios::index');
    $routes->post('/servicios/guardar', 'Servicios::save');
    $routes->post('/servicios/save', 'Servicios::save');
    $routes->post('/servicios/eliminar', 'Servicios::delete');
    $routes->post('/servicios/delete', 'Servicios::delete');
    
    // ----------------------------------------
    // PRESUPUESTOS
    // ----------------------------------------
    $routes->get('/presupuestos', 'PresupuestosController::index');
    $routes->get('/presupuestos/create', 'PresupuestosController::create');
    $routes->post('/presupuestos/store', 'PresupuestosController::store');
    $routes->get('/presupuestos/show/(:num)', 'PresupuestosController::show/$1');
    $routes->get('/presupuestos/edit/(:num)', 'PresupuestosController::edit/$1');
    $routes->post('/presupuestos/update/(:num)', 'PresupuestosController::update/$1');
    $routes->get('/presupuestos/delete/(:num)', 'PresupuestosController::delete/$1');
    $routes->get('/presupuestos/deleted', 'PresupuestosController::deleted');
    $routes->get('/presupuestos/restore/(:num)', 'PresupuestosController::restore/$1');
    $routes->get('/presupuestos/force-delete/(:num)', 'PresupuestosController::forceDelete/$1');
    $routes->get('/presupuestos/send/(:num)', 'PresupuestosController::send/$1');
    $routes->get('/presupuestos/approve/(:num)', 'PresupuestosController::approve/$1');
    $routes->get('/presupuestos/reject/(:num)', 'PresupuestosController::reject/$1');
    $routes->get('/presupuestos/convert/(:num)', 'PresupuestosController::convert/$1');
    $routes->get('/presupuestos/pdf/(:num)', 'PresupuestosController::pdf/$1');

    // ----------------------------------------
    // COTIZACIONES
    // ----------------------------------------
    $routes->get('/cotizaciones', 'Cotizaciones::index');
    $routes->get('/cotizaciones/nueva', 'Cotizaciones::nueva');
    $routes->get('/cotizaciones/crear/(:num)', 'Cotizaciones::crear/$1');
    $routes->get('/cotizaciones/(:num)', 'Cotizaciones::ver/$1');
    $routes->post('/cotizaciones/guardar', 'Cotizaciones::guardar');
    $routes->get('/cotizaciones/imprimir/(:num)', 'Cotizaciones::imprimir/$1');
    
    // ----------------------------------------
    // AJUSTES / CONFIGURACIÓN
    // ----------------------------------------
    $routes->get('/ajustes', 'Ajustes::index');
    $routes->get('/configuracion', 'Ajustes::index');
    
    // Perfil de usuario
    $routes->get('/ajustes/perfil', 'Ajustes::perfil');
    $routes->post('/ajustes/actualizar-perfil', 'Ajustes::actualizarPerfil');
    
    // Configuración de clínica
    $routes->get('/ajustes/clinica', 'Ajustes::clinica');
    $routes->post('/ajustes/actualizar-clinica', 'Ajustes::actualizarClinica');
    
    // Preferencias de usuario
    $routes->get('/ajustes/preferencias', 'Ajustes::preferencias');
    $routes->post('/ajustes/actualizar-preferencias', 'Ajustes::actualizarPreferencias');
    
    // Cambio de contraseña
    $routes->get('/ajustes/cambiar-contrasena', 'Ajustes::cambiarContrasena');
    $routes->post('/ajustes/actualizar-contrasena', 'Ajustes::actualizarContrasena');
    
    // Configuración de correo
    $routes->get('/ajustes/correo', 'Ajustes::correo');
    $routes->post('/ajustes/actualizar-correo', 'Ajustes::actualizarCorreo');
    $routes->post('/ajustes/probar-correo', 'Ajustes::probarCorreo');

    
    // ----------------------------------------
    // NOTIFICACIONES
    // ----------------------------------------
    $routes->get('/notificaciones', 'Notificaciones::index');
    
    // ----------------------------------------
    // PERFIL DE USUARIO
    // ----------------------------------------
    $routes->get('/perfil', 'Perfil::index');
    $routes->post('/perfil/actualizar', 'Perfil::actualizar');
    $routes->post('/perfil/cambiar-password', 'Perfil::cambiarPassword');
    
    // ----------------------------------------
    // AYUDA
    // ----------------------------------------
    $routes->get('/ayuda', 'Ayuda::index');
    
    // ----------------------------------------
    // HISTORIAL DE PACIENTES
    // ----------------------------------------
    // Timeline principal
    $routes->get('/historial/(:num)', 'HistorialController::index/$1');
    $routes->get('/historial/paciente/(:num)', 'HistorialController::index/$1'); // Alias para compatibilidad
    $routes->get('/pacientes/(:num)/historial', 'HistorialController::index/$1');
    
    // Detalles y búsqueda
    $routes->get('/historial/detalles/(:num)', 'HistorialController::detalles/$1');
    $routes->get('/historial/(:num)/buscar', 'HistorialController::buscar/$1');
    $routes->get('/historial/(:num)/tipo/(:segment)', 'HistorialController::porTipo/$1/$2');
    $routes->get('/historial/(:num)/recientes', 'HistorialController::recientes/$1');
    
    // Estadísticas y resumen (AJAX)
    $routes->get('/historial/(:num)/estadisticas', 'HistorialController::estadisticas/$1');
    $routes->get('/historial/(:num)/resumen', 'HistorialController::resumen/$1');
    
    // Exportación
    $routes->get('/historial/(:num)/exportar/(:segment)', 'HistorialController::exportar/$1/$2');
    $routes->get('/historial/(:num)/exportar', 'HistorialController::exportar/$1');
    
    // Gestión de actividades (AJAX)
    $routes->post('/historial/eliminar/(:num)', 'HistorialController::eliminar/$1');
    
    // Adjuntos
    $routes->post('/historial/adjuntos/subir/(:num)', 'HistorialController::subirAdjunto/$1');
    $routes->get('/historial/adjuntos/descargar/(:num)', 'HistorialController::descargarAdjunto/$1');
    $routes->post('/historial/adjuntos/eliminar/(:num)', 'HistorialController::eliminarAdjunto/$1');
    
    // Tratamientos
    $routes->get('/historial/(:num)/tratamientos', 'HistorialController::tratamientos/$1');
    $routes->get('/pacientes/(:num)/tratamientos', 'HistorialController::tratamientos/$1');
    $routes->get('/tratamientos/(:num)', 'HistorialController::verTratamiento/$1');
    $routes->post('/tratamientos/(:num)/estado', 'HistorialController::actualizarEstadoTratamiento/$1');
    $routes->post('/tratamientos/(:num)/pago', 'HistorialController::registrarPagoTratamiento/$1');
    
    // ----------------------------------------
    // ODONTOGRAMA
    // ----------------------------------------
    // Vista principal del odontograma
    $routes->get('/odontograma/(:num)', 'OdontogramaController::index/$1');
    $routes->get('/pacientes/(:num)/odontograma', 'OdontogramaController::index/$1');
    
    // Vista de historial del odontograma
    $routes->get('/odontograma/(:num)/historial', 'OdontogramaController::historial/$1');
    
    // API endpoints para AJAX
    $routes->get('/odontograma/api/get/(:num)', 'OdontogramaController::getOdontograma/$1');
    $routes->get('/odontograma/api/diente/(:num)/(:num)', 'OdontogramaController::getDiente/$1/$2');
    $routes->get('/odontograma/api/historial/(:num)', 'OdontogramaController::getHistorial/$1');
    $routes->get('/odontograma/api/estados', 'OdontogramaController::getEstados');
    $routes->get('/odontograma/api/resumen/(:num)', 'OdontogramaController::getResumen/$1');
    $routes->get('/odontograma/api/tab/(:num)', 'OdontogramaController::getOdontogramaTab/$1');
    
    // API endpoints para actualizar (POST)
    $routes->post('/odontograma/api/superficie', 'OdontogramaController::actualizarSuperficie');
    $routes->post('/odontograma/api/estado-diente', 'OdontogramaController::actualizarEstadoDiente');
    $routes->post('/odontograma/api/diente', 'OdontogramaController::actualizarDiente');
});

// Verification Route (Temporary - Public)
$routes->get('/verify-presupuestos', 'VerifyPresupuestos::index');