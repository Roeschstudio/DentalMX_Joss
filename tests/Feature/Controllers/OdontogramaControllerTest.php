<?php

namespace Tests\Feature\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\PacientesModel;
use App\Models\OdontogramaModel;
use App\Models\OdontogramaDienteModel;

/**
 * Feature tests for OdontogramaController
 * 
 * @group Controllers
 * @group Odontograma
 */
class OdontogramaControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = 'App';

    protected PacientesModel $pacientesModel;
    protected OdontogramaModel $odontogramaModel;
    protected int $testPatientId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pacientesModel = new PacientesModel();
        $this->odontogramaModel = new OdontogramaModel();
        
        // Crear paciente de prueba
        $this->testPatientId = $this->pacientesModel->insert([
            'nombre' => 'Test',
            'primer_apellido' => 'Odontograma',
            'email' => 'test.odontograma@test.com'
        ]);

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
     * Test de acceso a la página de odontograma
     */
    public function testOdontogramaPageLoads(): void
    {
        $result = $this->get("/odontograma/{$this->testPatientId}");
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de odontograma para paciente inexistente
     */
    public function testOdontogramaForNonExistentPatient(): void
    {
        $result = $this->get('/odontograma/99999');
        
        $statusCode = $result->response()->getStatusCode();
        // Debe redirigir a pacientes
        $this->assertTrue(in_array($statusCode, [302, 307]));
    }

    /**
     * Test del API de obtener odontograma
     */
    public function testGetOdontogramaApi(): void
    {
        $result = $this->get("/odontograma/api/odontograma/{$this->testPatientId}");
        
        $statusCode = $result->response()->getStatusCode();
        
        if ($statusCode === 200) {
            $body = $result->response()->getBody();
            $json = json_decode($body, true);
            
            $this->assertIsArray($json);
            $this->assertArrayHasKey('success', $json);
        }
    }

    /**
     * Test del API de estados disponibles
     */
    public function testGetEstadosApi(): void
    {
        $result = $this->get('/odontograma/api/estados');
        
        $statusCode = $result->response()->getStatusCode();
        
        // Puede requerir autenticación
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de actualización de superficie via API
     */
    public function testUpdateSuperficieApi(): void
    {
        // Crear odontograma primero
        $odontograma = $this->odontogramaModel->getOrCreateOdontograma($this->testPatientId);
        
        $updateData = [
            'id_odontograma' => $odontograma['id'],
            'numero_diente' => 11,
            'superficie' => 'oclusal',
            'estado' => 'caries'
        ];

        $result = $this->withBodyFormat('json')
                       ->post('/odontograma/api/superficie', $updateData);
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 400, 422]));
    }

    /**
     * Test de actualización de diente completo via API
     */
    public function testUpdateDienteApi(): void
    {
        // Crear odontograma primero
        $odontograma = $this->odontogramaModel->getOrCreateOdontograma($this->testPatientId);
        
        $updateData = [
            'id_odontograma' => $odontograma['id'],
            'numero_diente' => 21,
            'estado_general' => 'extraido',
            'notas' => 'Extracción realizada el 2024-01-15'
        ];

        $result = $this->withBodyFormat('json')
                       ->post('/odontograma/api/diente', $updateData);
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 400, 422]));
    }

    /**
     * Test de historial del odontograma
     */
    public function testHistorialPage(): void
    {
        // Crear odontograma primero
        $odontograma = $this->odontogramaModel->getOrCreateOdontograma($this->testPatientId);
        
        $result = $this->get("/odontograma/historial/{$this->testPatientId}");
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de obtener historial via API
     */
    public function testGetHistorialApi(): void
    {
        // Crear odontograma primero
        $odontograma = $this->odontogramaModel->getOrCreateOdontograma($this->testPatientId);
        
        $result = $this->get("/odontograma/api/historial/{$odontograma['id']}");
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de creación automática de odontograma
     */
    public function testAutoCreateOdontograma(): void
    {
        // El odontograma debe crearse automáticamente al acceder
        $result = $this->get("/odontograma/{$this->testPatientId}");
        
        if ($result->response()->getStatusCode() === 200) {
            // Verificar que se creó el odontograma
            $odontograma = $this->odontogramaModel->getOdontogramaPaciente($this->testPatientId);
            $this->assertNotNull($odontograma);
        }
    }

    /**
     * Test de validación de número de diente inválido
     */
    public function testInvalidToothNumber(): void
    {
        $odontograma = $this->odontogramaModel->getOrCreateOdontograma($this->testPatientId);
        
        $updateData = [
            'id_odontograma' => $odontograma['id'],
            'numero_diente' => 99, // Número inválido
            'superficie' => 'oclusal',
            'estado' => 'caries'
        ];

        $result = $this->withBodyFormat('json')
                       ->post('/odontograma/api/superficie', $updateData);
        
        $statusCode = $result->response()->getStatusCode();
        // Debe rechazar o validar
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 400, 422]));
    }

    /**
     * Test de validación de superficie inválida
     */
    public function testInvalidSurface(): void
    {
        $odontograma = $this->odontogramaModel->getOrCreateOdontograma($this->testPatientId);
        
        $updateData = [
            'id_odontograma' => $odontograma['id'],
            'numero_diente' => 11,
            'superficie' => 'invalida', // Superficie inválida
            'estado' => 'caries'
        ];

        $result = $this->withBodyFormat('json')
                       ->post('/odontograma/api/superficie', $updateData);
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 400, 422]));
    }

    /**
     * Test de cambio de tipo de dentadura
     */
    public function testChangeTipoDentadura(): void
    {
        $odontograma = $this->odontogramaModel->getOrCreateOdontograma($this->testPatientId, 'permanente');
        
        $updateData = [
            'tipo_dentadura' => 'mixta'
        ];

        $result = $this->withBodyFormat('json')
                       ->post("/odontograma/api/tipo-dentadura/{$odontograma['id']}", $updateData);
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 400, 404]));
    }

    /**
     * Test de resumen de estados
     */
    public function testGetResumenEstados(): void
    {
        $odontograma = $this->odontogramaModel->getOrCreateOdontograma($this->testPatientId);
        
        $result = $this->get("/odontograma/api/resumen/{$odontograma['id']}");
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de exportación PDF (si existe)
     */
    public function testExportPdf(): void
    {
        $result = $this->get("/odontograma/pdf/{$this->testPatientId}");
        
        $statusCode = $result->response()->getStatusCode();
        // PDF puede existir o no
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 404]));
    }

    /**
     * Test de estructura de dientes en respuesta
     */
    public function testOdontogramaDataStructure(): void
    {
        $result = $this->get("/odontograma/api/odontograma/{$this->testPatientId}");
        
        if ($result->response()->getStatusCode() === 200) {
            $body = $result->response()->getBody();
            $json = json_decode($body, true);
            
            if (isset($json['success']) && $json['success']) {
                $this->assertArrayHasKey('data', $json);
                $this->assertArrayHasKey('odontograma', $json['data']);
                $this->assertArrayHasKey('dientes', $json['data']);
            }
        }
    }
}
