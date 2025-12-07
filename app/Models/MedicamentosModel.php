<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicamentosModel extends Model
{
    protected $table            = 'medicamentos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nombre_comercial', 'sustancia_activa', 'presentacion', 'indicaciones_base', 'stock'];
    protected $useTimestamps    = true;
}
