<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder para poblar la tabla de catálogos odontológicos con estados dentales estándar.
 * 
 * Los códigos siguen convenciones internacionales para registros odontológicos:
 * - sano: Diente sin patología
 * - caries: Lesión cariosa
 * - obturado: Restauración/empaste presente
 * - extraccion: Diente indicado para extracción
 * - ausente: Diente faltante
 * - corona: Prótesis fija tipo corona
 * - endodoncia: Tratamiento de conductos
 * - implante: Implante dental colocado
 * - protesis: Prótesis removible
 * - sellador: Sellador de fosetas y fisuras
 * - fractura: Diente fracturado
 * - movilidad: Diente con movilidad patológica
 */
class CatalogosOdontologicosSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Estados básicos
            [
                'tipo' => 'estado_diente',
                'codigo' => 'sano',
                'nombre' => 'Sano',
                'descripcion' => 'Diente sano sin patología visible',
                'color_hex' => '#28a745',
                'icono' => 'check-circle',
                'orden' => 1,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'caries',
                'nombre' => 'Caries',
                'descripcion' => 'Lesión cariosa presente',
                'color_hex' => '#dc3545',
                'icono' => 'alert-circle',
                'orden' => 2,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'obturado',
                'nombre' => 'Obturado',
                'descripcion' => 'Diente con restauración/empaste',
                'color_hex' => '#007bff',
                'icono' => 'square',
                'orden' => 3,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'extraccion',
                'nombre' => 'Indicado para extracción',
                'descripcion' => 'Diente indicado para ser extraído',
                'color_hex' => '#6c757d',
                'icono' => 'x-circle',
                'orden' => 4,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'ausente',
                'nombre' => 'Ausente',
                'descripcion' => 'Diente faltante/extraído',
                'color_hex' => '#343a40',
                'icono' => 'minus-circle',
                'orden' => 5,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Tratamientos protésicos
            [
                'tipo' => 'estado_diente',
                'codigo' => 'corona',
                'nombre' => 'Corona',
                'descripcion' => 'Prótesis fija tipo corona',
                'color_hex' => '#ffc107',
                'icono' => 'award',
                'orden' => 6,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'puente',
                'nombre' => 'Puente',
                'descripcion' => 'Pilar de puente fijo',
                'color_hex' => '#fd7e14',
                'icono' => 'link',
                'orden' => 7,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'implante',
                'nombre' => 'Implante',
                'descripcion' => 'Implante dental colocado',
                'color_hex' => '#6f42c1',
                'icono' => 'crosshair',
                'orden' => 8,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'protesis',
                'nombre' => 'Prótesis Removible',
                'descripcion' => 'Diente de prótesis removible',
                'color_hex' => '#20c997',
                'icono' => 'refresh-cw',
                'orden' => 9,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Tratamientos
            [
                'tipo' => 'estado_diente',
                'codigo' => 'endodoncia',
                'nombre' => 'Endodoncia',
                'descripcion' => 'Tratamiento de conductos realizado',
                'color_hex' => '#e83e8c',
                'icono' => 'zap',
                'orden' => 10,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'sellador',
                'nombre' => 'Sellador',
                'descripcion' => 'Sellador de fosetas y fisuras',
                'color_hex' => '#17a2b8',
                'icono' => 'shield',
                'orden' => 11,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Patologías
            [
                'tipo' => 'estado_diente',
                'codigo' => 'fractura',
                'nombre' => 'Fractura',
                'descripcion' => 'Diente fracturado',
                'color_hex' => '#795548',
                'icono' => 'slash',
                'orden' => 12,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'movilidad',
                'nombre' => 'Movilidad',
                'descripcion' => 'Diente con movilidad patológica',
                'color_hex' => '#ff5722',
                'icono' => 'move',
                'orden' => 13,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'abrasion',
                'nombre' => 'Abrasión',
                'descripcion' => 'Desgaste por abrasión',
                'color_hex' => '#9e9e9e',
                'icono' => 'trending-down',
                'orden' => 14,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'tipo' => 'estado_diente',
                'codigo' => 'erosion',
                'nombre' => 'Erosión',
                'descripcion' => 'Erosión dental',
                'color_hex' => '#607d8b',
                'icono' => 'droplet',
                'orden' => 15,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // En tratamiento
            [
                'tipo' => 'estado_diente',
                'codigo' => 'provisional',
                'nombre' => 'Provisional',
                'descripcion' => 'Restauración provisional',
                'color_hex' => '#cddc39',
                'icono' => 'clock',
                'orden' => 16,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insertar solo si no existen
        foreach ($data as $row) {
            $exists = $this->db->table('catalogos_odontologicos')
                              ->where('tipo', $row['tipo'])
                              ->where('codigo', $row['codigo'])
                              ->countAllResults();
            
            if ($exists === 0) {
                $this->db->table('catalogos_odontologicos')->insert($row);
            }
        }

        echo "Catálogos odontológicos insertados correctamente.\n";
    }
}

