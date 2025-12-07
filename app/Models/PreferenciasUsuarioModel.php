<?php

namespace App\Models;

use CodeIgniter\Model;

class PreferenciasUsuarioModel extends Model
{
    protected $table = 'preferencias_usuario';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_usuario', 'tema', 'idioma', 'notificaciones_email', 
        'notificaciones_sistema', 'formato_fecha'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validaciones
    protected $validationRules = [
        'id_usuario' => 'required|integer',
        'tema' => 'required|in_list[light,dark,auto]',
        'idioma' => 'required|max_length[5]',
        'notificaciones_email' => 'required|in_list[0,1]',
        'notificaciones_sistema' => 'required|in_list[0,1]',
        'formato_fecha' => 'required|max_length[20]',
    ];

    // Obtener preferencias de usuario (crear si no existen)
    public function getPreferencias($id_usuario)
    {
        $preferencias = $this->where('id_usuario', $id_usuario)->first();
        if (!$preferencias) {
            // Crear preferencias por defecto
            $this->insert([
                'id_usuario' => $id_usuario,
                'tema' => 'light',
                'idioma' => 'es',
                'notificaciones_email' => true,
                'notificaciones_sistema' => true,
                'formato_fecha' => 'd/m/Y',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $preferencias = $this->where('id_usuario', $id_usuario)->first();
        }
        return $preferencias;
    }

    // Actualizar preferencias de usuario
    public function actualizarPreferencias($id_usuario, $data)
    {
        $preferencias = $this->where('id_usuario', $id_usuario)->first();
        if ($preferencias) {
            return $this->update($preferencias['id'], $data);
        } else {
            $data['id_usuario'] = $id_usuario;
            return $this->insert($data);
        }
    }
}
