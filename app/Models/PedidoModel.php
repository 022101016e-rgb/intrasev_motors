<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table      = 'tPedidos';
    protected $primaryKey = 'id_pedido';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['id_usuario', 'fecha_pedido', 'estado_pedido', 'monto_total'];
    protected $useTimestamps = false;
}
