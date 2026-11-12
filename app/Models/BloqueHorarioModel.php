<?php

namespace App\Models;

use CodeIgniter\Model;

class BloqueHorarioModel extends Model
{
    protected $table      = 'tBloquesHorarios';
    protected $primaryKey = 'id_bloque';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['nombre', 'hora_inicio', 'hora_fin', 'capacidad'];
    protected $useTimestamps = false;
}