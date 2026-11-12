<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verifica si el usuario NO está logueado o si su id_rol NO es 1 (Admin)
        if (!session()->has('id_usuario') || session()->get('id_rol') != 1) {
            // Si es un cliente u otro, lo expulsamos a la tienda
            return redirect()->to(base_url('/'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No es necesario realizar acciones después de la petición para este filtro
    }
}
