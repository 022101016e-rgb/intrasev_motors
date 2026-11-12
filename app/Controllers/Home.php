<?php

namespace App\Controllers;

// Importamos los modelos
use App\Models\ProductoModel;
use App\Models\AutoModel;

class Home extends BaseController
{
    public function index()
    {
        // 1. Instanciamos las clases de los modelos
        $productoModel = new ProductoModel();
        $autoModel = new AutoModel();

        // 2. Extraemos todos los registros de ambas tablas
        // Se almacenan en variables que irán dentro del arreglo $data
        $data = [
            'titulo'    => 'INTRASEV MOTORS - Inicio',
            'productos' => $productoModel->findAll(),
            'autos'     => $autoModel->findAll()
        ];

        // 3. Enviamos los datos a la Vista (Frontend)
        return view('index', $data);
    }
}