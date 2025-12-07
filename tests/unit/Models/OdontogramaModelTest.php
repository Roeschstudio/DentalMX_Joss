<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\OdontogramaModel;
use App\Models\OdontogramaDienteModel;
use App\Models\PacientesModel;

/**
 * Test suite for OdontogramaModel
 * 
 * @group Models
 * @group Odontograma
 */
class OdontogramaModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = 'App';

    protected OdontogramaModel $model;
    protected PacientesModel $pacientesModel;
    protected int $testPatientId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new OdontogramaModel();
        $this->pacientesModel = new PacientesModel();
        
        // Crear paciente de prueba
        $this->testPatientId = $this->pacientesModel->insert([
            'nombre' => 'Test',
            'primer_apellido' => 'Odontograma',
            'email' => 'odontograma@test.com'
        ]);
    }

    /**
     * Test que el modelo se instancia correctamente
     */
    public function testModelInstantiation(): void
    {
        $this->assertInstanceOf(OdontogramaModel::class, $this->model);
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

        $this->assertEquals('odontogramas', $tableName);
    }

    /**
     * Test de campos permitidos
     */
    public function testAllowedFields(): void
    {
        $expectedFields = [
            'id_paciente',
            'tipo_dentadura',
            'observaciones_generales',
            'estado_general'
        ];

        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('allowedFields');
        $property->setAccessible(true);
        $allowedFields = $property->getValue($this->model);

        foreach ($expectedFields as $field) {
            $this->assertContains($field, $allowedFields);
        }
    }

    /**
     * Test de reglas de validación
     */
    public function testValidationRules(): void
    {
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('validationRules');
        $property->setAccessible(true);
        $rules = $property->getValue($this->model);

        $this->assertArrayHasKey('id_paciente', $rules);
        $this->assertArrayHasKey('tipo_dentadura', $rules);
        $this->assertStringContainsString('required', $rules['id_paciente']);
        $this->assertStringContainsString('in_list[permanente,decidua,mixta]', $rules['tipo_dentadura']);
    }

    /**
     * Test de estructura de dientes adultos
     */
    public function testDientesAdultosStructure(): void
    {
        $dientes = OdontogramaModel::$dientesAdultos;

        $this->assertArrayHasKey('superior', $dientes);
        $this->assertArrayHasKey('inferior', $dientes);
        $this->assertArrayHasKey('derecho', $dientes['superior']);
        $this->assertArrayHasKey('izquierdo', $dientes['superior']);
        
        // Verificar cuadrante 1 (18-11)
        $this->assertEquals([18, 17, 16, 15, 14, 13, 12, 11], $dientes['superior']['derecho']);
        
        // Verificar cuadrante 2 (21-28)
        $this->assertEquals([21, 22, 23, 24, 25, 26, 27, 28], $dientes['superior']['izquierdo']);
        
        // Verificar cuadrante 3 (31-38)
        $this->assertEquals([31, 32, 33, 34, 35, 36, 37, 38], $dientes['inferior']['izquierdo']);
        
        // Verificar cuadrante 4 (48-41)
        $this->assertEquals([48, 47, 46, 45, 44, 43, 42, 41], $dientes['inferior']['derecho']);
    }

    /**
     * Test de estructura de dientes infantiles
     */
    public function testDientesInfantilesStructure(): void
    {
        $dientes = OdontogramaModel::$dientesInfantiles;

        $this->assertArrayHasKey('superior', $dientes);
        $this->assertArrayHasKey('inferior', $dientes);
        
        // Cuadrante 5 (55-51)
        $this->assertEquals([55, 54, 53, 52, 51], $dientes['superior']['derecho']);
        
        // Cuadrante 6 (61-65)
        $this->assertEquals([61, 62, 63, 64, 65], $dientes['superior']['izquierdo']);
    }

    /**
     * Test de superficies dentales
     */
    public function testSuperficiesStructure(): void
    {
        $superficies = OdontogramaModel::$superficies;

        $expectedSuperficies = ['oclusal', 'vestibular', 'lingual', 'mesial', 'distal'];
        
        foreach ($expectedSuperficies as $superficie) {
            $this->assertArrayHasKey($superficie, $superficies);
        }
    }

    /**
     * Test de creación de odontograma
     */
    public function testCreateOdontograma(): void
    {
        $odontogramaId = $this->model->insert([
            'id_paciente' => $this->testPatientId,
            'tipo_dentadura' => 'permanente',
            'estado_general' => 'bueno'
        ]);

        $this->assertIsInt($odontogramaId);
        $this->assertGreaterThan(0, $odontogramaId);

        $odontograma = $this->model->find($odontogramaId);
        $this->assertNotNull($odontograma);
        $this->assertEquals($this->testPatientId, $odontograma['id_paciente']);
        $this->assertEquals('permanente', $odontograma['tipo_dentadura']);
    }

    /**
     * Test de getOdontogramaPaciente
     */
    public function testGetOdontogramaPaciente(): void
    {
        // Sin odontograma
        $result = $this->model->getOdontogramaPaciente($this->testPatientId);
        $this->assertNull($result);

        // Crear odontograma
        $this->model->insert([
            'id_paciente' => $this->testPatientId,
            'tipo_dentadura' => 'permanente',
            'estado_general' => 'bueno'
        ]);

        // Con odontograma
        $result = $this->model->getOdontogramaPaciente($this->testPatientId);
        $this->assertIsArray($result);
        $this->assertEquals($this->testPatientId, $result['id_paciente']);
    }

    /**
     * Test de getOrCreateOdontograma - crear nuevo
     */
    public function testGetOrCreateOdontogramaCreatesNew(): void
    {
        $result = $this->model->getOrCreateOdontograma($this->testPatientId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals($this->testPatientId, $result['id_paciente']);
        $this->assertEquals('permanente', $result['tipo_dentadura']);
        $this->assertEquals('bueno', $result['estado_general']);
    }

    /**
     * Test de getOrCreateOdontograma - retornar existente
     */
    public function testGetOrCreateOdontogramaReturnsExisting(): void
    {
        // Crear odontograma inicial
        $created = $this->model->getOrCreateOdontograma($this->testPatientId, 'permanente');
        $originalId = $created['id'];

        // Intentar obtener/crear de nuevo
        $result = $this->model->getOrCreateOdontograma($this->testPatientId);

        $this->assertEquals($originalId, $result['id']);
    }

    /**
     * Test de tipos de dentadura válidos
     */
    public function testTiposDentaduraValidos(): void
    {
        $tiposValidos = ['permanente', 'decidua', 'mixta'];

        foreach ($tiposValidos as $tipo) {
            $id = $this->model->insert([
                'id_paciente' => $this->testPatientId + rand(1000, 9999), // Nuevo paciente ficticio
                'tipo_dentadura' => $tipo,
                'estado_general' => 'bueno'
            ]);

            if ($id !== false) {
                $odontograma = $this->model->find($id);
                $this->assertEquals($tipo, $odontograma['tipo_dentadura']);
            }
        }
    }

    /**
     * Test de estados generales válidos
     */
    public function testEstadosGeneralesValidos(): void
    {
        $estadosValidos = ['bueno', 'regular', 'malo'];

        foreach ($estadosValidos as $estado) {
            // Usar un ID de paciente diferente para cada prueba
            $patientId = $this->testPatientId + rand(10000, 99999);
            
            $id = $this->model->insert([
                'id_paciente' => $patientId,
                'tipo_dentadura' => 'permanente',
                'estado_general' => $estado
            ]);

            if ($id !== false) {
                $odontograma = $this->model->find($id);
                $this->assertEquals($estado, $odontograma['estado_general']);
            }
        }
    }

    /**
     * Test de soft delete
     */
    public function testSoftDelete(): void
    {
        $odontogramaId = $this->model->insert([
            'id_paciente' => $this->testPatientId,
            'tipo_dentadura' => 'permanente',
            'estado_general' => 'bueno'
        ]);

        // Eliminar
        $this->model->delete($odontogramaId);

        // No debe encontrarse con find normal
        $result = $this->model->find($odontogramaId);
        $this->assertNull($result);

        // Pero debe encontrarse con withDeleted
        $resultWithDeleted = $this->model->withDeleted()->find($odontogramaId);
        $this->assertNotNull($resultWithDeleted);
        $this->assertNotNull($resultWithDeleted['deleted_at']);
    }

    /**
     * Test de timestamps
     */
    public function testTimestamps(): void
    {
        $odontogramaId = $this->model->insert([
            'id_paciente' => $this->testPatientId,
            'tipo_dentadura' => 'permanente'
        ]);

        $odontograma = $this->model->find($odontogramaId);

        $this->assertArrayHasKey('created_at', $odontograma);
        $this->assertArrayHasKey('updated_at', $odontograma);
        $this->assertNotNull($odontograma['created_at']);
    }

    /**
     * Test de actualización de observaciones
     */
    public function testUpdateObservaciones(): void
    {
        $odontogramaId = $this->model->insert([
            'id_paciente' => $this->testPatientId,
            'tipo_dentadura' => 'permanente'
        ]);

        $observaciones = 'Paciente con buena higiene dental. Recomendado limpieza cada 6 meses.';
        
        $this->model->update($odontogramaId, [
            'observaciones_generales' => $observaciones
        ]);

        $odontograma = $this->model->find($odontogramaId);
        $this->assertEquals($observaciones, $odontograma['observaciones_generales']);
    }

    /**
     * Test del número total de dientes adultos
     */
    public function testTotalDientesAdultos(): void
    {
        $dientes = OdontogramaModel::$dientesAdultos;
        $total = 0;

        foreach ($dientes as $arcada) {
            foreach ($arcada as $lado) {
                $total += count($lado);
            }
        }

        $this->assertEquals(32, $total); // Adultos tienen 32 dientes
    }

    /**
     * Test del número total de dientes infantiles
     */
    public function testTotalDientesInfantiles(): void
    {
        $dientes = OdontogramaModel::$dientesInfantiles;
        $total = 0;

        foreach ($dientes as $arcada) {
            foreach ($arcada as $lado) {
                $total += count($lado);
            }
        }

        $this->assertEquals(20, $total); // Niños tienen 20 dientes
    }
}
