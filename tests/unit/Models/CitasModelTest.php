<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\CitasModel;
use App\Models\PacientesModel;
use App\Models\UsuariosModel;

/**
 * Test suite for CitasModel
 * 
 * @group Models
 * @group Citas
 */
class CitasModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = 'App';

    protected CitasModel $model;
    protected PacientesModel $pacientesModel;
    protected int $testPatientId;
    protected int $testUserId = 1; // Asumimos que existe un usuario con ID 1

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new CitasModel();
        $this->pacientesModel = new PacientesModel();
        
        // Crear paciente de prueba
        $this->testPatientId = $this->pacientesModel->insert([
            'nombre' => 'Test',
            'primer_apellido' => 'Citas',
            'email' => 'citas@test.com'
        ]);
    }

    /**
     * Test que el modelo se instancia correctamente
     */
    public function testModelInstantiation(): void
    {
        $this->assertInstanceOf(CitasModel::class, $this->model);
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

        $this->assertEquals('citas', $tableName);
    }

    /**
     * Test de campos permitidos
     */
    public function testAllowedFields(): void
    {
        $expectedFields = [
            'id_paciente', 'id_usuario', 'id_servicio', 'titulo', 'descripcion',
            'fecha_inicio', 'fecha_fin', 'estado', 'tipo_cita', 'color',
            'notas', 'recordatorio_enviado'
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
     * Test de reglas de validación
     */
    public function testValidationRulesExist(): void
    {
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('validationRules');
        $property->setAccessible(true);
        $rules = $property->getValue($this->model);

        $requiredRules = ['id_paciente', 'id_usuario', 'titulo', 'fecha_inicio', 'fecha_fin', 'estado', 'tipo_cita'];
        
        foreach ($requiredRules as $rule) {
            $this->assertArrayHasKey($rule, $rules, "Regla de validación '{$rule}' debería existir");
        }
    }

    /**
     * Test de estados válidos
     */
    public function testEstadosValidos(): void
    {
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('validationRules');
        $property->setAccessible(true);
        $rules = $property->getValue($this->model);

        $estadoRule = $rules['estado'];
        
        $this->assertStringContainsString('programada', $estadoRule);
        $this->assertStringContainsString('confirmada', $estadoRule);
        $this->assertStringContainsString('en_progreso', $estadoRule);
        $this->assertStringContainsString('completada', $estadoRule);
        $this->assertStringContainsString('cancelada', $estadoRule);
    }

    /**
     * Test de tipos de cita válidos
     */
    public function testTiposCitaValidos(): void
    {
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('validationRules');
        $property->setAccessible(true);
        $rules = $property->getValue($this->model);

        $tipoRule = $rules['tipo_cita'];
        
        $this->assertStringContainsString('consulta', $tipoRule);
        $this->assertStringContainsString('tratamiento', $tipoRule);
        $this->assertStringContainsString('revision', $tipoRule);
        $this->assertStringContainsString('urgencia', $tipoRule);
    }

    /**
     * Test de creación de cita
     */
    public function testCreateCita(): void
    {
        $fechaInicio = date('Y-m-d H:i:s', strtotime('+1 day 10:00'));
        $fechaFin = date('Y-m-d H:i:s', strtotime('+1 day 11:00'));

        $citaData = [
            'id_paciente' => $this->testPatientId,
            'id_usuario' => $this->testUserId,
            'titulo' => 'Consulta General',
            'descripcion' => 'Primera consulta del paciente',
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => 'programada',
            'tipo_cita' => 'consulta',
            'color' => '#5ccdde'
        ];

        $citaId = $this->model->insert($citaData);

        if ($citaId !== false) {
            $this->assertIsInt($citaId);
            $this->assertGreaterThan(0, $citaId);

            $cita = $this->model->find($citaId);
            $this->assertNotNull($cita);
            $this->assertEquals('Consulta General', $cita['titulo']);
            $this->assertEquals('programada', $cita['estado']);
        }
    }

    /**
     * Test de soft delete
     */
    public function testSoftDelete(): void
    {
        $fechaInicio = date('Y-m-d H:i:s', strtotime('+2 day 10:00'));
        $fechaFin = date('Y-m-d H:i:s', strtotime('+2 day 11:00'));

        $citaId = $this->model->insert([
            'id_paciente' => $this->testPatientId,
            'id_usuario' => $this->testUserId,
            'titulo' => 'Para Eliminar',
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => 'programada',
            'tipo_cita' => 'consulta'
        ]);

        if ($citaId !== false) {
            // Eliminar
            $this->model->delete($citaId);

            // No debe encontrarse
            $result = $this->model->find($citaId);
            $this->assertNull($result);

            // Pero debe encontrarse con withDeleted
            $resultWithDeleted = $this->model->withDeleted()->find($citaId);
            $this->assertNotNull($resultWithDeleted);
            $this->assertNotNull($resultWithDeleted['deleted_at']);
        }
    }

    /**
     * Test de actualización de estado
     */
    public function testUpdateEstado(): void
    {
        $fechaInicio = date('Y-m-d H:i:s', strtotime('+3 day 10:00'));
        $fechaFin = date('Y-m-d H:i:s', strtotime('+3 day 11:00'));

        $citaId = $this->model->insert([
            'id_paciente' => $this->testPatientId,
            'id_usuario' => $this->testUserId,
            'titulo' => 'Cambio Estado',
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => 'programada',
            'tipo_cita' => 'consulta'
        ]);

        if ($citaId !== false) {
            // Actualizar a confirmada
            $this->model->update($citaId, ['estado' => 'confirmada']);
            $cita = $this->model->find($citaId);
            $this->assertEquals('confirmada', $cita['estado']);

            // Actualizar a completada
            $this->model->update($citaId, ['estado' => 'completada']);
            $cita = $this->model->find($citaId);
            $this->assertEquals('completada', $cita['estado']);
        }
    }

    /**
     * Test de timestamps
     */
    public function testTimestamps(): void
    {
        $fechaInicio = date('Y-m-d H:i:s', strtotime('+4 day 10:00'));
        $fechaFin = date('Y-m-d H:i:s', strtotime('+4 day 11:00'));

        $citaId = $this->model->insert([
            'id_paciente' => $this->testPatientId,
            'id_usuario' => $this->testUserId,
            'titulo' => 'Test Timestamps',
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => 'programada',
            'tipo_cita' => 'consulta'
        ]);

        if ($citaId !== false) {
            $cita = $this->model->find($citaId);
            $this->assertArrayHasKey('created_at', $cita);
            $this->assertArrayHasKey('updated_at', $cita);
            $this->assertNotNull($cita['created_at']);
        }
    }

    /**
     * Test de colores por tipo
     */
    public function testColoresPorTipo(): void
    {
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('coloresTipo');
        $property->setAccessible(true);
        $colores = $property->getValue($this->model);

        $this->assertArrayHasKey('consulta', $colores);
        $this->assertArrayHasKey('tratamiento', $colores);
        $this->assertArrayHasKey('revision', $colores);
        $this->assertArrayHasKey('urgencia', $colores);

        // Verificar formato de color hex
        foreach ($colores as $color) {
            $this->assertMatchesRegularExpression('/^#[0-9a-fA-F]{6}$/', $color);
        }
    }

    /**
     * Test de colores por estado
     */
    public function testColoresPorEstado(): void
    {
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('coloresEstado');
        $property->setAccessible(true);
        $colores = $property->getValue($this->model);

        $this->assertArrayHasKey('programada', $colores);
        $this->assertArrayHasKey('confirmada', $colores);
        $this->assertArrayHasKey('en_progreso', $colores);
        $this->assertArrayHasKey('completada', $colores);
        $this->assertArrayHasKey('cancelada', $colores);

        // Verificar formato de color hex
        foreach ($colores as $color) {
            $this->assertMatchesRegularExpression('/^#[0-9a-fA-F]{6}$/', $color);
        }
    }
}
