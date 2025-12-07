<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePreferenciasUsuarioTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_usuario' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tema' => [
                'type' => 'ENUM',
                'constraint' => ['light', 'dark', 'auto'],
                'default' => 'light',
            ],
            'idioma' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'default' => 'es',
            ],
            'notificaciones_email' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'notificaciones_sistema' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'formato_fecha' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'd/m/Y',
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
        $this->forge->addForeignKey('id_usuario', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey('id_usuario');
        $this->forge->createTable('preferencias_usuario');
    }

    public function down()
    {
        $this->forge->dropTable('preferencias_usuario');
    }
}
