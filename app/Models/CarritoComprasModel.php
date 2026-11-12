<?php

namespace App\Models;

use CodeIgniter\Model;

class CarritoComprasModel extends Model
{
    protected $table      = 'tCarritoCompras';
    protected $primaryKey = 'id_carrito';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['id_usuario', 'id_producto', 'cantidad', 'fecha_agregado'];
    protected $useTimestamps = false;
}
