<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration para crear las tablas del sistema de odontograma.
 * 
 * Tablas creadas:
 * - odontogramas: Registro principal vinculado al paciente
 * - odontograma_dientes: Estado de cada diente con sus 5 superficies
 * - odontograma_historial: Histórico de cambios realizados
 * - catalogos_odontologicos: Catálogo de estados dentales
 */
class CreateOdontogramaTables extends Migration
{
    public function up()
    {
        // Tabla de catálogos odontológicos (si no existe)
        if (!$this->db->tableExists('catalogos_odontologicos')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'tipo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => false,
                    'comment' => 'Tipo de catálogo: estado_diente, condicion, etc.',
                ],
                'codigo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => false,
                    'comment' => 'Código único del estado',
                ],
                'nombre' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => false,
                    'comment' => 'Nombre visible del estado',
                ],
                'descripcion' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'Descripción detallada',
                ],
                'color' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => true,
                    'default' => '#6c757d',
                    'comment' => 'Color hexadecimal para visualización',
                ],
                'icono' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => true,
                    'comment' => 'Nombre del icono (feather icons)',
                ],
                'orden' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                    'comment' => 'Orden de visualización',
                ],
                'activo' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
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
            $this->forge->addUniqueKey(['tipo', 'codigo'], 'idx_tipo_codigo');
            $this->forge->createTable('catalogos_odontologicos');
        }

        // Tabla principal de odontogramas
        if (!$this->db->tableExists('odontogramas')) {
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
                    'null' => false,
                    'comment' => 'ID del paciente propietario',
                ],
                'tipo' => [
                    'type' => 'ENUM',
                    'constraint' => ['adulto', 'infantil'],
                    'default' => 'adulto',
                    'comment' => 'Tipo de dentición',
                ],
                'notas' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'Notas generales del odontograma',
                ],
                'fecha_ultima_actualizacion' => [
                    'type' => 'DATETIME',
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
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('id_paciente', false, false, 'idx_odontograma_paciente');
            $this->forge->createTable('odontogramas');
        }

        // Tabla de dientes del odontograma
        if (!$this->db->tableExists('odontograma_dientes')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'id_odontograma' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => false,
                ],
                'numero_diente' => [
                    'type' => 'INT',
                    'constraint' => 2,
                    'null' => false,
                    'comment' => 'Número FDI del diente (11-48 adultos, 51-85 niños)',
                ],
                'estado_general' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'sano',
                    'comment' => 'Estado general del diente',
                ],
                'superficie_oclusal' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'sano',
                    'comment' => 'Estado superficie oclusal/incisal (O/I)',
                ],
                'superficie_mesial' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'sano',
                    'comment' => 'Estado superficie mesial (M)',
                ],
                'superficie_distal' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'sano',
                    'comment' => 'Estado superficie distal (D)',
                ],
                'superficie_vestibular' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'sano',
                    'comment' => 'Estado superficie vestibular/bucal (V/B)',
                ],
                'superficie_lingual' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'sano',
                    'comment' => 'Estado superficie lingual/palatina (L/P)',
                ],
                'notas' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'Notas específicas del diente',
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
            $this->forge->addKey('id_odontograma', false, false, 'idx_diente_odontograma');
            $this->forge->addUniqueKey(['id_odontograma', 'numero_diente'], 'idx_odontograma_diente');
            $this->forge->addForeignKey('id_odontograma', 'odontogramas', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('odontograma_dientes');
        }

        // Tabla de historial de cambios
        if (!$this->db->tableExists('odontograma_historial')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'id_odontograma' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => false,
                ],
                'numero_diente' => [
                    'type' => 'INT',
                    'constraint' => 2,
                    'null' => false,
                ],
                'superficie' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => true,
                    'comment' => 'oclusal, mesial, distal, vestibular, lingual, o null para estado general',
                ],
                'estado_anterior' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => true,
                ],
                'estado_nuevo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => false,
                ],
                'id_usuario' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'comment' => 'Usuario que realizó el cambio',
                ],
                'notas' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('id_odontograma', false, false, 'idx_historial_odontograma');
            $this->forge->addKey('numero_diente', false, false, 'idx_historial_diente');
            $this->forge->addForeignKey('id_odontograma', 'odontogramas', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('odontograma_historial');
        }
    }

    public function down()
    {
        // Eliminar en orden inverso por las llaves foráneas
        $this->forge->dropTable('odontograma_historial', true);
        $this->forge->dropTable('odontograma_dientes', true);
        $this->forge->dropTable('odontogramas', true);
        $this->forge->dropTable('catalogos_odontologicos', true);
    }
}
