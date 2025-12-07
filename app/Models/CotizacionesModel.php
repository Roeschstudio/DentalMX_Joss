<?php

namespace App\Models;

use CodeIgniter\Model;

class CotizacionesModel extends Model
{
    protected $table            = 'cotizaciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['id_paciente', 'id_usuario', 'fecha_emision', 'fecha_vigencia', 'total', 'estado', 'observaciones'];
    protected $useTimestamps    = true;
}
