<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Navigation;
use App\Models\DoctorScheduleModel;
use App\Models\DoctorExceptionModel;
use App\Models\DoctorPreferenceModel;

/**
 * Controlador de Agenda / Horario
 * Gestiona la configuración de horarios, excepciones y preferencias del doctor
 */
class Agenda extends BaseController
{
    protected DoctorScheduleModel $scheduleModel;
    protected DoctorExceptionModel $exceptionModel;
    protected DoctorPreferenceModel $preferenceModel;

    public function __construct()
    {
        $this->scheduleModel = new DoctorScheduleModel();
        $this->exceptionModel = new DoctorExceptionModel();
        $this->preferenceModel = new DoctorPreferenceModel();
    }

    /**
     * Obtener ID del usuario actual
     */
    private function getUsuarioId(): int
    {
        return session()->get('usuario_id') ?? 1;
    }

    /**
     * Página principal de agenda/horario
     */
    public function index()
    {
        $usuarioId = $this->getUsuarioId();

        $navigationData = Navigation::prepareNavigationData('agenda', [
            'subtitle' => 'Horario de atención'
        ]);

        $data = array_merge($navigationData, [
            'horarios'            => $this->scheduleModel->getHorariosFormateados($usuarioId),
            'preferencias'        => $this->preferenceModel->getPreferencias($usuarioId),
            'excepciones_futuras' => $this->exceptionModel->getExcepcionesFuturas($usuarioId),
            'diasSemana'          => DoctorScheduleModel::$diasSemana
        ]);

        return view('agenda/index', $data);
    }

    /**
     * Formulario para configurar horario semanal completo
     */
    public function nueva()
    {
        $usuarioId = $this->getUsuarioId();

        $navigationData = Navigation::prepareNavigationData('agenda_nueva', [
            'subtitle' => 'Configurar horario semanal'
        ]);

        $data = array_merge($navigationData, [
            'horarios'     => $this->scheduleModel->getHorariosFormateados($usuarioId),
            'preferencias' => $this->preferenceModel->getPreferencias($usuarioId),
            'diasSemana'   => DoctorScheduleModel::$diasSemana,
            'formAction'   => base_url('/agenda/guardar')
        ]);

        return view('agenda/form', $data);
    }

    /**
     * Vista de calendario
     */
    public function calendario()
    {
        $usuarioId = $this->getUsuarioId();

        $navigationData = Navigation::prepareNavigationData('calendario', [
            'subtitle' => 'Vista de calendario'
        ]);

        $data = array_merge($navigationData, [
            'fechaActual'  => date('Y-m-d'),
            'horarios'     => $this->scheduleModel->getHorariosFormateados($usuarioId),
            'excepciones'  => $this->exceptionModel->getExcepcionesFuturas($usuarioId)
        ]);

        return view('agenda/calendario', $data);
    }

    /**
     * Guardar horario semanal completo
     */
    public function guardar()
    {
        $usuarioId = $this->getUsuarioId();

        // Obtener datos del formulario
        $horariosData = $this->request->getPost('horario') ?? [];
        $preferenciasData = $this->request->getPost('preferencias') ?? [];

        // Guardar horarios
        $horariosGuardados = $this->scheduleModel->guardarHorariosCompletos($usuarioId, $horariosData);

        // Guardar preferencias
        $preferenciasGuardadas = $this->preferenceModel->guardarPreferencias($usuarioId, $preferenciasData);

        if ($horariosGuardados && $preferenciasGuardadas) {
            return redirect()->to('/agenda')
                   ->with('success', 'Horario guardado correctamente.');
        } else {
            return redirect()->back()
                   ->with('error', 'Error al guardar el horario. Por favor, intente de nuevo.')
                   ->withInput();
        }
    }

    /**
     * Vista de excepciones de horario
     */
    public function excepciones()
    {
        $usuarioId = $this->getUsuarioId();

        $navigationData = Navigation::prepareNavigationData('agenda_excepciones', [
            'subtitle' => 'Días no disponibles'
        ]);

        $data = array_merge($navigationData, [
            'excepciones' => $this->exceptionModel->getExcepcionesDoctor($usuarioId)
        ]);

        return view('agenda/excepciones', $data);
    }

    /**
     * Guardar nueva excepción
     */
    public function guardarExcepcion()
    {
        $usuarioId = $this->getUsuarioId();

        $datos = [
            'fecha'       => $this->request->getPost('fecha'),
            'motivo'      => $this->request->getPost('motivo'),
            'todo_el_dia' => $this->request->getPost('todo_el_dia'),
            'hora_inicio' => $this->request->getPost('hora_inicio'),
            'hora_fin'    => $this->request->getPost('hora_fin'),
        ];

        // Validar datos
        if (empty($datos['fecha'])) {
            return redirect()->back()
                   ->with('error', 'La fecha es obligatoria.')
                   ->withInput();
        }

        // Verificar que no exista ya una excepción para esa fecha
        if ($this->exceptionModel->tieneExcepcion($usuarioId, $datos['fecha'])) {
            return redirect()->back()
                   ->with('error', 'Ya existe una excepción para esa fecha.')
                   ->withInput();
        }

        if ($this->exceptionModel->agregarExcepcion($usuarioId, $datos)) {
            return redirect()->to('/agenda/excepciones')
                   ->with('success', 'Excepción agregada correctamente.');
        } else {
            return redirect()->back()
                   ->with('error', 'Error al guardar la excepción.')
                   ->withInput();
        }
    }

    /**
     * Eliminar excepción
     */
    public function eliminarExcepcion($id)
    {
        $usuarioId = $this->getUsuarioId();

        if ($this->exceptionModel->eliminarExcepcion((int)$id, $usuarioId)) {
            return redirect()->to('/agenda/excepciones')
                   ->with('success', 'Excepción eliminada correctamente.');
        } else {
            return redirect()->back()
                   ->with('error', 'Error al eliminar la excepción.');
        }
    }

    /**
     * API: Obtener horarios disponibles para una fecha
     * Usado por el sistema de citas para mostrar slots disponibles
     */
    public function getHorariosDisponiblesApi()
    {
        $usuarioId = $this->request->getGet('usuario_id') ?? $this->getUsuarioId();
        $fecha = $this->request->getGet('fecha') ?? date('Y-m-d');

        // Obtener día de la semana (1=Lunes, 7=Domingo)
        $diaSemana = (int) date('N', strtotime($fecha));

        // Verificar si hay excepción para esa fecha
        if ($this->exceptionModel->noDisponiblePorExcepcion($usuarioId, $fecha)) {
            return $this->response->setJSON([
                'success' => true,
                'slots'   => [],
                'message' => 'No disponible por excepción'
            ]);
        }

        // Obtener horario del día
        $horario = $this->scheduleModel->getHorarioPorDoctorYDia($usuarioId, $diaSemana);

        if (!$horario || !$horario['activo']) {
            return $this->response->setJSON([
                'success' => true,
                'slots'   => [],
                'message' => 'No hay horario configurado para este día'
            ]);
        }

        // Obtener preferencias
        $preferencias = $this->preferenceModel->getPreferencias($usuarioId);
        $duracionCita = $preferencias['duracion_cita'];
        $descanso = $preferencias['tiempo_descanso'];

        // Generar slots disponibles
        $slots = [];
        $horaActual = strtotime($fecha . ' ' . $horario['hora_inicio']);
        $horaFin = strtotime($fecha . ' ' . $horario['hora_fin']);

        while ($horaActual + ($duracionCita * 60) <= $horaFin) {
            $slots[] = date('H:i', $horaActual);
            $horaActual += ($duracionCita + $descanso) * 60;
        }

        return $this->response->setJSON([
            'success' => true,
            'slots'   => $slots,
            'horario' => [
                'inicio'   => substr($horario['hora_inicio'], 0, 5),
                'fin'      => substr($horario['hora_fin'], 0, 5),
                'duracion' => $duracionCita
            ]
        ]);
    }

    /**
     * Vista previa del horario semanal
     */
    public function preview()
    {
        $usuarioId = $this->getUsuarioId();

        $navigationData = Navigation::prepareNavigationData('agenda_preview', [
            'subtitle' => 'Vista previa del horario'
        ]);

        $data = array_merge($navigationData, [
            'horarios'     => $this->scheduleModel->getHorariosFormateados($usuarioId),
            'preferencias' => $this->preferenceModel->getPreferencias($usuarioId),
            'excepciones'  => $this->exceptionModel->getExcepcionesFuturas($usuarioId),
            'diasSemana'   => DoctorScheduleModel::$diasSemana
        ]);

        return view('agenda/preview', $data);
    }
}
