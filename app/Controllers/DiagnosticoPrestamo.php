<?php

namespace App\Controllers;

use App\Services\PrestamoService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Prestamos;

final class DiagnosticoPrestamo extends BaseController
{
    public function evaluar(int $activos): ResponseInterface
    {
        $config  = config(Prestamos::class);
        $service = new PrestamoService($config->maxActivos);

        return $this->response->setJSON([
            'prestamos_activos' => $activos,
            'limite'            => $service->limite(),
            'puede_registrar'   => $service->puedeRegistrar($activos),
        ]);
    }
}