<?php

namespace App\Models;

use CodeIgniter\Model;

class CotizacionesDetallesModel extends Model
{
    protected $table            = 'cotizaciones_detalles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_cotizacion', 'id_servicio', 'cantidad', 'precio_unitario', 'subtotal'];
    protected $useTimestamps    = false;
}
