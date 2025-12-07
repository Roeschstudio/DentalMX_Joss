<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimestampsToDetallesTables extends Migration
{
    public function up()
    {
        // Add timestamps and soft deletes to presupuestos_detalles (already has created_at, updated_at, add deleted_at)
        if ($this->db->tableExists('presupuestos_detalles')) {
            // Add deleted_at for soft deletes
            $this->forge->addColumn('presupuestos_detalles', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'updated_at',
                ],
            ]);
        }

        // Add timestamps to cotizaciones_detalles
        if ($this->db->tableExists('cotizaciones_detalles')) {
            $this->forge->addColumn('cotizaciones_detalles', [
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'precio_unitario',
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'updated_at',
                ],
            ]);
        }

        // Add timestamps to recetas_detalles
        if ($this->db->tableExists('recetas_detalles')) {
            $this->forge->addColumn('recetas_detalles', [
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'dosis',
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'updated_at',
                ],
            ]);
        }
    }

    public function down()
    {
        // Remove timestamps from presupuestos_detalles
        if ($this->db->tableExists('presupuestos_detalles')) {
            $this->forge->dropColumn('presupuestos_detalles', 'deleted_at');
        }

        // Remove timestamps from cotizaciones_detalles
        if ($this->db->tableExists('cotizaciones_detalles')) {
            $this->forge->dropColumn('cotizaciones_detalles', ['created_at', 'updated_at', 'deleted_at']);
        }

        // Remove timestamps from recetas_detalles
        if ($this->db->tableExists('recetas_detalles')) {
            $this->forge->dropColumn('recetas_detalles', ['created_at', 'updated_at', 'deleted_at']);
        }
    }
}
