<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'tUsuarios';
    protected $primaryKey = 'id_usuario';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['id_rol', 'usuario', 'nombre_completo', 'correo_electronico', 'contrasena_hash', 'telefono', 'fecha_creacion'];
    protected $useTimestamps = false;
}
