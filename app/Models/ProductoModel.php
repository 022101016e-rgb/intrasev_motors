<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table      = 'tProductos';
    protected $primaryKey = 'id_producto';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['nombre', 'descripcion', 'precio', 'imagen', 'stock', 'estado_activo'];
}
