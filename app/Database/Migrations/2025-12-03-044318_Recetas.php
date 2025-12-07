<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Recetas extends Migration
{
    public function up()
    {
        // Tabla Cabecera
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_paciente' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => false],
            'id_usuario' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'folio' => ['type' => 'VARCHAR', 'constraint' => 20],
            'fecha' => ['type' => 'DATETIME', 'null' => true],
            'notas_adicionales' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_paciente', 'pacientes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_usuario', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recetas');

        // Tabla Detalles
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_receta' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_medicamento' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'dosis' => ['type' => 'VARCHAR', 'constraint' => 150],
            'duracion' => ['type' => 'VARCHAR', 'constraint' => 100],
            'cantidad' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_receta', 'recetas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_medicamento', 'medicamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recetas_detalles');
    }

    public function down()
    {
        $this->forge->dropTable('recetas_detalles');
        $this->forge->dropTable('recetas');
    }
}
