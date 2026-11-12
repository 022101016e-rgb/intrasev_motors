<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CarritoComprasModel;
use App\Models\ProductoModel;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;

class CarritoController extends Controller
{
    // Método de prueba temporal — Acceder desde el navegador: /index.php/carrito/test
    public function test()
    {
        return $this->response->setJSON(['success' => true, 'message' => 'CarritoController funciona correctamente.', 'session' => session()->get('id_usuario') ?? 'Sin sesión']);
    }

    private function checkAuth()
    {
        if (!session()->has('id_usuario')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Debes iniciar sesión para comprar.']);
        }
        if (session()->get('id_rol') == 1) {
            return $this->response->setJSON(['success' => false, 'message' => 'Los administradores no pueden realizar compras.']);
        }
        return null; // OK
    }

    public function obtener()
    {
        if ($error = $this->checkAuth()) return $error;

        $db = \Config\Database::connect();
        $id_usuario = session()->get('id_usuario');

        $query = $db->table('tCarritoCompras')
                    ->select('tCarritoCompras.id_carrito, tCarritoCompras.cantidad, tProductos.id_producto, tProductos.nombre, tProductos.precio, tProductos.imagen, tProductos.stock')
                    ->join('tProductos', 'tProductos.id_producto = tCarritoCompras.id_producto')
                    ->where('tCarritoCompras.id_usuario', $id_usuario)
                    ->get();

        $items = $query->getResultArray();
        $total = 0;

        foreach ($items as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        return $this->response->setJSON([
            'success' => true,
            'items' => $items,
            'total' => $total
        ]);
    }

    public function agregar()
    {
        if ($error = $this->checkAuth()) return $error;

        $id_producto = $this->request->getPost('id_producto');
        $id_usuario = session()->get('id_usuario');

        $carritoModel = new CarritoComprasModel();
        $productoModel = new ProductoModel();

        // Validar Stock actual
        $producto = $productoModel->find($id_producto);
        if (!$producto || $producto['stock'] <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Producto sin stock disponible.']);
        }

        // Buscar si ya lo tiene en el carrito
        $itemExistente = $carritoModel->where('id_usuario', $id_usuario)
                                      ->where('id_producto', $id_producto)
                                      ->first();

        if ($itemExistente) {
            // Verificar que no supere el stock al agregar uno más
            $nuevaCantidad = $itemExistente['cantidad'] + 1;
            if ($nuevaCantidad > $producto['stock']) {
                return $this->response->setJSON(['success' => false, 'message' => 'No puedes agregar más. Solo quedan ' . $producto['stock'] . ' en stock.']);
            }

            $carritoModel->update($itemExistente['id_carrito'], ['cantidad' => $nuevaCantidad]);
        } else {
            $carritoModel->insert([
                'id_usuario' => $id_usuario,
                'id_producto' => $id_producto,
                'cantidad' => 1,
                'fecha_agregado' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Producto agregado al carrito.']);
    }

    public function actualizar()
    {
        if ($error = $this->checkAuth()) return $error;

        $id_carrito = $this->request->getPost('id_carrito');
        $accion = $this->request->getPost('accion'); // 'sumar' o 'restar'

        $carritoModel = new CarritoComprasModel();
        $productoModel = new ProductoModel();

        $item = $carritoModel->find($id_carrito);
        if (!$item || $item['id_usuario'] != session()->get('id_usuario')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item no válido.']);
        }

        $producto = $productoModel->find($item['id_producto']);
        
        if ($accion === 'sumar') {
            $nuevaCantidad = $item['cantidad'] + 1;
            if ($nuevaCantidad > $producto['stock']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Solo tenemos ' . $producto['stock'] . ' unidades disponibles de este producto.']);
            }
            $carritoModel->update($id_carrito, ['cantidad' => $nuevaCantidad]);
        } else if ($accion === 'restar') {
            $nuevaCantidad = $item['cantidad'] - 1;
            if ($nuevaCantidad <= 0) {
                $carritoModel->delete($id_carrito);
            } else {
                $carritoModel->update($id_carrito, ['cantidad' => $nuevaCantidad]);
            }
        } else if ($accion === 'fijar') {
            $nuevaCantidad = (int)$this->request->getPost('cantidad');
            if ($nuevaCantidad > $producto['stock']) {
                $nuevaCantidad = $producto['stock'];
            }
            if ($nuevaCantidad <= 0) {
                $carritoModel->delete($id_carrito);
            } else {
                $carritoModel->update($id_carrito, ['cantidad' => $nuevaCantidad]);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function eliminar()
    {
        if ($error = $this->checkAuth()) return $error;

        $id_carrito = $this->request->getPost('id_carrito');
        $carritoModel = new CarritoComprasModel();
        
        // Medida de seguridad
        $item = $carritoModel->find($id_carrito);
        if ($item && $item['id_usuario'] == session()->get('id_usuario')) {
            $carritoModel->delete($id_carrito);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function vaciar()
    {
        if ($error = $this->checkAuth()) return $error;

        $carritoModel = new CarritoComprasModel();
        $carritoModel->where('id_usuario', session()->get('id_usuario'))->delete();

        return $this->response->setJSON(['success' => true, 'message' => 'Carrito vaciado.']);
    }

    public function checkout()
    {
        if ($error = $this->checkAuth()) return redirect()->to('login');

        $db = \Config\Database::connect();
        $id_usuario = session()->get('id_usuario');

        $query = $db->table('tCarritoCompras')
                    ->select('tCarritoCompras.cantidad, tProductos.id_producto, tProductos.precio, tProductos.stock, tProductos.nombre, tProductos.imagen')
                    ->join('tProductos', 'tProductos.id_producto = tCarritoCompras.id_producto')
                    ->where('tCarritoCompras.id_usuario', $id_usuario)
                    ->get();

        $items = $query->getResultArray();

        if (empty($items)) {
            return redirect()->to('public/')->with('error', 'Tu carrito está vacío.');
        }

        $total = 0;
        foreach ($items as $item) {
            if ($item['cantidad'] > $item['stock']) {
                // Flash message for user
                return redirect()->to('public/')->with('error', 'El producto "' . $item['nombre'] . '" excedió el stock disponible. Ajusta la cantidad.');
            }
            $total += $item['precio'] * $item['cantidad'];
        }

        // Crear o actualizar pedido "Pendiente"
        $pedidoModel = new PedidoModel();
        $detalleModel = new DetallePedidoModel();
        
        $id_pedido_pendiente = session()->get('id_pedido_pendiente');
        
        if ($id_pedido_pendiente) {
            // Actualizar total
            $pedidoModel->update($id_pedido_pendiente, ['monto_total' => $total, 'estado_pedido' => 'Pendiente']);
            // Borrar detalles viejos y recrear
            $detalleModel->where('id_pedido', $id_pedido_pendiente)->delete();
        } else {
            // Crear nuevo pedido Pendiente
            $pedidoModel->insert([
                'id_usuario' => $id_usuario,
                'fecha_pedido' => date('Y-m-d H:i:s'),
                'monto_total' => $total,
                'estado_pedido' => 'Pendiente'
            ]);
            $id_pedido_pendiente = $pedidoModel->getInsertID();
            session()->set('id_pedido_pendiente', $id_pedido_pendiente);
        }

        // Insertar detalles actuales
        foreach ($items as $item) {
            $detalleModel->insert([
                'id_pedido' => $id_pedido_pendiente,
                'id_producto' => $item['id_producto'],
                'cantidad' => $item['cantidad'],
                'precio_unitario_historico' => $item['precio']
            ]);
        }

        return view('checkout', ['items' => $items, 'total' => $total]);
    }

    public function procesar_pago()
    {
        if ($error = $this->checkAuth()) return $this->response->setJSON(['success' => false, 'message' => 'Sesión expirada.']);

        $id_usuario = session()->get('id_usuario');
        $db = \Config\Database::connect();
        
        $query = $db->table('tCarritoCompras')
                    ->select('tCarritoCompras.cantidad, tProductos.id_producto, tProductos.precio, tProductos.stock, tProductos.nombre')
                    ->join('tProductos', 'tProductos.id_producto = tCarritoCompras.id_producto')
                    ->where('tCarritoCompras.id_usuario', $id_usuario)
                    ->get();

        $items = $query->getResultArray();

        if (empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tu carrito está vacío.']);
        }

        $total = 0;
        foreach ($items as $item) {
            if ($item['cantidad'] > $item['stock']) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'El producto "' . $item['nombre'] . '" ya no tiene stock (' . $item['stock'] . ' disp).'
                ]);
            }
            $total += $item['precio'] * $item['cantidad'];
        }

        // Procesar compra y pago
        $db->transStart();

        $pedidoModel = new PedidoModel();
        $id_pedido_pendiente = session()->get('id_pedido_pendiente');

        if ($id_pedido_pendiente) {
            $pedidoModel->update($id_pedido_pendiente, [
                'monto_total' => $total,
                'estado_pedido' => 'Pagado'
            ]);
            $id_pedido = $id_pedido_pendiente;
            session()->remove('id_pedido_pendiente');
        } else {
            $pedidoModel->insert([
                'id_usuario' => $id_usuario,
                'fecha_pedido' => date('Y-m-d H:i:s'),
                'monto_total' => $total,
                'estado_pedido' => 'Pagado'
            ]);
            $id_pedido = $pedidoModel->getInsertID();
        }

        $productoModel = new ProductoModel();

        foreach ($items as $item) {
            // Restar stock
            $nuevoStock = $item['stock'] - $item['cantidad'];
            $productoModel->update($item['id_producto'], ['stock' => $nuevoStock]);
        }

        // Vaciar carrito
        $carritoModel = new CarritoComprasModel();
        $carritoModel->where('id_usuario', $id_usuario)->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al procesar el pago en la base de datos.']);
        }

        return $this->response->setJSON(['success' => true, 'id_pedido' => $id_pedido]);
    }

    public function cancelar_pago()
    {
        if ($error = $this->checkAuth()) return $this->response->setJSON(['success' => false, 'message' => 'Sesión expirada.']);

        $id_usuario = session()->get('id_usuario');
        $db = \Config\Database::connect();
        
        $query = $db->table('tCarritoCompras')
                    ->select('tCarritoCompras.cantidad, tProductos.id_producto, tProductos.precio, tProductos.stock, tProductos.nombre')
                    ->join('tProductos', 'tProductos.id_producto = tCarritoCompras.id_producto')
                    ->where('tCarritoCompras.id_usuario', $id_usuario)
                    ->get();

        $items = $query->getResultArray();

        if (empty($items)) {
            return $this->response->setJSON(['success' => true]);
        }

        $total = 0;
        foreach ($items as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        $db->transStart();

        $pedidoModel = new PedidoModel();
        $id_pedido_pendiente = session()->get('id_pedido_pendiente');

        if ($id_pedido_pendiente) {
            $pedidoModel->update($id_pedido_pendiente, [
                'estado_pedido' => 'Cancelado'
            ]);
            session()->remove('id_pedido_pendiente');
        } else {
            $pedidoModel->insert([
                'id_usuario' => $id_usuario,
                'fecha_pedido' => date('Y-m-d H:i:s'),
                'monto_total' => $total,
                'estado_pedido' => 'Cancelado'
            ]);
            $id_pedido = $pedidoModel->getInsertID();
            
            $detalleModel = new DetallePedidoModel();
            foreach ($items as $item) {
                $detalleModel->insert([
                    'id_pedido' => $id_pedido,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario_historico' => $item['precio']
                ]);
            }
        }
        
        // Vaciar el carrito porque el usuario canceló el pedido
        $carritoModel = new CarritoComprasModel();
        $carritoModel->where('id_usuario', $id_usuario)->delete();

        $db->transComplete();

        return $this->response->setJSON(['success' => true]);
    }

    public function exito()
    {
        if ($error = $this->checkAuth()) return redirect()->to('login');
        return view('checkout_exito');
    }
}
