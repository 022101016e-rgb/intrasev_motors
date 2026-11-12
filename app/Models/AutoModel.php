<?php

namespace App\Models;

use CodeIgniter\Model;

class AutoModel extends Model
{
    protected $table      = 'tAutos';
    protected $primaryKey = 'id_auto';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['modelo', 'descripcion', 'url_imagen'];
}
