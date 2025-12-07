<?php

namespace App\Models;

use CodeIgniter\Model;

class RecetasModel extends Model
{
    protected $table            = 'recetas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['id_paciente', 'id_usuario', 'folio', 'fecha', 'notas_adicionales'];
    protected $useTimestamps    = true;
}
