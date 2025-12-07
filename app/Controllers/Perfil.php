<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Navigation;

class Perfil extends BaseController
{
    public function __construct()
    {
        helper('form');
    }
    
    public function index()
    {
        // Preparar datos de navegación
        $navigationData = Navigation::prepareNavigationData('perfil', [
            'subtitle' => 'Información de tu cuenta'
        ]);
        
        // Obtener datos del usuario de la sesión
        $usuario = session()->get('usuario');
        
        $data = array_merge($navigationData, [
            'usuario' => $usuario,
            'pageTitle' => 'Mi Perfil'
        ]);
        
        return view('perfil/index', $data);
    }
    
    public function actualizar()
    {
        $usuario = session()->get('usuario');
        
        $rules = [
            'nombre' => 'required|min_length[2]|max_length[100]',
            'apellido' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]',
            'telefono' => 'max_length[20]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Por favor corrige los errores del formulario');
        }
        
        // Actualizar en base de datos
        $db = \Config\Database::connect();
        $builder = $db->table('usuarios');
        
        $updateData = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
        ];
        
        $builder->where('id', $usuario['id']);
        $builder->update($updateData);
        
        // Actualizar sesión
        $usuario = array_merge($usuario, $updateData);
        session()->set('usuario', $usuario);
        
        return redirect()->to('/perfil')->with('success', 'Perfil actualizado correctamente');
    }
    
    public function cambiarPassword()
    {
        $usuario = session()->get('usuario');
        
        $rules = [
            'password_actual' => 'required',
            'password_nuevo' => 'required|min_length[6]',
            'password_confirmar' => 'required|matches[password_nuevo]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Por favor corrige los errores del formulario');
        }
        
        $db = \Config\Database::connect();
        $builder = $db->table('usuarios');
        
        // Verificar contraseña actual
        $query = $builder->where('id', $usuario['id'])->get();
        $user = $query->getRowArray();
        
        if (!password_verify($this->request->getPost('password_actual'), $user['password'])) {
            return redirect()->back()->with('error', 'La contraseña actual es incorrecta');
        }
        
        // Actualizar contraseña
        $builder->where('id', $usuario['id']);
        $builder->update(['password' => password_hash($this->request->getPost('password_nuevo'), PASSWORD_DEFAULT)]);
        
        return redirect()->to('/perfil')->with('success', 'Contraseña actualizada correctamente');
    }
}
