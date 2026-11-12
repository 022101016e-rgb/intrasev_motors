<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Controller;

class AuthController extends BaseController
{
    public function index()
    {
        // Si ya está logueado, redirigir según su rol
        if (session()->has('id_usuario')) {
            if (session()->get('id_rol') == 1) {
                return redirect()->to(site_url('admin/dashboard'));
            } else {
                return redirect()->to(site_url('/'));
            }
        }
        
        return view('login');
    }

    public function login()
    {
        $usuarioModel = new UsuarioModel();

        $usuario = $this->request->getPost('usuario');
        $password = $this->request->getPost('password');

        // Buscar el usuario por su nombre de usuario
        $user = $usuarioModel->where('usuario', $usuario)->first();

        $usuario  = (string) $usuario;
        $password = (string) $password;

        // DEF-01: las contraseñas se almacenan con password_hash(). Para no romper cuentas
        // creadas antes de esta corrección (texto plano), se acepta la coincidencia exacta una
        // sola vez y se reemplaza de inmediato por su hash.
        $valido = false;
        if ($user) {
            $almacenada = (string) $user['contrasena_hash'];
            if (password_get_info($almacenada)['algo'] !== null) {
                $valido = password_verify($password, $almacenada);
                if ($valido && password_needs_rehash($almacenada, PASSWORD_DEFAULT)) {
                    $usuarioModel->update($user['id_usuario'], ['contrasena_hash' => password_hash($password, PASSWORD_DEFAULT)]);
                }
            } elseif (hash_equals($almacenada, $password)) {
                $valido = true;
                $usuarioModel->update($user['id_usuario'], ['contrasena_hash' => password_hash($password, PASSWORD_DEFAULT)]);
            }
        }

        if ($valido) {
            // Evita fijación de sesión
            session()->regenerate();

            // Establecer variables de sesión
            $sessionData = [
                'id_usuario' => $user['id_usuario'],
                'usuario' => $user['usuario'],
                'nombre_completo' => $user['nombre_completo'],
                'id_rol' => $user['id_rol'],
                'isLoggedIn' => true
            ];
            
            session()->set($sessionData);

            // Bifurcación según el id_rol
            if ($user['id_rol'] == 1) {
                // Administrador
                return redirect()->to(site_url('admin/dashboard'));
            } else {
                // Cliente
                return redirect()->to(site_url('/'));
            }
        } else {
            // Error en credenciales
            session()->setFlashdata('error', 'Usuario o contraseña incorrectos.');
            return redirect()->to(site_url('login'));
        }
    }

    public function registro()
    {
        return view('registro');
    }

    public function registrar()
    {
        $usuarioModel = new UsuarioModel();

        $datosForm = [
            'usuario'            => trim((string) $this->request->getPost('usuario')),
            'nombre_completo'    => trim((string) $this->request->getPost('nombre_completo')),
            'correo_electronico' => trim((string) $this->request->getPost('correo_electronico')),
            'telefono'           => trim((string) $this->request->getPost('telefono')),
            'contrasena'         => (string) $this->request->getPost('contrasena'),
        ];

        // DEF-07: antes no se validaba nada y un correo repetido producía un error 500 (clave única).
        $reglas = [
            'usuario'            => 'required|min_length[3]|max_length[50]|is_unique[tUsuarios.usuario]',
            'nombre_completo'    => 'required|max_length[150]',
            'correo_electronico' => 'required|valid_email|max_length[150]|is_unique[tUsuarios.correo_electronico]',
            'telefono'           => 'permit_empty|max_length[20]',
            'contrasena'         => 'required|min_length[6]|max_length[72]',
        ];
        $mensajes = [
            'usuario'            => ['is_unique' => 'El nombre de usuario ya está en uso. Por favor, elige otro.'],
            'correo_electronico' => ['is_unique' => 'El correo electrónico ya está registrado.'],
        ];

        if (!$this->validateData($datosForm, $reglas, $mensajes)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to(site_url('registro'))->withInput();
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $idUsuario = $usuarioModel->insert([
                'id_rol'             => 2, // Rol de Cliente
                'usuario'            => $datosForm['usuario'],
                'nombre_completo'    => $datosForm['nombre_completo'],
                'correo_electronico' => $datosForm['correo_electronico'],
                'telefono'           => $datosForm['telefono'] !== '' ? $datosForm['telefono'] : null,
                'contrasena_hash'    => password_hash($datosForm['contrasena'], PASSWORD_DEFAULT), // DEF-01
                'fecha_creacion'     => date('Y-m-d H:i:s'),
            ]);

            if (empty($idUsuario)) {
                throw new \RuntimeException('No se pudo crear el usuario.');
            }

            // DEF-06: todo cliente registrado necesita su perfil en tClientes para usar el módulo de Servicios.
            $db->table('tClientes')->insert([
                'id_usuario' => (int) $idUsuario,
                'estado'     => 'Activo',
                'deudas'     => 0.00,
            ]);

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Error al registrar usuario: ' . $e->getMessage());
            session()->setFlashdata('error', 'No fue posible completar el registro. Intenta nuevamente.');
            return redirect()->to(site_url('registro'))->withInput();
        }

        // Mensaje de éxito y redirección
        session()->setFlashdata('success', '¡Cuenta creada exitosamente! Ahora puedes iniciar sesión.');
        return redirect()->to(site_url('login'));
    }

    public function logout()
    {
        // Destruimos la sesión
        session()->destroy();
        return redirect()->to(site_url('/'));
    }
}
