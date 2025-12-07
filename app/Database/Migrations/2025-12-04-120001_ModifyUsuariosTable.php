<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyUsuariosTable extends Migration
{
    public function up()
    {
        $fields = [
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'email'
            ],
            'direccion' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'telefono'
            ],
            'foto_perfil' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'direccion'
            ]
        ];
        
        $this->forge->addColumn('usuarios', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('usuarios', ['telefono', 'direccion', 'foto_perfil']);
    }
}
