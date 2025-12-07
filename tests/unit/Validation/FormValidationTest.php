<?php

namespace Tests\Unit\Validation;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Test suite for Form Validations
 * Tests validation patterns and logic used in the application
 * 
 * @group Validation
 */
class FormValidationTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset services to avoid state pollution between tests
        \Config\Services::reset(true);
    }

    // ==========================================
    // PACIENTE VALIDATION TESTS
    // ==========================================

    /**
     * Test de validación de nombre de paciente
     */
    public function testPatientNameValidation(): void
    {
        $validation = \Config\Services::validation();
        $validation->setRules(['nombre' => 'required|min_length[2]|max_length[100]']);

        // Valid
        $this->assertTrue($validation->run(['nombre' => 'Juan']));

        // Empty - Invalid
        $validation->reset();
        $validation->setRules(['nombre' => 'required|min_length[2]|max_length[100]']);
        $this->assertFalse($validation->run(['nombre' => '']));

        // Too short
        $validation->reset();
        $validation->setRules(['nombre' => 'required|min_length[2]|max_length[100]']);
        $this->assertFalse($validation->run(['nombre' => 'J']));

        // Too long
        $validation->reset();
        $validation->setRules(['nombre' => 'required|min_length[2]|max_length[100]']);
        $longName = str_repeat('a', 101);
        $this->assertFalse($validation->run(['nombre' => $longName]));
    }

    /**
     * Test de validación de estado de cita
     */
    public function testAppointmentStatusValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['estado' => 'required|in_list[programada,confirmada,en_progreso,completada,cancelada]'];

        // Valid states
        $validStates = ['programada', 'confirmada', 'en_progreso', 'completada', 'cancelada'];

        foreach ($validStates as $estado) {
            $validation->reset();
            $validation->setRules($rules);
            $this->assertTrue(
                $validation->run(['estado' => $estado]),
                "Estado '{$estado}' should be valid"
            );
        }

        // Invalid state
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['estado' => 'pendiente']));
    }

    /**
     * Test de validación de tipo de cita
     */
    public function testAppointmentTypeValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['tipo_cita' => 'required|in_list[consulta,tratamiento,revision,urgencia]'];

        // Valid types
        $validTypes = ['consulta', 'tratamiento', 'revision', 'urgencia'];

        foreach ($validTypes as $tipo) {
            $validation->reset();
            $validation->setRules($rules);
            $this->assertTrue(
                $validation->run(['tipo_cita' => $tipo]),
                "Tipo '{$tipo}' should be valid"
            );
        }

        // Invalid type
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['tipo_cita' => 'otro']));
    }

    /**
     * Test de validación de tipo de dentadura
     */
    public function testDentitionTypeValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['tipo_dentadura' => 'required|in_list[permanente,decidua,mixta]'];

        // Valid types
        $validTypes = ['permanente', 'decidua', 'mixta'];

        foreach ($validTypes as $tipo) {
            $validation->reset();
            $validation->setRules($rules);
            $this->assertTrue(
                $validation->run(['tipo_dentadura' => $tipo]),
                "Tipo '{$tipo}' should be valid"
            );
        }

        // Invalid type
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['tipo_dentadura' => 'otro']));
    }

    /**
     * Test de validación de estado general dental
     */
    public function testDentalGeneralStateValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['estado_general' => 'permit_empty|in_list[bueno,regular,malo]'];

        // Valid states
        $validStates = ['bueno', 'regular', 'malo'];

        foreach ($validStates as $estado) {
            $validation->reset();
            $validation->setRules($rules);
            $this->assertTrue(
                $validation->run(['estado_general' => $estado]),
                "Estado '{$estado}' should be valid"
            );
        }

        // Empty is allowed with permit_empty
        $validation->reset();
        $validation->setRules($rules);
        $this->assertTrue($validation->run(['estado_general' => '']));
    }

    /**
     * Test de validación de superficie dental
     */
    public function testToothSurfaceValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['superficie' => 'required|in_list[oclusal,vestibular,lingual,mesial,distal]'];

        // Valid surfaces
        $validSurfaces = ['oclusal', 'vestibular', 'lingual', 'mesial', 'distal'];

        foreach ($validSurfaces as $superficie) {
            $validation->reset();
            $validation->setRules($rules);
            $this->assertTrue(
                $validation->run(['superficie' => $superficie]),
                "Superficie '{$superficie}' should be valid"
            );
        }

        // Invalid surface
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['superficie' => 'lateral']));
    }

    /**
     * Test de validación de número de diente adulto
     */
    public function testAdultToothNumberValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['numero_diente' => 'required|integer|greater_than[10]|less_than[49]'];

        // Valid teeth (11-48)
        $validTeeth = [11, 18, 21, 28, 31, 38, 41, 48];

        foreach ($validTeeth as $tooth) {
            $validation->reset();
            $validation->setRules($rules);
            $this->assertTrue(
                $validation->run(['numero_diente' => $tooth]),
                "Tooth number {$tooth} should be valid"
            );
        }

        // Invalid - out of range
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['numero_diente' => 5]));

        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['numero_diente' => 99]));
    }

    /**
     * Test de validación de ID de paciente
     */
    public function testPatientIdValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['id_paciente' => 'required|integer|greater_than[0]'];
        $validation->setRules($rules);

        // Valid ID
        $this->assertTrue($validation->run(['id_paciente' => 1]));

        $validation->reset();
        $validation->setRules($rules);
        $this->assertTrue($validation->run(['id_paciente' => 999]));

        // Invalid IDs
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['id_paciente' => 0]));

        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['id_paciente' => -1]));
    }

    /**
     * Test de validación de campos requeridos
     */
    public function testRequiredFieldValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['campo' => 'required'];
        $validation->setRules($rules);

        // Valid - has value
        $this->assertTrue($validation->run(['campo' => 'valor']));

        // Invalid - empty
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['campo' => '']));
    }

    /**
     * Test de validación de longitud mínima
     */
    public function testMinLengthValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['texto' => 'required|min_length[5]'];
        $validation->setRules($rules);

        // Valid - long enough
        $this->assertTrue($validation->run(['texto' => 'largo suficiente']));

        // Invalid - too short
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['texto' => 'abc']));
    }

    /**
     * Test de validación de longitud máxima
     */
    public function testMaxLengthValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['texto' => 'required|max_length[10]'];
        $validation->setRules($rules);

        // Valid - short enough
        $this->assertTrue($validation->run(['texto' => 'corto']));

        // Invalid - too long
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['texto' => 'este texto es demasiado largo']));
    }

    /**
     * Test de validación de integer
     */
    public function testIntegerValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['numero' => 'required|integer'];
        $validation->setRules($rules);

        // Valid integer
        $this->assertTrue($validation->run(['numero' => 123]));

        $validation->reset();
        $validation->setRules($rules);
        $this->assertTrue($validation->run(['numero' => '456']));

        // Invalid - not an integer
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['numero' => 'abc']));
    }

    /**
     * Test de validación greater_than
     */
    public function testGreaterThanValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['numero' => 'required|integer|greater_than[10]'];
        $validation->setRules($rules);

        // Valid - greater than 10
        $this->assertTrue($validation->run(['numero' => 15]));

        // Invalid - not greater
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['numero' => 5]));

        // Edge case - equal (not greater)
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['numero' => 10]));
    }

    /**
     * Test de validación in_list
     */
    public function testInListValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['opcion' => 'required|in_list[a,b,c]'];
        $validation->setRules($rules);

        // Valid - in list
        $this->assertTrue($validation->run(['opcion' => 'a']));

        $validation->reset();
        $validation->setRules($rules);
        $this->assertTrue($validation->run(['opcion' => 'b']));

        // Invalid - not in list
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['opcion' => 'd']));
    }

    /**
     * Test de validación permit_empty
     */
    public function testPermitEmptyValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['campo' => 'permit_empty|min_length[5]'];
        $validation->setRules($rules);

        // Valid - empty allowed
        $this->assertTrue($validation->run(['campo' => '']));

        // Valid - has valid value
        $validation->reset();
        $validation->setRules($rules);
        $this->assertTrue($validation->run(['campo' => 'largo']));

        // Invalid - has value but too short
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run(['campo' => 'ab']));
    }

    /**
     * Test de múltiples campos
     */
    public function testMultipleFieldsValidation(): void
    {
        $validation = \Config\Services::validation();
        $rules = [
            'nombre' => 'required|min_length[2]',
            'estado' => 'required|in_list[activo,inactivo]'
        ];
        $validation->setRules($rules);

        // All valid
        $this->assertTrue($validation->run([
            'nombre' => 'Juan',
            'estado' => 'activo'
        ]));

        // One invalid
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run([
            'nombre' => 'J', // too short
            'estado' => 'activo'
        ]));

        // Both invalid
        $validation->reset();
        $validation->setRules($rules);
        $this->assertFalse($validation->run([
            'nombre' => '',
            'estado' => 'desconocido'
        ]));
    }

    /**
     * Test de obtener errores de validación
     */
    public function testGetValidationErrors(): void
    {
        $validation = \Config\Services::validation();
        $rules = ['nombre' => 'required'];
        $validation->setRules($rules);

        $validation->run(['nombre' => '']);
        
        $errors = $validation->getErrors();
        
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('nombre', $errors);
    }
}
