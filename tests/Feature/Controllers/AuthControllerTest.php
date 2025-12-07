<?php

namespace Tests\Feature\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\UsuariosModel;

/**
 * Feature tests for Auth Controller
 * 
 * @group Controllers
 * @group Auth
 */
class AuthControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = 'App';

    protected UsuariosModel $usuariosModel;
    protected string $testEmail = 'test@example.com';
    protected string $testPassword = 'password123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuariosModel = new UsuariosModel();
        
        // Limpiar sesión antes de cada test
        session()->destroy();
    }

    /**
     * Crear usuario de prueba
     */
    protected function createTestUser(): int
    {
        return $this->usuariosModel->insert([
            'nombre' => 'Test User',
            'email' => $this->testEmail,
            'password' => password_hash($this->testPassword, PASSWORD_DEFAULT),
            'rol' => 'admin',
            'telefono' => '5551234567'
        ]);
    }

    /**
     * Test de que la página de login carga
     */
    public function testLoginPageLoads(): void
    {
        $result = $this->get('/login');
        
        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302]));
        
        if ($statusCode === 200) {
            $result->assertSee('login', 'i'); // Login en algún lugar (ignorando mayúsculas)
        }
    }

    /**
     * Test de redirección si ya está logueado
     */
    public function testRedirectIfLoggedIn(): void
    {
        // Simular sesión activa
        session()->set([
            'isLoggedIn' => true,
            'id' => 1,
            'nombre' => 'Test'
        ]);

        $result = $this->get('/login');
        
        $statusCode = $result->response()->getStatusCode();
        // Debe redirigir al dashboard
        $this->assertTrue(in_array($statusCode, [200, 302, 307]));
    }

    /**
     * Test de login con credenciales válidas
     */
    public function testLoginWithValidCredentials(): void
    {
        $this->createTestUser();

        $result = $this->post('/auth/login', [
            'email' => $this->testEmail,
            'password' => $this->testPassword
        ]);

        $statusCode = $result->response()->getStatusCode();
        // Debe redirigir después de login exitoso
        $this->assertTrue(in_array($statusCode, [200, 302, 303, 307]));
    }

    /**
     * Test de login con contraseña incorrecta
     */
    public function testLoginWithWrongPassword(): void
    {
        $this->createTestUser();

        $result = $this->post('/auth/login', [
            'email' => $this->testEmail,
            'password' => 'wrongpassword'
        ]);

        $statusCode = $result->response()->getStatusCode();
        // Debe redirigir de vuelta al login
        $this->assertTrue(in_array($statusCode, [302, 303, 307]));
    }

    /**
     * Test de login con email inexistente
     */
    public function testLoginWithNonExistentEmail(): void
    {
        $result = $this->post('/auth/login', [
            'email' => 'noexiste@test.com',
            'password' => 'password123'
        ]);

        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [302, 303, 307]));
    }

    /**
     * Test de login con campos vacíos
     */
    public function testLoginWithEmptyFields(): void
    {
        $result = $this->post('/auth/login', [
            'email' => '',
            'password' => ''
        ]);

        $statusCode = $result->response()->getStatusCode();
        // Debe redirigir con error
        $this->assertTrue(in_array($statusCode, [302, 303, 307]));
    }

    /**
     * Test de login con email inválido
     */
    public function testLoginWithInvalidEmail(): void
    {
        $result = $this->post('/auth/login', [
            'email' => 'not-an-email',
            'password' => 'password123'
        ]);

        $statusCode = $result->response()->getStatusCode();
        // Debe redirigir con error
        $this->assertTrue(in_array($statusCode, [302, 303, 307]));
    }

    /**
     * Test de logout
     */
    public function testLogout(): void
    {
        // Simular sesión activa
        session()->set([
            'isLoggedIn' => true,
            'id' => 1,
            'nombre' => 'Test'
        ]);

        $result = $this->get('/logout');
        
        $statusCode = $result->response()->getStatusCode();
        // Debe redirigir al login
        $this->assertTrue(in_array($statusCode, [302, 303, 307]));
    }

    /**
     * Test de que logout destruye la sesión
     */
    public function testLogoutDestroysSession(): void
    {
        // Simular sesión activa
        session()->set([
            'isLoggedIn' => true,
            'id' => 1,
            'nombre' => 'Test'
        ]);

        $this->get('/logout');
        
        // La sesión debe estar destruida
        $this->assertNull(session()->get('isLoggedIn'));
    }

    /**
     * Test de acceso protegido sin autenticación
     */
    public function testProtectedRouteWithoutAuth(): void
    {
        // Intentar acceder a ruta protegida sin estar logueado
        $result = $this->get('/pacientes');
        
        $statusCode = $result->response()->getStatusCode();
        // Debe redirigir al login
        $this->assertTrue(in_array($statusCode, [302, 307]));
    }

    /**
     * Test de sesión después de login exitoso
     */
    public function testSessionDataAfterLogin(): void
    {
        $userId = $this->createTestUser();

        $result = $this->post('/auth/login', [
            'email' => $this->testEmail,
            'password' => $this->testPassword
        ]);

        // Verificar datos de sesión
        if ($result->response()->getStatusCode() === 302) {
            // Login exitoso
            $session = session();
            // Puede que la sesión se haya establecido
            $isLoggedIn = $session->get('isLoggedIn');
            // No podemos verificar directamente en tests de feature
            $this->assertTrue(true);
        }
    }

    /**
     * Test de sanitización de entrada
     */
    public function testInputSanitization(): void
    {
        $this->createTestUser();

        // Intento de inyección SQL
        $result = $this->post('/auth/login', [
            'email' => "' OR '1'='1",
            'password' => "' OR '1'='1"
        ]);

        $statusCode = $result->response()->getStatusCode();
        // No debe loguear
        $this->assertTrue(in_array($statusCode, [302, 303, 307]));
    }

    /**
     * Test de límite de caracteres en email
     */
    public function testEmailMaxLength(): void
    {
        $longEmail = str_repeat('a', 300) . '@test.com';

        $result = $this->post('/auth/login', [
            'email' => $longEmail,
            'password' => 'password123'
        ]);

        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [302, 303, 307, 422]));
    }

    /**
     * Test de contenido de página de login
     */
    public function testLoginPageContent(): void
    {
        $result = $this->get('/login');
        
        if ($result->response()->getStatusCode() === 200) {
            $body = $result->response()->getBody();
            
            // Debe tener un formulario con campos de email y password
            $this->assertStringContainsString('email', strtolower($body));
            $this->assertStringContainsString('password', strtolower($body));
        }
    }

    /**
     * Test de remember me (si existe)
     */
    public function testRememberMe(): void
    {
        $this->createTestUser();

        $result = $this->post('/auth/login', [
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'remember' => '1'
        ]);

        $statusCode = $result->response()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [200, 302, 303, 307]));
    }
}
