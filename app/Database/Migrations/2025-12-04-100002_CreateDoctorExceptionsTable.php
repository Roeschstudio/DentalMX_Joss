<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migración para crear la tabla de excepciones de horario
 * Almacena días no disponibles, vacaciones, etc.
 */
class CreateDoctorExceptionsTable extends Migration
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
            'fecha' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'motivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'todo_el_dia' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1=Todo el día, 0=Parcial',
            ],
            'hora_inicio' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Solo si es parcial',
            ],
            'hora_fin' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Solo si es parcial',
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
        $this->forge->addKey('usuario_id');
        $this->forge->addKey(['usuario_id', 'fecha']);
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('doctor_exceptions');
    }

    public function down()
    {
        $this->forge->dropTable('doctor_exceptions');
    }
}
