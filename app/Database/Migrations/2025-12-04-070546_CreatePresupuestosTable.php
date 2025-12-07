<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePresupuestosTable extends Migration
{
    public function up()
    {
        // Tabla presupuestos
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_paciente' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false,
            ],
            'id_usuario' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false,
            ],
            'folio' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'fecha_emision' => [
                'type' => 'DATETIME',
            ],
            'fecha_vigencia' => [
                'type' => 'DATE',
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'iva' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['borrador', 'pendiente', 'aprobado', 'rechazado', 'convertido'],
                'default' => 'borrador',
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('folio', false, true); // Unique key
        $this->forge->addKey(['id_paciente'], false, false, 'idx_paciente');
        $this->forge->addKey(['estado'], false, false, 'idx_estado');
        // Foreign keys sin restricción estricta para compatibilidad con tablas existentes
        // $this->forge->addForeignKey('id_paciente', 'pacientes', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('id_usuario', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('presupuestos');

        // Tabla presupuestos_detalles
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_presupuesto' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_servicio' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'descripcion' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'cantidad' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 1.00,
            ],
            'precio_unitario' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'descuento_porcentaje' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
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
        $this->forge->addKey(['id_presupuesto'], false, false, 'idx_presupuesto');
        $this->forge->addKey(['id_servicio'], false, false, 'idx_servicio');
        $this->forge->addForeignKey('id_presupuesto', 'presupuestos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_servicio', 'servicios', 'id', 'RESTRICT', 'RESTRICT'); // Use RESTRICT or CASCADE based on preference, RESTRICT is safer for services
        $this->forge->createTable('presupuestos_detalles');
    }

    public function down()
    {
        $this->forge->dropTable('presupuestos_detalles');
        $this->forge->dropTable('presupuestos');
    }
}
