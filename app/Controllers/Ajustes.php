<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use App\Models\ConfiguracionClinicaModel;
use App\Models\PreferenciasUsuarioModel;

class Ajustes extends BaseController
{
    protected $usuariosModel;
    protected $configuracionModel;
    protected $preferenciasModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        helper(['form', 'url']);
        $this->usuariosModel = new UsuariosModel();
        $this->configuracionModel = new ConfiguracionClinicaModel();
        $this->preferenciasModel = new PreferenciasUsuarioModel();
    }

    // Vista principal de ajustes
    public function index()
    {
        return view('ajustes/index');
    }

    // Configuración de perfil
    public function perfil()
    {
        $id_usuario = session()->get('id');
        $usuario = $this->usuariosModel->find($id_usuario);
        $preferencias = $this->preferenciasModel->getPreferencias($id_usuario);
        
        return view('ajustes/perfil', [
            'usuario' => $usuario,
            'preferencias' => $preferencias
        ]);
    }

    // Actualizar perfil
    public function actualizarPerfil()
    {
        $id_usuario = session()->get('id');
        
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'telefono' => 'permit_empty|max_length[20]',
            'direccion' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'direccion' => $this->request->getPost('direccion'),
        ];

        // Manejar upload de foto
        $file = $this->request->getFile('foto_perfil');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return redirect()->back()->with('error', 'Tipo de archivo no permitido. Solo JPEG, PNG o GIF.');
            }

            // Validar tamaño (max 2MB)
            if ($file->getSize() > 2097152) {
                return redirect()->back()->with('error', 'El archivo excede el tamaño máximo permitido (2MB).');
            }

            // Crear directorio en public si no existe (accesible via URL)
            $uploadPath = FCPATH . 'uploads/perfiles';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Eliminar foto anterior si existe
            $usuario = $this->usuariosModel->find($id_usuario);
            if (!empty($usuario['foto_perfil']) && file_exists($uploadPath . '/' . $usuario['foto_perfil'])) {
                unlink($uploadPath . '/' . $usuario['foto_perfil']);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['foto_perfil'] = $newName;
        }

        // Verificar si el email ya existe (excepto el actual)
        $existingUser = $this->usuariosModel->where('email', $data['email'])
                                          ->where('id !=', $id_usuario)
                                          ->first();
        if ($existingUser) {
            return redirect()->back()->withInput()->with('error', 'El email ya está en uso por otro usuario.');
        }

        // Actualizar preferencias si se enviaron
        if ($this->request->getPost('tema')) {
            $preferencesData = [
                'tema' => $this->request->getPost('tema'),
                'idioma' => $this->request->getPost('idioma'),
                'notificaciones_email' => $this->request->getPost('notificaciones_email') ? 1 : 0,
                'notificaciones_sistema' => $this->request->getPost('notificaciones_sistema') ? 1 : 0,
                'formato_fecha' => $this->request->getPost('formato_fecha'),
            ];
            $this->preferenciasModel->actualizarPreferencias($id_usuario, $preferencesData);
        }

        $this->usuariosModel->update($id_usuario, $data);
        
        return redirect()->to('/ajustes/perfil')
                        ->with('success', 'Perfil actualizado correctamente');
    }

    // Configuración de clínica
    public function clinica()
    {
        $configuracion = $this->configuracionModel->getConfiguracion();
        
        return view('ajustes/clinica', [
            'configuracion' => $configuracion
        ]);
    }

    // Actualizar configuración de clínica
    public function actualizarClinica()
    {
        $rules = [
            'nombre_clinica' => 'required|min_length[3]|max_length[150]',
            'email' => 'permit_empty|valid_email|max_length[150]',
            'telefono' => 'permit_empty|max_length[20]',
            'vigencia_presupuestos' => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombre_clinica' => $this->request->getPost('nombre_clinica'),
            'telefono' => $this->request->getPost('telefono'),
            'email' => $this->request->getPost('email'),
            'direccion' => $this->request->getPost('direccion'),
            'horario_atencion' => $this->request->getPost('horario_atencion'),
            'vigencia_presupuestos' => $this->request->getPost('vigencia_presupuestos'),
            'mensaje_bienvenida' => $this->request->getPost('mensaje_bienvenida'),
        ];

        // Manejar upload de logo
        $file = $this->request->getFile('logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return redirect()->back()->with('error', 'Tipo de archivo no permitido. Solo JPEG, PNG o GIF.');
            }

            // Validar tamaño (max 2MB)
            if ($file->getSize() > 2097152) {
                return redirect()->back()->with('error', 'El archivo excede el tamaño máximo permitido (2MB).');
            }

            // Crear directorio en public si no existe (accesible via URL)
            $uploadPath = FCPATH . 'uploads/logos';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Eliminar logo anterior si existe
            $configActual = $this->configuracionModel->getConfiguracion();
            if (!empty($configActual['logo']) && file_exists($uploadPath . '/' . $configActual['logo'])) {
                unlink($uploadPath . '/' . $configActual['logo']);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['logo'] = $newName;
        }

        $this->configuracionModel->actualizarConfiguracion($data);
        
        return redirect()->to('/ajustes/clinica')
                        ->with('success', 'Configuración de clínica actualizada correctamente');
    }

    // Preferencias de usuario
    public function preferencias()
    {
        $id_usuario = session()->get('id');
        $preferencias = $this->preferenciasModel->getPreferencias($id_usuario);
        
        return view('ajustes/preferencias', [
            'preferencias' => $preferencias
        ]);
    }

    // Actualizar preferencias
    public function actualizarPreferencias()
    {
        $id_usuario = session()->get('id');
        
        $rules = [
            'tema' => 'required|in_list[light,dark,auto]',
            'idioma' => 'required|max_length[5]',
            'formato_fecha' => 'required|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tema' => $this->request->getPost('tema'),
            'idioma' => $this->request->getPost('idioma'),
            'notificaciones_email' => $this->request->getPost('notificaciones_email') ? 1 : 0,
            'notificaciones_sistema' => $this->request->getPost('notificaciones_sistema') ? 1 : 0,
            'formato_fecha' => $this->request->getPost('formato_fecha'),
        ];

        $this->preferenciasModel->actualizarPreferencias($id_usuario, $data);
        
        return redirect()->to('/ajustes/preferencias')
                        ->with('success', 'Preferencias actualizadas correctamente');
    }

    // Formulario de cambio de contraseña
    public function cambiarContrasena()
    {
        return view('ajustes/cambiar_contrasena');
    }

    // Actualizar contraseña
    public function actualizarContrasena()
    {
        $id_usuario = session()->get('id');
        
        $rules = [
            'contrasena_actual' => 'required',
            'contrasena_nueva' => 'required|min_length[8]',
            'confirmar_contrasena' => 'required|matches[contrasena_nueva]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $contrasena_actual = $this->request->getPost('contrasena_actual');
        $contrasena_nueva = $this->request->getPost('contrasena_nueva');

        // Verificar contraseña actual
        $usuario = $this->usuariosModel->find($id_usuario);
        if (!password_verify($contrasena_actual, $usuario['password'])) {
            return redirect()->back()->with('error', 'La contraseña actual es incorrecta');
        }

        // Actualizar contraseña
        $this->usuariosModel->update($id_usuario, [
            'password' => password_hash($contrasena_nueva, PASSWORD_DEFAULT)
        ]);
        
        return redirect()->to('/ajustes/perfil')
                        ->with('success', 'Contraseña actualizada correctamente');
    }

    // Configuración de correo
    public function correo()
    {
        $configuracion = $this->configuracionModel->getConfiguracion();
        
        return view('ajustes/correo', [
            'configuracion' => $configuracion
        ]);
    }

    // Actualizar configuración de correo
    public function actualizarCorreo()
    {
        $rules = [
            'mail_host' => 'required|max_length[255]',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|max_length[255]',
            'mail_from_email' => 'required|valid_email|max_length[255]',
            'mail_from_name' => 'required|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'mail_host' => $this->request->getPost('mail_host'),
            'mail_port' => $this->request->getPost('mail_port'),
            'mail_username' => $this->request->getPost('mail_username'),
            'mail_password' => $this->request->getPost('mail_password'),
            'mail_encryption' => $this->request->getPost('mail_encryption'),
            'mail_from_email' => $this->request->getPost('mail_from_email'),
            'mail_from_name' => $this->request->getPost('mail_from_name'),
        ];

        // Si no se proporciona contraseña, mantener la actual
        if (empty($data['mail_password'])) {
            unset($data['mail_password']);
        }

        $this->configuracionModel->actualizarConfiguracion($data);
        
        return redirect()->to('/ajustes/correo')
                        ->with('success', 'Configuración de correo actualizada correctamente');
    }

    // Probar configuración de correo
    public function probarCorreo()
    {
        $configuracion = $this->configuracionModel->getConfiguracion();
        
        // Configurar email para prueba
        $email = \Config\Services::email();
        $email->setFrom($configuracion['mail_from_email'], $configuracion['mail_from_name']);
        $email->setTo($this->request->getPost('email_test'));
        $email->setSubject('Prueba de Configuración de Correo');
        $email->setMessage('Este es un correo de prueba para verificar que la configuración SMTP es correcta.');
        
        // Configurar SMTP
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => $configuracion['mail_host'],
            'SMTPPort' => $configuracion['mail_port'],
            'SMTPUser' => $configuracion['mail_username'],
            'SMTPPass' => $configuracion['mail_password'],
            'SMTPCrypto' => $configuracion['mail_encryption'] ?? '',
            'mailType' => 'html',
        ];
        
        $email->initialize($config);
        
        if ($email->send()) {
            return redirect()->to('/ajustes/correo')
                            ->with('success', 'Correo de prueba enviado correctamente');
        } else {
            return redirect()->to('/ajustes/correo')
                            ->with('error', 'Error al enviar correo: ' . $email->printDebugger(['headers']));
        }
    }
}
