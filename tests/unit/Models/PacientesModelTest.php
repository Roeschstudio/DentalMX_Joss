<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\PacientesModel;

/**
 * Test suite for PacientesModel
 * 
 * @group Models
 * @group Pacientes
 */
class PacientesModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = 'App';

    protected PacientesModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new PacientesModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test que el modelo se instancia correctamente
     */
    public function testModelInstantiation(): void
    {
        $this->assertInstanceOf(PacientesModel::class, $this->model);
    }

    /**
     * Test de los campos permitidos del modelo
     */
    public function testAllowedFields(): void
    {
        $expectedFields = [
            'nombre',
            'primer_apellido',
            'segundo_apellido',
            'fecha_nacimiento',
            'nacionalidad',
            'domicilio',
            'telefono',
            'celular',
            'email'
        ];

        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('allowedFields');
        $property->setAccessible(true);
        $allowedFields = $property->getValue($this->model);

        foreach ($expectedFields as $field) {
            $this->assertContains($field, $allowedFields, "Campo '{$field}' debería estar en allowedFields");
        }
    }

    /**
     * Test de nombre de tabla
     */
    public function testTableName(): void
    {
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        $tableName = $property->getValue($this->model);

        $this->assertEquals('pacientes', $tableName);
    }

    /**
     * Test de inserción de paciente válido
     */
    public function testInsertValidPatient(): void
    {
        $patientData = [
            'nombre' => 'Juan',
            'primer_apellido' => 'Pérez',
            'segundo_apellido' => 'García',
            'fecha_nacimiento' => '1990-05-15',
            'nacionalidad' => 'Mexicana',
            'domicilio' => 'Calle Principal #123',
            'telefono' => '5551234567',
            'celular' => '5559876543',
            'email' => 'juan.perez@example.com'
        ];

        $result = $this->model->insert($patientData);
        
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
        
        // Verificar que se guardó
        $savedPatient = $this->model->find($result);
        $this->assertNotNull($savedPatient);
        $this->assertEquals('Juan', $savedPatient['nombre']);
        $this->assertEquals('juan.perez@example.com', $savedPatient['email']);
    }

    /**
     * Test del método saveData
     */
    public function testSaveDataMethod(): void
    {
        $patientData = [
            'nombre' => 'María',
            'primer_apellido' => 'López',
            'segundo_apellido' => 'Hernández',
            'fecha_nacimiento' => '1985-08-20',
            'email' => 'maria.lopez@example.com'
        ];

        $result = $this->model->saveData($patientData);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Paciente guardado exitosamente', $result['message']);
    }

    /**
     * Test de obtener todos los pacientes
     */
    public function testGetAllPacients(): void
    {
        // Insertar algunos pacientes de prueba
        $patients = [
            ['nombre' => 'Paciente 1', 'primer_apellido' => 'Apellido1', 'email' => 'p1@test.com'],
            ['nombre' => 'Paciente 2', 'primer_apellido' => 'Apellido2', 'email' => 'p2@test.com'],
            ['nombre' => 'Paciente 3', 'primer_apellido' => 'Apellido3', 'email' => 'p3@test.com'],
        ];

        foreach ($patients as $patient) {
            $this->model->insert($patient);
        }

        $allPatients = $this->model->getAllPacients();
        
        $this->assertIsArray($allPatients);
        $this->assertCount(3, $allPatients);
        
        // Verificar orden descendente
        $this->assertEquals('Paciente 3', $allPatients[0]['nombre']);
    }

    /**
     * Test de eliminación de paciente
     */
    public function testDeleteUser(): void
    {
        // Insertar un paciente
        $patientId = $this->model->insert([
            'nombre' => 'Para Eliminar',
            'primer_apellido' => 'Test',
            'email' => 'delete@test.com'
        ]);

        // Verificar que existe
        $this->assertNotNull($this->model->find($patientId));

        // Eliminar
        $result = $this->model->deleteUser($patientId);
        
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Paciente eliminado exitosamente', $result['message']);

        // Verificar que ya no existe
        $this->assertNull($this->model->find($patientId));
    }

    /**
     * Test de paginación
     */
    public function testGetPaginated(): void
    {
        // Insertar varios pacientes
        for ($i = 1; $i <= 15; $i++) {
            $this->model->insert([
                'nombre' => "Paciente $i",
                'primer_apellido' => 'Test',
                'email' => "paciente$i@test.com"
            ]);
        }

        $paginated = $this->model->getPaginated(10);
        
        $this->assertIsArray($paginated);
        $this->assertCount(10, $paginated);

        $pager = $this->model->getPaginationData();
        $this->assertNotNull($pager);
    }

    /**
     * Test de actualización de paciente
     */
    public function testUpdatePatient(): void
    {
        // Insertar paciente
        $patientId = $this->model->insert([
            'nombre' => 'Original',
            'primer_apellido' => 'Apellido',
            'email' => 'original@test.com'
        ]);

        // Actualizar
        $updateResult = $this->model->update($patientId, [
            'nombre' => 'Actualizado',
            'email' => 'actualizado@test.com'
        ]);

        $this->assertTrue($updateResult);

        // Verificar cambios
        $updated = $this->model->find($patientId);
        $this->assertEquals('Actualizado', $updated['nombre']);
        $this->assertEquals('actualizado@test.com', $updated['email']);
    }

    /**
     * Test de timestamps automáticos
     */
    public function testTimestamps(): void
    {
        $patientId = $this->model->insert([
            'nombre' => 'Timestamp Test',
            'primer_apellido' => 'Test',
            'email' => 'timestamp@test.com'
        ]);

        $patient = $this->model->find($patientId);
        
        $this->assertArrayHasKey('created_at', $patient);
        $this->assertArrayHasKey('updated_at', $patient);
        $this->assertNotNull($patient['created_at']);
    }

    /**
     * Test de búsqueda por ID
     */
    public function testFindById(): void
    {
        $patientId = $this->model->insert([
            'nombre' => 'Buscar',
            'primer_apellido' => 'PorId',
            'email' => 'buscar@test.com'
        ]);

        $found = $this->model->find($patientId);
        
        $this->assertIsArray($found);
        $this->assertEquals($patientId, $found['id']);
        $this->assertEquals('Buscar', $found['nombre']);
    }

    /**
     * Test de búsqueda de paciente inexistente
     */
    public function testFindNonExistentPatient(): void
    {
        $found = $this->model->find(99999);
        $this->assertNull($found);
    }

    /**
     * Test que los métodos placeholder retornan arrays vacíos
     */
    public function testPlaceholderMethods(): void
    {
        $historial = $this->model->getHistorial(1);
        $this->assertIsArray($historial);
        $this->assertEmpty($historial);

        $citas = $this->model->getCitas(1);
        $this->assertIsArray($citas);
        $this->assertEmpty($citas);

        $recetas = $this->model->getRecetas(1);
        $this->assertIsArray($recetas);
        $this->assertEmpty($recetas);
    }
}
