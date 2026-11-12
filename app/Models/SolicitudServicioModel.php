<?php

namespace App\Models;

use CodeIgniter\Model;

class SolicitudServicioModel extends Model
{
    protected $table      = 'tSolicitudesServicio';
    protected $primaryKey = 'id_solicitud';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = [
        'codigo', 'id_cliente', 'id_vehiculo', 'id_bloque',
        'fecha', 'hora', 'estado', 'detalle', 'fecha_registro'
    ];
    protected $useTimestamps = false;
}