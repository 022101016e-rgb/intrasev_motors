<?php

namespace App\Models;

use CodeIgniter\Model;

class VehiculoModel extends Model
{
    protected $table      = 'tVehiculos';
    protected $primaryKey = 'id_vehiculo';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['id_cliente', 'placa', 'marca', 'modelo', 'anio'];
    protected $useTimestamps = false;
}