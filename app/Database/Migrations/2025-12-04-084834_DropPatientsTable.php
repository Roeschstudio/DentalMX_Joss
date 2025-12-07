<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropPatientsTable extends Migration
{
    public function up()
    {
        // Drop the patients table that conflicts with pacientes
        // The correct table is 'pacientes' which is used by Patient model
        if ($this->db->tableExists('patients')) {
            $this->forge->dropTable('patients');
        }
    }

    public function down()
    {
        // Recreate if needed - keeping empty for now
        // The patients table structure is NOT canonical
    }
}
