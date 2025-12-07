<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCitasTable extends Migration
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
            // pacientes.id is NOT unsigned (signed INT)
            'id_paciente' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false,
            ],
            // usuarios.id IS unsigned
            'id_usuario' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            // servicios.id IS unsigned
            'id_servicio' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'titulo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'fecha_inicio' => [
                'type' => 'DATETIME',
            ],
            'fecha_fin' => [
                'type' => 'DATETIME',
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['programada', 'confirmada', 'en_progreso', 'completada', 'cancelada'],
                'default' => 'programada',
            ],
            'tipo_cita' => [
                'type' => 'ENUM',
                'constraint' => ['consulta', 'tratamiento', 'revision', 'urgencia'],
                'default' => 'consulta',
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#5ccdde',
            ],
            'notas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'recordatorio_enviado' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        
        // Índices para optimización (sin foreign keys por incompatibilidad de tipos)
        $this->forge->addKey(['id_usuario', 'fecha_inicio'], false, false, 'idx_usuario_fecha');
        $this->forge->addKey(['id_paciente', 'fecha_inicio'], false, false, 'idx_paciente_fecha');
        $this->forge->addKey('estado', false, false, 'idx_estado');
        
        $this->forge->createTable('citas');
    }

    public function down()
    {
        $this->forge->dropTable('citas');
    }
}
