<?php

namespace App\Controllers;

use App\Services\PrestamoService;
use CodeIgniter\HTTP\ResponseInterface;

final class DiagnosticoPrestamo extends BaseController
{
    public function evaluar(int $activos): ResponseInterface
    {
        $service = new PrestamoService(3);

        return $this->response->setJSON([
            'prestamos_activos' => $activos,
            'limite'            => $service->limite(),
            'puede_registrar'   => $service->puedeRegistrar($activos),
        ]);
    }
}
