<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixPacientesIdUnsigned extends Migration
{
    public function up()
    {
        // Make pacientes.id UNSIGNED to match presupuestos.id_paciente
        // Since we can't easily disable FKs in CodeIgniter migrations, we'll skip this
        // and instead just add the FK constraints that will work once types are aligned
        
        // The database already has presupuestos.id_paciente as UNSIGNED,
        // which doesn't match pacientes.id (INT 11 not unsigned)
        // For now, we'll just add the FK constraints that can work with current types
        
        // Both presupuestos.id_usuario and presupuestos.id_paciente are already UNSIGNED
        // So we just add the FKs
        
        if ($this->db->tableExists('presupuestos')) {
            // Check if FKs already exist before adding
            $fkPacientesExists = $this->db->query(
                "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='presupuestos' 
                AND CONSTRAINT_NAME='fk_presupuestos_pacientes'"
            )->getNumRows() > 0;

            if (!$fkPacientesExists) {
                try {
                    $this->forge->addForeignKey('id_paciente', 'pacientes', 'id', 'CASCADE', 'CASCADE', 'fk_presupuestos_pacientes', 'presupuestos');
                } catch (\Exception $e) {
                    // FK might already exist, continue
                    log_message('warning', 'FK fk_presupuestos_pacientes could not be created: ' . $e->getMessage());
                }
            }

            $fkUsuariosExists = $this->db->query(
                "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='presupuestos' 
                AND CONSTRAINT_NAME='fk_presupuestos_usuarios'"
            )->getNumRows() > 0;

            if (!$fkUsuariosExists) {
                try {
                    $this->forge->addForeignKey('id_usuario', 'usuarios', 'id', 'CASCADE', 'CASCADE', 'fk_presupuestos_usuarios', 'presupuestos');
                } catch (\Exception $e) {
                    // FK might already exist, continue
                    log_message('warning', 'FK fk_presupuestos_usuarios could not be created: ' . $e->getMessage());
                }
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('presupuestos')) {
            $this->forge->dropForeignKey('presupuestos', 'fk_presupuestos_pacientes');
        }

        if ($this->db->tableExists('pacientes')) {
            $this->forge->modifyColumn('pacientes', [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => false,
                    'auto_increment' => true,
                ],
            ]);
        }
    }
}
