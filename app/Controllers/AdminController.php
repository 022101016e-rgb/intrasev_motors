<?php

namespace App\Controllers;

use App\Models\ProductoModel;
use CodeIgniter\Controller;

class AdminController extends BaseController
{
    public function dashboard()
    {
        return view('admin_dashboard');
    }

    public function productos()
    {
        $productoModel = new ProductoModel();
        $data = [
            'productos' => $productoModel->findAll()
        ];
        
        return view('admin/productos', $data);
    }

    public function guardarProducto()
    {
        $productoModel = new ProductoModel();
        
        $id_producto = $this->request->getPost('id_producto');
        
        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'descripcion'   => $this->request->getPost('descripcion'),
            'precio'        => $this->request->getPost('precio'),
            'stock'         => $this->request->getPost('stock'),
            'estado_activo' => $this->request->getPost('estado_activo') ?? 1
        ];

        // Manejo de la subida del archivo físico
        $file = $this->request->getFile('imagen_archivo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            // Guarda el archivo en public/uploads/productos/
            $file->move(FCPATH . 'uploads/productos', $newName);
            // La ruta que se guardará en la base de datos
            $data['imagen'] = 'uploads/productos/' . $newName;
        } else {
            // Si no suben archivo nuevo (por ejemplo al editar), mantener la imagen antigua
            $data['imagen'] = $this->request->getPost('imagen_actual');
        }

        if ($id_producto) {
            // Actualizar
            $productoModel->update($id_producto, $data);
            session()->setFlashdata('mensaje', 'Producto actualizado correctamente.');
        } else {
            // Crear
            $productoModel->insert($data);
            session()->setFlashdata('mensaje', 'Producto creado correctamente.');
        }

        return redirect()->to(site_url('admin/productos'));
    }

    public function eliminarProducto($id)
    {
        $productoModel = new ProductoModel();
        $productoModel->delete($id);
        session()->setFlashdata('mensaje', 'Producto eliminado correctamente.');
        return redirect()->to(site_url('admin/productos'));
    }

    // ==========================================
    // OPERACIÓN UPDATE DE PRODUCTO (Actividad 3)
    // ==========================================

    public function actualizarProducto($id)
    {
        $productoModel = new ProductoModel();

        // 1) VERIFICAR QUE EL REGISTRO EXISTA
        $producto = $productoModel->find($id);
        if (!$producto) {
            session()->setFlashdata('error', 'El producto #' . $id . ' no existe o ya fue eliminado.');
            return redirect()->to(site_url('admin/productos'));
        }

        // 2) VALIDAR LOS NUEVOS DATOS (reglas de CodeIgniter)
        $rules = [
            'nombre'      => 'required|min_length[3]|max_length[150]',
            'descripcion' => 'required|max_length[2000]',
            'precio'      => 'required|numeric|greater_than_equal_to[0]',
            'stock'       => 'required|integer|greater_than_equal_to[0]',
        ];

        // 3) APLICAR REGLAS CUANDO CORRESPONDA
        if (!$this->validate($rules)) {
            $errores = $this->validator->getErrors();
            session()->setFlashdata('error', 'Datos inválidos. Revisa: ' . implode(', ', array_keys($errores)));
            return redirect()->to(site_url('admin/productos'));
        }

        // 4) ACTUALIZAR ÚNICAMENTE LOS CAMPOS PERMITIDOS
        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'descripcion'   => $this->request->getPost('descripcion'),
            'precio'        => $this->request->getPost('precio'),
            'stock'         => (int) $this->request->getPost('stock'),
            'estado_activo' => $this->request->getPost('estado_activo') ?? 1,
        ];
        // Nota: el Model (ProductoModel) define $allowedFields,
        // por lo que CI4 descarta cualquier campo no permitido.

        // Manejo de imagen: solo se actualiza si se sube un archivo nuevo
        $file = $this->request->getFile('imagen_archivo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/productos', $newName);
            $data['imagen'] = 'uploads/productos/' . $newName;
        } elseif ($this->request->getPost('imagen_actual')) {
            $data['imagen'] = $this->request->getPost('imagen_actual');
        }

        // Ejecutar la actualización
        $actualizado = $productoModel->update($id, $data);

        // 5) INFORMAR EL RESULTADO
        if ($actualizado) {
            session()->setFlashdata('mensaje', 'Producto #' . $id . ' actualizado correctamente.');
        } else {
            session()->setFlashdata('error', 'No se pudo actualizar el producto #' . $id . '.');
        }

        return redirect()->to(site_url('admin/productos'));
    }

    // ==========================================
    // GESTIÓN DE ADMINISTRADORES
    // ==========================================

    public function usuarios()
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        // Solo mostramos usuarios con rol de administrador (id_rol = 1)
        $data = [
            'usuarios' => $usuarioModel->where('id_rol', 1)->findAll()
        ];
        
        return view('admin/usuarios', $data);
    }

    public function guardarUsuario()
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        
        $id_usuario = $this->request->getPost('id_usuario');
        
        $data = [
            'id_rol'             => 1, // Forzado a Administrador
            'usuario'            => $this->request->getPost('usuario'),
            'nombre_completo'    => $this->request->getPost('nombre_completo'),
            'correo_electronico' => $this->request->getPost('correo_electronico'),
            'telefono'           => $this->request->getPost('telefono')
        ];

        // Solo actualizar contraseña si se envió una nueva
        $password = $this->request->getPost('contrasena_hash');
        if (!empty($password)) {
            // DEF-01: se almacena el hash, nunca la contraseña en texto plano
            $data['contrasena_hash'] = password_hash((string) $password, PASSWORD_DEFAULT);
        }

        if ($id_usuario) {
            // Actualizar
            $usuarioModel->update($id_usuario, $data);
            session()->setFlashdata('mensaje', 'Administrador actualizado correctamente.');
        } else {
            // Crear: Asignar fecha actual
            $data['fecha_creacion'] = date('Y-m-d H:i:s');
            $usuarioModel->insert($data);
            session()->setFlashdata('mensaje', 'Administrador creado correctamente.');
        }

        return redirect()->to(site_url('admin/usuarios'));
    }

    public function eliminarUsuario($id)
    {
        // Evitar que el administrador en sesión se elimine a sí mismo
        if ($id == session()->get('id_usuario')) {
            session()->setFlashdata('error', 'No puedes eliminar tu propia cuenta mientras estás en sesión.');
            return redirect()->to(site_url('admin/usuarios'));
        }

        $usuarioModel = new \App\Models\UsuarioModel();
        $usuarioModel->delete($id);
        session()->setFlashdata('mensaje', 'Administrador eliminado correctamente.');
        return redirect()->to(site_url('admin/usuarios'));
    }
}
