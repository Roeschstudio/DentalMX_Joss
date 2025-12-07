<?php

namespace Tests\Support\Database;

use App\Models\PacientesModel;
use App\Models\OdontogramaModel;
use App\Models\CitasModel;
use App\Models\UsuariosModel;

/**
 * Factory para crear datos de prueba
 */
class TestDataFactory
{
    protected static PacientesModel $pacientesModel;
    protected static OdontogramaModel $odontogramaModel;
    protected static CitasModel $citasModel;
    protected static UsuariosModel $usuariosModel;

    /**
     * Inicializar modelos
     */
    public static function init(): void
    {
        self::$pacientesModel = new PacientesModel();
        self::$odontogramaModel = new OdontogramaModel();
        self::$citasModel = new CitasModel();
        self::$usuariosModel = new UsuariosModel();
    }

    /**
     * Crear un paciente de prueba
     */
    public static function createPatient(array $overrides = []): array
    {
        self::init();

        $data = array_merge([
            'nombre' => 'Test' . rand(1000, 9999),
            'primer_apellido' => 'Apellido' . rand(100, 999),
            'segundo_apellido' => 'Segundo' . rand(100, 999),
            'fecha_nacimiento' => '1990-01-15',
            'nacionalidad' => 'Mexicana',
            'domicilio' => 'Calle Test #' . rand(1, 999),
            'telefono' => '555' . rand(1000000, 9999999),
            'celular' => '555' . rand(1000000, 9999999),
            'email' => 'test' . rand(10000, 99999) . '@example.com'
        ], $overrides);

        $id = self::$pacientesModel->insert($data);
        
        return array_merge(['id' => $id], $data);
    }

    /**
     * Crear múltiples pacientes de prueba
     */
    public static function createPatients(int $count, array $overrides = []): array
    {
        $patients = [];
        for ($i = 0; $i < $count; $i++) {
            $patients[] = self::createPatient($overrides);
        }
        return $patients;
    }

    /**
     * Crear un usuario de prueba
     */
    public static function createUser(array $overrides = []): array
    {
        self::init();

        $data = array_merge([
            'nombre' => 'Usuario Test ' . rand(100, 999),
            'email' => 'user' . rand(10000, 99999) . '@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'rol' => 'admin',
            'telefono' => '555' . rand(1000000, 9999999)
        ], $overrides);

        $id = self::$usuariosModel->insert($data);
        
        return array_merge(['id' => $id], $data);
    }

    /**
     * Crear una cita de prueba
     */
    public static function createAppointment(int $patientId, int $userId, array $overrides = []): array
    {
        self::init();

        $fechaInicio = date('Y-m-d H:i:s', strtotime('+' . rand(1, 30) . ' days 10:00'));
        $fechaFin = date('Y-m-d H:i:s', strtotime($fechaInicio . ' +1 hour'));

        $data = array_merge([
            'id_paciente' => $patientId,
            'id_usuario' => $userId,
            'titulo' => 'Cita de prueba ' . rand(100, 999),
            'descripcion' => 'Descripción de la cita de prueba',
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => 'programada',
            'tipo_cita' => 'consulta',
            'color' => '#5ccdde'
        ], $overrides);

        $id = self::$citasModel->insert($data);
        
        return array_merge(['id' => $id], $data);
    }

    /**
     * Crear un odontograma de prueba
     */
    public static function createOdontograma(int $patientId, array $overrides = []): array
    {
        self::init();

        return self::$odontogramaModel->getOrCreateOdontograma(
            $patientId,
            $overrides['tipo_dentadura'] ?? 'permanente'
        );
    }

    /**
     * Crear un conjunto completo de datos de prueba
     */
    public static function createFullTestData(): array
    {
        self::init();

        // Crear usuario
        $user = self::createUser();

        // Crear pacientes
        $patients = self::createPatients(5);

        // Crear citas para cada paciente
        $appointments = [];
        foreach ($patients as $patient) {
            $appointments[] = self::createAppointment($patient['id'], $user['id']);
        }

        // Crear odontogramas
        $odontogramas = [];
        foreach ($patients as $patient) {
            $odontogramas[] = self::createOdontograma($patient['id']);
        }

        return [
            'user' => $user,
            'patients' => $patients,
            'appointments' => $appointments,
            'odontogramas' => $odontogramas
        ];
    }

    /**
     * Limpiar datos de prueba (opcional)
     */
    public static function cleanup(): void
    {
        // Implementar si es necesario
    }

    /**
     * Obtener datos de paciente válidos para formularios
     */
    public static function getValidPatientFormData(): array
    {
        return [
            'nombre' => 'Juan',
            'primer_apellido' => 'Pérez',
            'segundo_apellido' => 'García',
            'email' => 'juan.perez@example.com',
            'telefono' => '5551234567',
            'celular' => '5559876543',
            'fecha_nacimiento' => '1990-05-15',
            'domicilio' => 'Calle Principal #123, Col. Centro',
            'nacionalidad' => 'Mexicana'
        ];
    }

    /**
     * Obtener datos de usuario válidos para formularios
     */
    public static function getValidUserFormData(): array
    {
        return [
            'nombre' => 'Dr. María López',
            'email' => 'maria.lopez@clinica.com',
            'password' => 'Password123!',
            'password_confirm' => 'Password123!',
            'rol' => 'doctor',
            'telefono' => '5551234567'
        ];
    }

    /**
     * Obtener datos de cita válidos para formularios
     */
    public static function getValidAppointmentFormData(int $patientId, int $userId): array
    {
        return [
            'id_paciente' => $patientId,
            'id_usuario' => $userId,
            'titulo' => 'Consulta General',
            'descripcion' => 'Primera consulta del paciente',
            'fecha_inicio' => date('Y-m-d H:i:s', strtotime('+1 day 10:00')),
            'fecha_fin' => date('Y-m-d H:i:s', strtotime('+1 day 11:00')),
            'estado' => 'programada',
            'tipo_cita' => 'consulta',
            'color' => '#5ccdde'
        ];
    }
}
