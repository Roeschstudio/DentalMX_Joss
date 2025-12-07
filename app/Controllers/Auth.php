<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuariosModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Auth extends BaseController
{
    public function index()
    {
        try {
            if (session()->get('isLoggedIn')) {
                log_message('info', 'Usuario ya logueado, redirigiendo al dashboard');
                return redirect()->to('/');
            }
            return view('auth/login');
        } catch (\Exception $e) {
            log_message('error', 'Excepción cargando login: ' . $e->getMessage());
            return view('auth/login');
        }
    }

    public function login()
    {
        $session = session();
        $model = new UsuariosModel();
        
        try {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            
            // Validaciones básicas
            if (empty($email) || empty($password)) {
                $session->setFlashdata('error', 'Por favor, completa todos los campos');
                return redirect()->to('/login');
            }
            
            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $session->setFlashdata('error', 'El correo electrónico no es válido');
                return redirect()->to('/login');
            }
            
            $user = $model->getUserByEmail($email);
            
            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $ses_data = [
                        'id'       => $user['id'],
                        'nombre'   => $user['nombre'],
                        'email'    => $user['email'],
                        'rol'      => $user['rol'],
                        'isLoggedIn' => true,
                        'last_login' => date('Y-m-d H:i:s')
                    ];
                    $session->set($ses_data);
                    
                    log_message('info', 'Login exitoso: ' . $email);
                    
                    $session->setFlashdata('success', '¡Bienvenido ' . $user['nombre'] . '!');
                    return redirect()->to('/');
                } else {
                    log_message('warning', 'Login fallido (password incorrecto): ' . $email);
                    $session->setFlashdata('error', 'La contraseña ingresada es incorrecta');
                    return redirect()->to('/login');
                }
            } else {
                log_message('warning', 'Login fallido (email no encontrado): ' . $email);
                $session->setFlashdata('error', 'No existe una cuenta con ese correo electrónico');
                return redirect()->to('/login');
            }
            
        } catch (DatabaseException $e) {
            log_message('error', 'Error DB en login: ' . $e->getMessage());
            $session->setFlashdata('error', 'Error del sistema. Por favor, intenta más tarde.');
            return redirect()->to('/login');
            
        } catch (\Exception $e) {
            log_message('critical', 'Excepción en login: ' . $e->getMessage());
            $session->setFlashdata('error', 'Error del sistema. Contacta al administrador.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        $session = session();
        $email = $session->get('email');
        log_message('info', 'Logout usuario: ' . ($email ?? 'unknown'));
        
        $session->destroy();
        return redirect()->to('/login');
    }
}
