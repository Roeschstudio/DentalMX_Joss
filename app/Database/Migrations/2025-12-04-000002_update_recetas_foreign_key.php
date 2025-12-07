<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateRecetasForeignKey extends Migration
{
    public function up()
    {
        // Drop the existing foreign key constraint using raw SQL
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $this->db->query('ALTER TABLE recetas DROP FOREIGN KEY IF EXISTS recetas_id_paciente_foreign');
        
        // Modify the column to reference the new patients table
        $this->forge->modifyColumn('recetas', [
            'id_paciente' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'after' => 'id',
            ],
        ]);
        
        // Add the new foreign key constraint
        $this->forge->addForeignKey('id_paciente', 'patients', 'id', 'CASCADE', 'CASCADE');
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        
        // Drop the foreign key to patients table
        $this->db->query('ALTER TABLE recetas DROP FOREIGN KEY IF EXISTS recetas_id_paciente_foreign');
        
        // Revert the column to reference the old pacientes table
        $this->forge->modifyColumn('recetas', [
            'id_paciente' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false,
                'null' => false,
                'after' => 'id',
            ],
        ]);
        
        // Add back the original foreign key constraint
        $this->forge->addForeignKey('id_paciente', 'pacientes', 'id', 'CASCADE', 'CASCADE');
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}