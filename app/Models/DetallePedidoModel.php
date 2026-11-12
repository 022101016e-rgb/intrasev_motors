<?php

namespace App\Models;

use CodeIgniter\Model;

class DetallePedidoModel extends Model
{
    protected $table      = 'tDetallePedidos';
    protected $primaryKey = 'id_detalle';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['id_pedido', 'id_producto', 'cantidad', 'precio_unitario_historico'];
    protected $useTimestamps = false;
}
