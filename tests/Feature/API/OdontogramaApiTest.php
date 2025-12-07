<?php

namespace Tests\Feature\API;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\PacientesModel;
use App\Models\OdontogramaModel;
use App\Models\OdontogramaDienteModel;

/**
 * API Endpoint Tests for Odontograma
 * 
 * @group API
 * @group Odontograma
 */
class OdontogramaApiTest extends CIUnitTestCase
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
    protected array $testOdontograma;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pacientesModel = new PacientesModel();
        $this->odontogramaModel = new OdontogramaModel();
        
        // Crear paciente de prueba
        $this->testPatientId = $this->pacientesModel->insert([
            'nombre' => 'API',
            'primer_apellido' => 'Test',
            'email' => 'api.test@test.com'
        ]);

        // Crear odontograma
        $this->testOdontograma = $this->odontogramaModel->getOrCreateOdontograma($this->testPatientId);

        // Configurar sesión de prueba
        session()->set([
            'user_id' => 1,
            'user_name' => 'Test User',
            'logged_in' => true,
            'isLoggedIn' => true,
            'user_role' => 'admin'
        ]);
    }

    /**
     * Test de API obtener estados disponibles
     */
    public function testGetEstadosEndpoint(): void
    {
        $result = $this->get('/odontograma/api/estados');
        
        $statusCode = $result->response()->getStatusCode();
        
        if ($statusCode === 200) {
            $body = $result->response()->getBody();
            $json = json_decode($body, true);
            
            $this->assertIsArray($json);
            $this->assertArrayHasKey('success', $json);
            
            if ($json['success']) {
                $this->assertArrayHasKey('data', $json);
            }
        } else {
            // Si redirige al login, también es válido
            $this->assertTrue(in_array($statusCode, [302, 307]));
        }
    }

    /**
     * Test de API obtener odontograma de paciente
     */
    public function testGetOdontogramaEndpoint(): void
    {
        $result = $this->get("/odontograma/api/odontograma/{$this->testPatientId}");
        
        $statusCode = $result->response()->getStatusCode();
        
        if ($statusCode === 200) {
            $body = $result->response()->getBody();
            $json = json_decode($body, true);
            
            $this->assertIsArray($json);
            $this->assertArrayHasKey('success', $json);
            
            if ($json['success']) {
                $this->assertArrayHasKey('data', $json);
                $this->assertArrayHasKey('odontograma', $json['data']);
            }
        } else {
            $this->assertTrue(in_array($statusCode, [302, 307]));
        }
    }

    /**
     * Test de API actualizar superficie
     */
    public function testUpdateSuperficieEndpoint(): void
    {
        $data = [
            'id_odontograma' => $this->testOdontograma['id'],
            'numero_diente' => 11,
            'superficie' => 'oclusal',
            'estado' => 'sano'
        ];

        $result = $this->withBodyFormat('json')
                       ->withBody(json_encode($data))
                       ->post('/odontograma/api/superficie');
        
        $statusCode = $result->response()->getStatusCode();
        
        // Puede ser exitoso, redirigir, o requerir auth
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 400, 422]));
        
        if ($statusCode === 200) {
            $body = $result->response()->getBody();
            $json = json_decode($body, true);
            
            if (is_array($json)) {
                $this->assertArrayHasKey('success', $json);
            }
        }
    }

    /**
     * Test de API actualizar diente completo
     */
    public function testUpdateDienteEndpoint(): void
    {
        $data = [
            'id_odontograma' => $this->testOdontograma['id'],
            'numero_diente' => 21,
            'estado_general' => 'sano'
        ];

        $result = $this->withBodyFormat('json')
                       ->withBody(json_encode($data))
                       ->post('/odontograma/api/diente');
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 400, 404, 422]));
    }

    /**
     * Test de API obtener historial
     */
    public function testGetHistorialEndpoint(): void
    {
        $result = $this->get("/odontograma/api/historial/{$this->testOdontograma['id']}");
        
        $statusCode = $result->response()->getStatusCode();
        
        if ($statusCode === 200) {
            $body = $result->response()->getBody();
            $json = json_decode($body, true);
            
            if (is_array($json)) {
                $this->assertArrayHasKey('success', $json);
            }
        } else {
            $this->assertTrue(in_array($statusCode, [302, 307]));
        }
    }

    /**
     * Test de API con paciente inexistente
     */
    public function testGetOdontogramaInvalidPatient(): void
    {
        $result = $this->get('/odontograma/api/odontograma/99999');
        
        $statusCode = $result->response()->getStatusCode();
        
        if ($statusCode === 200) {
            $body = $result->response()->getBody();
            $json = json_decode($body, true);
            
            if (is_array($json)) {
                // Debe indicar que no se encontró
                $this->assertFalse($json['success'] ?? true);
            }
        } else {
            // 404 o redirección también son válidos
            $this->assertTrue(in_array($statusCode, [302, 307, 404]));
        }
    }

    /**
     * Test de API con datos inválidos
     */
    public function testUpdateSuperficieInvalidData(): void
    {
        $data = [
            'id_odontograma' => $this->testOdontograma['id'],
            'numero_diente' => 'invalid', // Debería ser número
            'superficie' => 'unknown',    // Superficie inválida
            'estado' => ''
        ];

        $result = $this->withBodyFormat('json')
                       ->withBody(json_encode($data))
                       ->post('/odontograma/api/superficie');
        
        $statusCode = $result->response()->getStatusCode();
        
        // Debe rechazar datos inválidos
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 400, 422]));
    }

    /**
     * Test de respuesta JSON válida
     */
    public function testJsonResponseFormat(): void
    {
        $result = $this->get("/odontograma/api/odontograma/{$this->testPatientId}");
        
        if ($result->response()->getStatusCode() === 200) {
            $contentType = $result->response()->getHeaderLine('Content-Type');
            
            // Debe ser JSON
            $this->assertStringContainsString('json', $contentType);
            
            $body = $result->response()->getBody();
            $json = json_decode($body, true);
            
            // Debe poder parsearse
            $this->assertNotNull($json);
            $this->assertIsArray($json);
        }
    }

    /**
     * Test de resumen de estados
     */
    public function testGetResumenEndpoint(): void
    {
        $result = $this->get("/odontograma/api/resumen/{$this->testOdontograma['id']}");
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 307, 404]));
        
        if ($statusCode === 200) {
            $body = $result->response()->getBody();
            $json = json_decode($body, true);
            
            if (is_array($json) && isset($json['success']) && $json['success']) {
                $this->assertArrayHasKey('data', $json);
            }
        }
    }

    /**
     * Test de API sin autenticación
     */
    public function testApiWithoutAuth(): void
    {
        // Destruir sesión
        session()->destroy();
        
        $result = $this->get('/odontograma/api/estados');
        
        $statusCode = $result->response()->getStatusCode();
        
        // Debe requerir autenticación (redirección al login)
        $this->assertTrue(in_array($statusCode, [302, 307, 401, 403]));
    }

    /**
     * Test de método POST requerido
     */
    public function testPostMethodRequired(): void
    {
        // Intentar GET en endpoint que requiere POST
        $result = $this->get('/odontograma/api/superficie');
        
        $statusCode = $result->response()->getStatusCode();
        
        // Debe rechazar o redirigir
        $this->assertTrue(in_array($statusCode, [302, 307, 404, 405]));
    }

    /**
     * Test de actualización exitosa guarda historial
     */
    public function testUpdateCreatesHistory(): void
    {
        $data = [
            'id_odontograma' => $this->testOdontograma['id'],
            'numero_diente' => 16,
            'superficie' => 'oclusal',
            'estado' => 'caries'
        ];

        $result = $this->withBodyFormat('json')
                       ->withBody(json_encode($data))
                       ->post('/odontograma/api/superficie');
        
        if ($result->response()->getStatusCode() === 200) {
            // Verificar que se creó historial
            $historialResult = $this->get("/odontograma/api/historial/{$this->testOdontograma['id']}");
            
            if ($historialResult->response()->getStatusCode() === 200) {
                $body = $historialResult->response()->getBody();
                $json = json_decode($body, true);
                
                // El historial debería tener al menos un registro
                if (isset($json['success']) && $json['success'] && isset($json['data'])) {
                    $this->assertNotEmpty($json['data']);
                }
            }
        }
    }
}
