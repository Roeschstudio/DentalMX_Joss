<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToPacientesTable extends Migration
{
    public function up()
    {
        // Add deleted_at column to pacientes table for soft deletes
        $this->forge->addColumn('pacientes', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at'
            ]
        ]);
    }

    public function down()
    {
        // Remove deleted_at column from pacientes table
        $this->forge->dropColumn('pacientes', 'deleted_at');
    }
}