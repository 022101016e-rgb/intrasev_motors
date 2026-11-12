<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * Endpoint de salud para pruebas de humo tras el despliegue (Guía 9, SM-01).
 * No toca la base de datos ni expone información interna.
 */
class Salud extends BaseController
{
    public function index(): ResponseInterface
    {
        return $this->response->setJSON(['estado' => 'ok']);
    }
}
