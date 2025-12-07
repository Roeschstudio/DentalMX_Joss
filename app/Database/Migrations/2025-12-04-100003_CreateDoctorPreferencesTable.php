<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migración para crear la tabla de preferencias de doctor
 * Almacena configuraciones de duración de citas, descansos, etc.
 */
class CreateDoctorPreferencesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'ID del doctor/usuario',
            ],
            'duracion_cita' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 30,
                'comment'    => 'Duración de cita en minutos',
            ],
            'tiempo_descanso' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 15,
                'comment'    => 'Descanso entre citas en minutos',
            ],
            'citas_simultaneas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'comment'    => 'Número máximo de citas simultáneas',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('usuario_id', false, true); // Índice único
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('doctor_preferences');
    }

    public function down()
    {
        $this->forge->dropTable('doctor_preferences');
    }
}
