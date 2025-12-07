<?php

namespace App\Controllers;

use App\Models\Patient;
use App\Models\HistorialActividadesModel;
use App\Models\CitasModel;
use App\Models\OdontogramaModel;
use App\Controllers\Navigation;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;

class PatientController extends Controller
{
    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new Patient();
        helper('form');
    }

    /**
     * Muestra el listado de pacientes con paginación, búsqueda y filtros
     */
    public function index()
    {
        // Obtener parámetros de búsqueda y filtros
        $search = $this->request->getVar('search');
        $estado = $this->request->getVar('estado');
        
        // Sanitizar entrada
        $search = $search ? trim($search) : null;
        $estado = $estado ? trim($estado) : null;
        
        // Validar que el estado sea válido
        if ($estado && !in_array($estado, ['activo', 'inactivo'])) {
            $estado = null;
        }

        // Obtener pacientes con búsqueda y filtros
        $result = $this->patientModel->getPatients($search, $estado, 20);
        
        // Preparar datos de navegación usando el controller Navigation
        $navigationData = Navigation::prepareNavigationData('pacientes', [
            'subtitle' => 'Gestión de pacientes'
        ]);
        
        // Debug: Log the pageTitle value
        log_message('debug', 'PatientController::index() - pageTitle: ' . ($navigationData['pageTitle'] ?? 'NOT SET'));
        
        $data = array_merge($navigationData, [
            'patients' => $result['patients'],
            'pager' => $result['pager'],
            'search' => $search,
            'estado' => $estado,
        ]);

        return view('patients/index', $data);
    }

    /**
     * Muestra el formulario para crear un nuevo paciente
     */
    public function create()
    {
        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('paciente_nuevo', [
            'subtitle' => 'Registrar nuevo paciente'
        ]);
        
        $data = array_merge($navigationData, [
            'validation' => \Config\Services::validation(),
        ]);

        return view('patients/create', $data);
    }

    /**
     * Guarda un nuevo paciente en la base de datos
     */
    public function store()
    {
        $rules = [
            'nombre' => 'required|min_length[2]|max_length[100]',
            'primer_apellido' => 'required|min_length[2]|max_length[100]',
            'segundo_apellido' => 'permit_empty|max_length[100]',
            'email' => 'valid_email|max_length[100]|is_unique[pacientes.email]',
            'telefono' => 'max_length[20]',
            'celular' => 'permit_empty|max_length[20]',
            'fecha_nacimiento' => 'required|valid_date[Y-m-d]',
            'domicilio' => 'permit_empty|max_length[255]',
            'nacionalidad' => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nombre' => $this->request->getVar('nombre'),
            'primer_apellido' => $this->request->getVar('primer_apellido'),
            'segundo_apellido' => $this->request->getVar('segundo_apellido'),
            'email' => $this->request->getVar('email'),
            'telefono' => $this->request->getVar('telefono'),
            'celular' => $this->request->getVar('celular'),
            'fecha_nacimiento' => $this->request->getVar('fecha_nacimiento'),
            'domicilio' => $this->request->getVar('domicilio'),
            'nacionalidad' => $this->request->getVar('nacionalidad'),
        ];

        $pacienteId = $this->patientModel->insert($data);

        // Crear odontograma automáticamente para el nuevo paciente
        if ($pacienteId) {
            $odontogramaModel = new OdontogramaModel();
            $odontogramaModel->getOrCreateOdontograma($pacienteId);
            
            // Registrar creación en historial
            $historialModel = new \App\Models\HistorialActividadesModel();
            $historialModel->registrarActividad(
                $pacienteId,
                'paciente',
                $pacienteId,
                'Paciente registrado - ' . $data['nombre'] . ' ' . $data['primer_apellido']
            );
        }

        return redirect()->to('/pacientes')->with('success', 'Paciente creado exitosamente.');
    }

    /**
     * Muestra los detalles de un paciente específico
     */
    public function show($id = null)
    {
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return redirect()->to('/pacientes')->with('error', 'Paciente no encontrado.');
        }

        // Preparar datos de navegación con contexto del paciente
        $nombreCompleto = trim(($patient['nombre'] ?? '') . ' ' . ($patient['apellido'] ?? ''));
        $navigationData = Navigation::prepareNavigationData('paciente_ver', [
            'subtitle' => 'Información del paciente',
            'paciente' => $patient
        ]);
        
        $data = array_merge($navigationData, [
            'patient' => $patient,
        ]);

        return view('patients/show', $data);
    }

    /**
     * Muestra el formulario para editar un paciente
     */
    public function edit($id = null)
    {
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return redirect()->to('/pacientes')->with('error', 'Paciente no encontrado.');
        }

        // Preparar datos de navegación con contexto del paciente
        $navigationData = Navigation::prepareNavigationData('paciente_editar', [
            'subtitle' => 'Modificar información del paciente',
            'paciente' => $patient
        ]);
        
        $data = array_merge($navigationData, [
            'patient' => $patient,
            'validation' => \Config\Services::validation(),
        ]);

        return view('patients/edit', $data);
    }

    /**
     * Actualiza los datos de un paciente
     */
    public function update($id = null)
    {
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return redirect()->to('/pacientes')->with('error', 'Paciente no encontrado.');
        }

        $rules = [
            'nombre' => 'required|min_length[2]|max_length[100]',
            'primer_apellido' => 'required|min_length[2]|max_length[100]',
            'segundo_apellido' => 'permit_empty|max_length[100]',
            'email' => "valid_email|max_length[100]|is_unique[pacientes.email,id,{$id}]",
            'telefono' => 'max_length[20]',
            'celular' => 'permit_empty|max_length[20]',
            'fecha_nacimiento' => 'required|valid_date[Y-m-d]',
            'domicilio' => 'permit_empty|max_length[255]',
            'nacionalidad' => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nombre' => $this->request->getVar('nombre'),
            'primer_apellido' => $this->request->getVar('primer_apellido'),
            'segundo_apellido' => $this->request->getVar('segundo_apellido'),
            'email' => $this->request->getVar('email'),
            'telefono' => $this->request->getVar('telefono'),
            'celular' => $this->request->getVar('celular'),
            'fecha_nacimiento' => $this->request->getVar('fecha_nacimiento'),
            'domicilio' => $this->request->getVar('domicilio'),
            'nacionalidad' => $this->request->getVar('nacionalidad'),
        ];

        $this->patientModel->update($id, $data);
        
        // Registrar actualización en historial
        $historialModel = new \App\Models\HistorialActividadesModel();
        $historialModel->registrarActividad(
            $id,
            'paciente',
            $id,
            'Información del paciente actualizada'
        );

        return redirect()->to('/pacientes')->with('success', 'Paciente actualizado exitosamente.');
    }

    /**
     * Elimina un paciente (soft delete)
     */
    public function delete($id = null)
    {
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return redirect()->to('/pacientes')->with('error', 'Paciente no encontrado.');
        }
        
        // Registrar eliminación en historial antes de eliminar
        $historialModel = new \App\Models\HistorialActividadesModel();
        $historialModel->registrarActividad(
            $id,
            'paciente',
            $id,
            'Paciente eliminado - ' . $patient['nombre'] . ' ' . $patient['primer_apellido']
        );

        $this->patientModel->delete($id);

        return redirect()->to('/pacientes')->with('success', 'Paciente eliminado exitosamente.');
    }

    /**
     * Genera un PDF con la ficha completa del paciente
     */
    public function pdf($id = null)
    {
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return redirect()->to('/pacientes')->with('error', 'Paciente no encontrado.');
        }

        // Cargar datos adicionales del paciente
        $historialModel = new HistorialActividadesModel();
        
        // Obtener estadísticas básicas
        $estadisticas = $historialModel->getEstadisticas($id);
        
        // Obtener últimas actividades
        $ultimasActividades = $historialModel->getTimelineByPaciente($id, 10, 0, []);

        $data = [
            'patient' => $patient,
            'estadisticas' => $estadisticas,
            'actividades' => $ultimasActividades,
            'fecha_generacion' => date('d/m/Y H:i'),
        ];

        // Generar HTML del PDF
        $html = view('patients/pdf', $data);

        // Configurar DomPDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Nombre del archivo
        $nombreArchivo = 'Ficha_Paciente_' . $patient['id'] . '_' . date('Ymd') . '.pdf';

        // Enviar al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $nombreArchivo . '"')
            ->setBody($dompdf->output());
    }
}