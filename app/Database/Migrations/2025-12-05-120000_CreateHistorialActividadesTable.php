<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration para crear las tablas del historial de actividades
 * 
 * Tablas creadas:
 * - historial_actividades: Registro de todas las actividades del paciente
 * - tratamientos_realizados: Seguimiento de tratamientos
 * - historial_adjuntos: Archivos adjuntos a las actividades
 */
class CreateHistorialActividadesTable extends Migration
{
    public function up()
    {
        // ========================================
        // Tabla: historial_actividades
        // ========================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_paciente' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
            ],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
            ],
            'tipo_actividad' => [
                'type'       => 'ENUM',
                'constraint' => ['cita', 'receta', 'presupuesto', 'cotizacion', 'nota_evolucion', 'tratamiento', 'pago', 'odontograma'],
            ],
            'id_referencia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'fecha_actividad' => [
                'type' => 'DATETIME',
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
        // Índices para optimización (sin foreign keys por incompatibilidad de tipos)
        $this->forge->addKey(['id_paciente', 'fecha_actividad'], false, false, 'idx_paciente_fecha');
        $this->forge->addKey('id_usuario', false, false, 'idx_usuario');
        $this->forge->addKey('tipo_actividad', false, false, 'idx_tipo');
        $this->forge->addKey('id_referencia', false, false, 'idx_referencia');
        $this->forge->createTable('historial_actividades');

        // ========================================
        // Tabla: tratamientos_realizados
        // ========================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_paciente' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
            ],
            'id_servicio' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
            ],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
            ],
            'diente' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'superficie' => [
                'type'       => 'ENUM',
                'constraint' => ['vestibular', 'lingual', 'oclusal', 'mesial', 'distal', 'incisal', 'palatino', 'bucal'],
                'null'       => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['iniciado', 'en_progreso', 'completado', 'suspendido', 'cancelado'],
                'default'    => 'iniciado',
            ],
            'fecha_inicio' => [
                'type' => 'DATE',
            ],
            'fecha_fin' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'costo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'pagado' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
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
        // Índices para optimización (sin foreign keys por incompatibilidad de tipos)
        $this->forge->addKey('id_paciente', false, false, 'idx_tratamiento_paciente');
        $this->forge->addKey('id_servicio', false, false, 'idx_tratamiento_servicio');
        $this->forge->addKey('id_usuario', false, false, 'idx_tratamiento_usuario');
        $this->forge->addKey('estado', false, false, 'idx_tratamiento_estado');
        $this->forge->addKey('fecha_inicio', false, false, 'idx_tratamiento_fecha');
        $this->forge->createTable('tratamientos_realizados');

        // ========================================
        // Tabla: historial_adjuntos
        // ========================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_historial_actividad' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nombre_archivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'ruta_archivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'tipo_archivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tamanio_archivo' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        // Índices para optimización
        $this->forge->addKey('id_historial_actividad', false, false, 'idx_adjunto_historial');
        $this->forge->addKey('tipo_archivo', false, false, 'idx_adjunto_tipo');
        $this->forge->createTable('historial_adjuntos');
    }

    public function down()
    {
        // Eliminar tablas en orden inverso
        $this->forge->dropTable('historial_adjuntos', true);
        $this->forge->dropTable('tratamientos_realizados', true);
        $this->forge->dropTable('historial_actividades', true);
    }
}
