# DentalMX Test Suite

Este directorio contiene la suite completa de tests para la aplicación DentalMX.

## Estructura de Tests

```
tests/
├── unit/                          # Tests unitarios
│   ├── Models/                    # Tests de modelos
│   │   ├── PacientesModelTest.php
│   │   ├── OdontogramaModelTest.php
│   │   └── CitasModelTest.php
│   └── Validation/                # Tests de validación
│       └── FormValidationTest.php
├── Feature/                       # Tests de características/integración
│   ├── Controllers/               # Tests de controladores
│   │   ├── PatientControllerTest.php
│   │   ├── OdontogramaControllerTest.php
│   │   └── AuthControllerTest.php
│   └── API/                       # Tests de endpoints API
│       └── OdontogramaApiTest.php
├── database/                      # Tests de base de datos
│   └── DatabaseSchemaTest.php
├── session/                       # Tests de sesión
└── _support/                      # Helpers y factories
    └── Database/
        └── TestDataFactory.php
```

## Ejecutar Tests

### Todos los tests
```bash
php vendor/bin/phpunit
```

### Tests específicos por grupo
```bash
# Solo modelos
php vendor/bin/phpunit --group Models

# Solo controladores
php vendor/bin/phpunit --group Controllers

# Solo API
php vendor/bin/phpunit --group API

# Solo validación
php vendor/bin/phpunit --group Validation

# Solo base de datos
php vendor/bin/phpunit --group Database

# Solo pacientes
php vendor/bin/phpunit --group Pacientes

# Solo odontograma
php vendor/bin/phpunit --group Odontograma

# Solo autenticación
php vendor/bin/phpunit --group Auth
```

### Test específico
```bash
php vendor/bin/phpunit tests/unit/Models/PacientesModelTest.php
```

### Con cobertura de código
```bash
php vendor/bin/phpunit --coverage-html build/coverage
```

## Grupos de Tests

| Grupo | Descripción |
|-------|-------------|
| `Models` | Tests de modelos (PacientesModel, OdontogramaModel, etc.) |
| `Controllers` | Tests de controladores HTTP |
| `Pacientes` | Tests relacionados con pacientes |
| `Odontograma` | Tests del sistema de odontograma |
| `Citas` | Tests del sistema de citas |
| `Auth` | Tests de autenticación y autorización |
| `API` | Tests de endpoints API (JSON responses) |
| `Validation` | Tests de validación de formularios |
| `Database` | Tests de estructura de base de datos |

## Configuración de Base de Datos de Tests

Para usar una base de datos separada para tests, edita `phpunit.xml.dist`:

```xml
<env name="database.tests.hostname" value="localhost"/>
<env name="database.tests.database" value="dental_mx_tests"/>
<env name="database.tests.username" value="tests_user"/>
<env name="database.tests.password" value="tests_password"/>
<env name="database.tests.DBDriver" value="MySQLi"/>
<env name="database.tests.DBPrefix" value="tests_"/>
```

## Crear Datos de Prueba

Usa `TestDataFactory` para crear datos de prueba:

```php
use Tests\Support\Database\TestDataFactory;

// Crear un paciente
$patient = TestDataFactory::createPatient();

// Crear múltiples pacientes
$patients = TestDataFactory::createPatients(10);

// Crear usuario
$user = TestDataFactory::createUser();

// Crear cita
$appointment = TestDataFactory::createAppointment($patientId, $userId);

// Crear un conjunto completo de datos
$data = TestDataFactory::createFullTestData();
```

## Convenciones

1. **Nombres de tests**: `test<LoQueSeTestea>()`
2. **Grupos**: Usar `@group` en la clase para categorizar
3. **Datos de prueba**: Usar factories en `_support/Database/`
4. **Assertions**: Usar assertions de PHPUnit estándar
5. **Traits**: Usar `DatabaseTestTrait` para tests que modifican la BD

## Tests Disponibles

### Unit Tests

#### PacientesModelTest (12 tests)
- ✅ testModelInstantiation
- ✅ testAllowedFields
- ✅ testTableName
- ✅ testInsertValidPatient
- ✅ testSaveDataMethod
- ✅ testGetAllPacients
- ✅ testDeleteUser
- ✅ testGetPaginated
- ✅ testUpdatePatient
- ✅ testTimestamps
- ✅ testFindById
- ✅ testFindNonExistentPatient

#### OdontogramaModelTest (15 tests)
- ✅ testModelInstantiation
- ✅ testTableName
- ✅ testAllowedFields
- ✅ testValidationRules
- ✅ testDientesAdultosStructure
- ✅ testDientesInfantilesStructure
- ✅ testSuperficiesStructure
- ✅ testCreateOdontograma
- ✅ testGetOdontogramaPaciente
- ✅ testGetOrCreateOdontograma
- ✅ testTiposDentaduraValidos
- ✅ testEstadosGeneralesValidos
- ✅ testSoftDelete
- ✅ testTotalDientesAdultos (32 dientes)
- ✅ testTotalDientesInfantiles (20 dientes)

#### CitasModelTest (10 tests)
- ✅ testModelInstantiation
- ✅ testTableName
- ✅ testAllowedFields
- ✅ testValidationRulesExist
- ✅ testEstadosValidos
- ✅ testTiposCitaValidos
- ✅ testCreateCita
- ✅ testSoftDelete
- ✅ testColoresPorTipo
- ✅ testColoresPorEstado

#### FormValidationTest (15 tests)
- ✅ testPatientNameValidation
- ✅ testEmailValidation
- ✅ testPhoneValidation
- ✅ testBirthDateValidation
- ✅ testAppointmentTitleValidation
- ✅ testAppointmentStatusValidation
- ✅ testAppointmentTypeValidation
- ✅ testColorHexValidation
- ✅ testDentitionTypeValidation
- ✅ testDentalGeneralStateValidation
- ✅ testToothSurfaceValidation
- ✅ testAdultToothNumberValidation
- ✅ testPatientIdValidation
- ✅ testLoginValidation
- ✅ testPasswordStrengthValidation

### Feature Tests

#### PatientControllerTest (12 tests)
- ✅ testIndexPageLoads
- ✅ testCreatePageLoads
- ✅ testCreateFormHasRequiredFields
- ✅ testStorePatientWithValidData
- ✅ testStorePatientWithInvalidData
- ✅ testShowPatient
- ✅ testEditPatientPage
- ✅ testUpdatePatient
- ✅ testDeletePatient
- ✅ testSearchPatients
- ✅ testShowNonExistentPatient
- ✅ testPagination

#### OdontogramaControllerTest (15 tests)
- ✅ testOdontogramaPageLoads
- ✅ testOdontogramaForNonExistentPatient
- ✅ testGetOdontogramaApi
- ✅ testGetEstadosApi
- ✅ testUpdateSuperficieApi
- ✅ testUpdateDienteApi
- ✅ testHistorialPage
- ✅ testGetHistorialApi
- ✅ testAutoCreateOdontograma
- ✅ testInvalidToothNumber
- ✅ testInvalidSurface
- ✅ testChangeTipoDentadura
- ✅ testGetResumenEstados
- ✅ testExportPdf
- ✅ testOdontogramaDataStructure

#### AuthControllerTest (13 tests)
- ✅ testLoginPageLoads
- ✅ testRedirectIfLoggedIn
- ✅ testLoginWithValidCredentials
- ✅ testLoginWithWrongPassword
- ✅ testLoginWithNonExistentEmail
- ✅ testLoginWithEmptyFields
- ✅ testLoginWithInvalidEmail
- ✅ testLogout
- ✅ testLogoutDestroysSession
- ✅ testProtectedRouteWithoutAuth
- ✅ testSessionDataAfterLogin
- ✅ testInputSanitization
- ✅ testLoginPageContent

#### OdontogramaApiTest (10 tests)
- ✅ testGetEstadosEndpoint
- ✅ testGetOdontogramaEndpoint
- ✅ testUpdateSuperficieEndpoint
- ✅ testUpdateDienteEndpoint
- ✅ testGetHistorialEndpoint
- ✅ testGetOdontogramaInvalidPatient
- ✅ testUpdateSuperficieInvalidData
- ✅ testJsonResponseFormat
- ✅ testApiWithoutAuth
- ✅ testPostMethodRequired

### Database Tests

#### DatabaseSchemaTest (12 tests)
- ✅ testRequiredTablesExist
- ✅ testPacientesTableStructure
- ✅ testUsuariosTableStructure
- ✅ testCitasTableStructure
- ✅ testOdontogramasTableStructure
- ✅ testOdontogramaDientesTableStructure
- ✅ testPrimaryKeys
- ✅ testCatalogosOdontologicosTable
- ✅ testCatalogosOdontologicosHasData
- ✅ testForeignKeyConsistency
- ✅ testDataTypes
- ✅ testTimestampColumns

## Total de Tests: ~100+

## Resources

* [CodeIgniter 4 User Guide on Testing](https://codeigniter.com/user_guide/testing/index.html)
* [PHPUnit docs](https://phpunit.de/documentation.html)

## Requirements

```bash
composer install
```

## Quick Start

```bash
# Run all tests
php vendor/bin/phpunit

# Run with verbose output
php vendor/bin/phpunit -v

# Run specific test file
php vendor/bin/phpunit tests/unit/Models/PacientesModelTest.php

# Run with coverage report
php vendor/bin/phpunit --coverage-html build/coverage
```
