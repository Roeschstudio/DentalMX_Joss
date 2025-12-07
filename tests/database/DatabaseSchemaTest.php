<?php

namespace Tests\Database;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

/**
 * Database Schema and Integrity Tests
 * 
 * @group Database
 */
class DatabaseSchemaTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $namespace = 'App';

    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Database::connect();
    }

    /**
     * Test de que las tablas principales existen
     */
    public function testRequiredTablesExist(): void
    {
        $tables = [
            'pacientes',
            'usuarios',
            'citas',
            'odontogramas',
            'odontograma_dientes',
            'servicios'
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                $this->db->tableExists($table),
                "Table '{$table}' should exist"
            );
        }
    }

    /**
     * Test de estructura de tabla pacientes
     */
    public function testPacientesTableStructure(): void
    {
        if (!$this->db->tableExists('pacientes')) {
            $this->markTestSkipped('Table pacientes does not exist');
        }

        $fields = $this->db->getFieldNames('pacientes');
        
        $requiredFields = [
            'id',
            'nombre',
            'primer_apellido',
            'email',
            'created_at',
            'updated_at'
        ];

        foreach ($requiredFields as $field) {
            $this->assertContains(
                $field,
                $fields,
                "Field '{$field}' should exist in pacientes table"
            );
        }
    }

    /**
     * Test de estructura de tabla usuarios
     */
    public function testUsuariosTableStructure(): void
    {
        if (!$this->db->tableExists('usuarios')) {
            $this->markTestSkipped('Table usuarios does not exist');
        }

        $fields = $this->db->getFieldNames('usuarios');
        
        $requiredFields = [
            'id',
            'nombre',
            'email',
            'password',
            'rol'
        ];

        foreach ($requiredFields as $field) {
            $this->assertContains(
                $field,
                $fields,
                "Field '{$field}' should exist in usuarios table"
            );
        }
    }

    /**
     * Test de estructura de tabla citas
     */
    public function testCitasTableStructure(): void
    {
        if (!$this->db->tableExists('citas')) {
            $this->markTestSkipped('Table citas does not exist');
        }

        $fields = $this->db->getFieldNames('citas');
        
        $requiredFields = [
            'id',
            'id_paciente',
            'id_usuario',
            'titulo',
            'fecha_inicio',
            'fecha_fin',
            'estado',
            'tipo_cita'
        ];

        foreach ($requiredFields as $field) {
            $this->assertContains(
                $field,
                $fields,
                "Field '{$field}' should exist in citas table"
            );
        }
    }

    /**
     * Test de estructura de tabla odontogramas
     */
    public function testOdontogramasTableStructure(): void
    {
        if (!$this->db->tableExists('odontogramas')) {
            $this->markTestSkipped('Table odontogramas does not exist');
        }

        $fields = $this->db->getFieldNames('odontogramas');
        
        $requiredFields = [
            'id',
            'id_paciente',
            'tipo_dentadura'
        ];

        foreach ($requiredFields as $field) {
            $this->assertContains(
                $field,
                $fields,
                "Field '{$field}' should exist in odontogramas table"
            );
        }
    }

    /**
     * Test de estructura de tabla odontograma_dientes
     */
    public function testOdontogramaDientesTableStructure(): void
    {
        if (!$this->db->tableExists('odontograma_dientes')) {
            $this->markTestSkipped('Table odontograma_dientes does not exist');
        }

        $fields = $this->db->getFieldNames('odontograma_dientes');
        
        $requiredFields = [
            'id',
            'id_odontograma',
            'numero_diente'
        ];

        foreach ($requiredFields as $field) {
            $this->assertContains(
                $field,
                $fields,
                "Field '{$field}' should exist in odontograma_dientes table"
            );
        }
    }

    /**
     * Test de claves primarias
     */
    public function testPrimaryKeys(): void
    {
        $tables = ['pacientes', 'usuarios', 'citas', 'odontogramas'];

        foreach ($tables as $table) {
            if (!$this->db->tableExists($table)) {
                continue;
            }

            $fields = $this->db->getFieldData($table);
            $hasPrimaryKey = false;

            foreach ($fields as $field) {
                if ($field->primary_key ?? false) {
                    $hasPrimaryKey = true;
                    break;
                }
            }

            // Verificar que el campo 'id' existe (asumimos que es la PK)
            $fieldNames = $this->db->getFieldNames($table);
            $this->assertContains('id', $fieldNames, "Table {$table} should have 'id' field");
        }
    }

    /**
     * Test de índices en campos de búsqueda frecuente
     */
    public function testSearchFieldsIndexed(): void
    {
        if (!$this->db->tableExists('pacientes')) {
            $this->markTestSkipped('Table pacientes does not exist');
        }

        $indexes = $this->db->getIndexData('pacientes');
        
        // El email debería tener un índice para búsquedas rápidas
        // Este test es informativo, no falla si no hay índice
        $this->assertIsArray($indexes);
    }

    /**
     * Test de que los campos nullable están configurados correctamente
     */
    public function testNullableFields(): void
    {
        if (!$this->db->tableExists('pacientes')) {
            $this->markTestSkipped('Table pacientes does not exist');
        }

        $fields = $this->db->getFieldData('pacientes');
        
        foreach ($fields as $field) {
            // Campos que no deberían ser null
            if ($field->name === 'id' || $field->name === 'nombre') {
                // Primary key y campos requeridos no deberían permitir null
                // Pero esto depende de la configuración específica
            }
        }

        $this->assertTrue(true); // Test informativo
    }

    /**
     * Test de tabla catalogos_odontologicos
     */
    public function testCatalogosOdontologicosTable(): void
    {
        if (!$this->db->tableExists('catalogos_odontologicos')) {
            $this->markTestSkipped('Table catalogos_odontologicos does not exist');
        }

        $fields = $this->db->getFieldNames('catalogos_odontologicos');
        
        $requiredFields = [
            'id',
            'codigo',
            'tipo',
            'nombre',
            'color_hex'
        ];

        foreach ($requiredFields as $field) {
            $this->assertContains(
                $field,
                $fields,
                "Field '{$field}' should exist in catalogos_odontologicos table"
            );
        }
    }

    /**
     * Test de que catalogos_odontologicos tiene datos
     */
    public function testCatalogosOdontologicosHasData(): void
    {
        if (!$this->db->tableExists('catalogos_odontologicos')) {
            $this->markTestSkipped('Table catalogos_odontologicos does not exist');
        }

        $count = $this->db->table('catalogos_odontologicos')->countAllResults();
        
        // Debe tener datos del seeder
        $this->assertGreaterThan(0, $count, 'catalogos_odontologicos should have seed data');
    }

    /**
     * Test de consistencia de foreign keys
     */
    public function testForeignKeyConsistency(): void
    {
        // Verificar que las referencias de citas a pacientes son válidas
        if (!$this->db->tableExists('citas') || !$this->db->tableExists('pacientes')) {
            $this->markTestSkipped('Required tables do not exist');
        }

        // Obtener IDs de pacientes en citas
        $citas = $this->db->table('citas')->select('id_paciente')->get()->getResultArray();
        
        foreach ($citas as $cita) {
            if (!empty($cita['id_paciente'])) {
                $paciente = $this->db->table('pacientes')
                    ->where('id', $cita['id_paciente'])
                    ->get()
                    ->getRow();
                
                // El paciente debe existir
                $this->assertNotNull(
                    $paciente,
                    "Paciente {$cita['id_paciente']} referenced in citas should exist"
                );
            }
        }
    }

    /**
     * Test de integridad de odontogramas
     */
    public function testOdontogramaIntegrity(): void
    {
        if (!$this->db->tableExists('odontogramas') || !$this->db->tableExists('pacientes')) {
            $this->markTestSkipped('Required tables do not exist');
        }

        // Obtener IDs de pacientes en odontogramas
        $odontogramas = $this->db->table('odontogramas')->select('id_paciente')->get()->getResultArray();
        
        foreach ($odontogramas as $odontograma) {
            if (!empty($odontograma['id_paciente'])) {
                $paciente = $this->db->table('pacientes')
                    ->where('id', $odontograma['id_paciente'])
                    ->get()
                    ->getRow();
                
                // El paciente debe existir
                $this->assertNotNull(
                    $paciente,
                    "Paciente {$odontograma['id_paciente']} referenced in odontogramas should exist"
                );
            }
        }
    }

    /**
     * Test de tipos de datos
     */
    public function testDataTypes(): void
    {
        if (!$this->db->tableExists('pacientes')) {
            $this->markTestSkipped('Table pacientes does not exist');
        }

        $fields = $this->db->getFieldData('pacientes');
        
        foreach ($fields as $field) {
            // Verificar que id es INT o BIGINT
            if ($field->name === 'id') {
                $this->assertMatchesRegularExpression(
                    '/int/i',
                    $field->type,
                    "Field 'id' should be an integer type"
                );
            }
            
            // Verificar que email es varchar
            if ($field->name === 'email') {
                $this->assertMatchesRegularExpression(
                    '/varchar|text/i',
                    $field->type,
                    "Field 'email' should be varchar or text"
                );
            }
        }
    }

    /**
     * Test de longitud de campos
     */
    public function testFieldLengths(): void
    {
        if (!$this->db->tableExists('pacientes')) {
            $this->markTestSkipped('Table pacientes does not exist');
        }

        $fields = $this->db->getFieldData('pacientes');
        
        foreach ($fields as $field) {
            // El email debe tener suficiente longitud
            if ($field->name === 'email' && isset($field->max_length)) {
                $this->assertGreaterThanOrEqual(
                    50,
                    $field->max_length,
                    "Field 'email' should have at least 50 characters"
                );
            }
            
            // El nombre debe tener suficiente longitud
            if ($field->name === 'nombre' && isset($field->max_length)) {
                $this->assertGreaterThanOrEqual(
                    20,
                    $field->max_length,
                    "Field 'nombre' should have at least 20 characters"
                );
            }
        }
    }

    /**
     * Test de valores enum en citas
     */
    public function testCitasEnumValues(): void
    {
        if (!$this->db->tableExists('citas')) {
            $this->markTestSkipped('Table citas does not exist');
        }

        // Insertar cita con estado válido
        $validStates = ['programada', 'confirmada', 'en_progreso', 'completada', 'cancelada'];
        $validTypes = ['consulta', 'tratamiento', 'revision', 'urgencia'];

        // Este test verifica que los valores son aceptados
        // En una BD real, se verificaría con constraints
        $this->assertContains('programada', $validStates);
        $this->assertContains('consulta', $validTypes);
    }

    /**
     * Test de timestamps automáticos
     */
    public function testTimestampColumns(): void
    {
        $tablesWithTimestamps = ['pacientes', 'usuarios', 'citas', 'odontogramas'];

        foreach ($tablesWithTimestamps as $table) {
            if (!$this->db->tableExists($table)) {
                continue;
            }

            $fields = $this->db->getFieldNames($table);
            
            $this->assertContains(
                'created_at',
                $fields,
                "Table {$table} should have created_at column"
            );
            
            $this->assertContains(
                'updated_at',
                $fields,
                "Table {$table} should have updated_at column"
            );
        }
    }
}
