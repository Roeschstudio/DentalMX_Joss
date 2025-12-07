<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Cotizaciones extends Migration
{
    public function up()
    {
        // Tabla Cabecera
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_paciente' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_usuario' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'fecha_emision' => ['type' => 'DATETIME', 'null' => true],
            'fecha_vigencia' => ['type' => 'DATE', 'null' => true],
            'total' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'estado' => ['type' => 'ENUM', 'constraint' => ['pendiente', 'aceptada', 'rechazada'], 'default' => 'pendiente'],
            'observaciones' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cotizaciones');

        // Tabla Detalles
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_cotizacion' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_servicio' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'cantidad' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'precio_unitario' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cotizaciones_detalles');
    }

    public function down()
    {
        $this->forge->dropTable('cotizaciones_detalles');
        $this->forge->dropTable('cotizaciones');
    }
}
