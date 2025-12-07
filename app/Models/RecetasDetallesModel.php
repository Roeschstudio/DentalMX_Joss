<?php

namespace App\Models;

use CodeIgniter\Model;

class RecetasDetallesModel extends Model
{
    protected $table            = 'recetas_detalles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_receta', 'id_medicamento', 'dosis', 'duracion', 'cantidad'];
    protected $useTimestamps    = false;
}
