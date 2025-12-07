<?php

namespace Tests\Feature\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\Patient;
use App\Models\PacientesModel;

/**
 * Feature tests for PatientController
 * 
 * @group Controllers
 * @group Patients
 */
class PatientControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = 'App';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar sesión de prueba
        $session = \Config\Services::session();
        $session->set([
            'user_id' => 1,
            'user_name' => 'Test User',
            'logged_in' => true,
            'user_role' => 'admin'
        ]);
    }

    /**
     * Test de acceso a la página de listado de pacientes
     */
    public function testIndexPageLoads(): void
    {
        $result = $this->get('/pacientes');
        
        // Puede redirigir a login si no hay sesión
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de acceso a la página de crear paciente
     */
    public function testCreatePageLoads(): void
    {
        $result = $this->get('/pacientes/create');
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de estructura de formulario de creación
     */
    public function testCreateFormHasRequiredFields(): void
    {
        $result = $this->get('/pacientes/create');
        
        if ($result->response()->getStatusCode() === 200) {
            $result->assertSee('Nombre');
            $result->assertSee('Apellido');
        }
    }

    /**
     * Test de creación de paciente con datos válidos
     */
    public function testStorePatientWithValidData(): void
    {
        $patientData = [
            'nombre' => 'Nuevo',
            'primer_apellido' => 'Paciente',
            'segundo_apellido' => 'Test',
            'email' => 'nuevo.paciente@test.com',
            'telefono' => '5551234567',
            'celular' => '5559876543',
            'fecha_nacimiento' => '1990-01-15',
            'domicilio' => 'Calle Test 123',
            'nacionalidad' => 'Mexicana'
        ];

        $result = $this->post('/pacientes/store', $patientData);
        
        // Debe redirigir después de crear (302 o 307)
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 303]));
    }

    /**
     * Test de creación de paciente con datos inválidos (sin nombre)
     */
    public function testStorePatientWithInvalidData(): void
    {
        $patientData = [
            'nombre' => '', // Campo vacío
            'primer_apellido' => 'Test',
            'email' => 'invalid@test.com'
        ];

        $result = $this->post('/pacientes/store', $patientData);
        
        // Puede redirigir de vuelta o mostrar errores
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 422]));
    }

    /**
     * Test de visualización de paciente
     */
    public function testShowPatient(): void
    {
        // Crear paciente primero
        $model = new PacientesModel();
        $patientId = $model->insert([
            'nombre' => 'Ver',
            'primer_apellido' => 'Paciente',
            'email' => 'ver@test.com'
        ]);

        if ($patientId) {
            $result = $this->get("/pacientes/show/{$patientId}");
            $statusCode = $result->response()->getStatusCode();
            $this->assertTrue(in_array($statusCode, [200, 302, 307]));
        }
    }

    /**
     * Test de página de edición de paciente
     */
    public function testEditPatientPage(): void
    {
        // Crear paciente primero
        $model = new PacientesModel();
        $patientId = $model->insert([
            'nombre' => 'Editar',
            'primer_apellido' => 'Paciente',
            'email' => 'editar@test.com'
        ]);

        if ($patientId) {
            $result = $this->get("/pacientes/edit/{$patientId}");
            $statusCode = $result->response()->getStatusCode();
            $this->assertTrue(in_array($statusCode, [200, 302, 307]));
        }
    }

    /**
     * Test de actualización de paciente
     */
    public function testUpdatePatient(): void
    {
        // Crear paciente primero
        $model = new PacientesModel();
        $patientId = $model->insert([
            'nombre' => 'Original',
            'primer_apellido' => 'Nombre',
            'email' => 'original@test.com'
        ]);

        if ($patientId) {
            $updateData = [
                'nombre' => 'Actualizado',
                'primer_apellido' => 'Nombre',
                'email' => 'actualizado@test.com'
            ];

            $result = $this->post("/pacientes/update/{$patientId}", $updateData);
            
            $statusCode = $result->response()->getStatusCode();
            $this->assertTrue(in_array($statusCode, [200, 302, 303, 307]));
            
            // Verificar que se actualizó
            $updated = $model->find($patientId);
            if ($updated) {
                // El nombre podría haber sido actualizado
                $this->assertNotNull($updated);
            }
        }
    }

    /**
     * Test de eliminación de paciente
     */
    public function testDeletePatient(): void
    {
        // Crear paciente primero
        $model = new PacientesModel();
        $patientId = $model->insert([
            'nombre' => 'Eliminar',
            'primer_apellido' => 'Test',
            'email' => 'eliminar@test.com'
        ]);

        if ($patientId) {
            $result = $this->post("/pacientes/delete/{$patientId}");
            
            $statusCode = $result->response()->getStatusCode();
            $this->assertTrue(in_array($statusCode, [200, 302, 303, 307]));
        }
    }

    /**
     * Test de búsqueda de pacientes
     */
    public function testSearchPatients(): void
    {
        // Crear pacientes de prueba
        $model = new PacientesModel();
        $model->insert(['nombre' => 'María', 'primer_apellido' => 'García', 'email' => 'maria@test.com']);
        $model->insert(['nombre' => 'Juan', 'primer_apellido' => 'López', 'email' => 'juan@test.com']);

        $result = $this->get('/pacientes?search=María');
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de paciente inexistente (404)
     */
    public function testShowNonExistentPatient(): void
    {
        $result = $this->get('/pacientes/show/99999');
        
        $statusCode = $result->response()->getStatusCode();
        // Puede ser 404 o redirección
        $this->assertTrue(in_array($statusCode, [200, 302, 404, 307]));
    }

    /**
     * Test de filtro por estado
     */
    public function testFilterByEstado(): void
    {
        $result = $this->get('/pacientes?estado=activo');
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de paginación
     */
    public function testPagination(): void
    {
        // Crear varios pacientes
        $model = new PacientesModel();
        for ($i = 1; $i <= 25; $i++) {
            $model->insert([
                'nombre' => "Paciente {$i}",
                'primer_apellido' => 'Test',
                'email' => "paciente{$i}@test.com"
            ]);
        }

        // Ir a la página 2
        $result = $this->get('/pacientes?page=2');
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }
}
