<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table      = 'tClientes';
    protected $primaryKey = 'id_cliente';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['id_usuario', 'estado', 'deudas'];
    protected $useTimestamps = false;
}