<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfiguracionClinicaModel extends Model
{
    protected $table = 'configuracion_clinica';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre_clinica', 'logo', 'telefono', 'email', 
        'direccion', 'horario_atencion', 'vigencia_presupuestos', 
        'mensaje_bienvenida', 'mail_host', 'mail_port', 'mail_username',
        'mail_password', 'mail_encryption', 'mail_from_email', 'mail_from_name'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validaciones
    protected $validationRules = [
        'nombre_clinica' => 'required|max_length[150]',
        'telefono' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[150]',
        'vigencia_presupuestos' => 'required|integer|greater_than[0]',
    ];

    protected $validationMessages = [
        'nombre_clinica' => [
            'required' => 'El nombre de la clínica es obligatorio',
            'max_length' => 'El nombre no puede exceder 150 caracteres',
        ],
        'vigencia_presupuestos' => [
            'required' => 'La vigencia de presupuestos es obligatoria',
            'greater_than' => 'La vigencia debe ser mayor a 0',
        ],
    ];

    // Obtener configuración (crear si no existe)
    public function getConfiguracion()
    {
        $config = $this->first();
        if (!$config) {
            // Crear configuración por defecto
            $this->insert([
                'nombre_clinica' => 'Dental MX',
                'vigencia_presupuestos' => 30,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $config = $this->first();
        }
        return $config;
    }

    // Actualizar configuración
    public function actualizarConfiguracion($data)
    {
        $config = $this->first();
        if ($config) {
            return $this->update($config['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    // Obtener logo de la clínica
    public function getLogo()
    {
        $config = $this->first();
        return $config ? $config['logo'] : null;
    }
}
