<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Endpoint de salud (pruebas de humo del despliegue)
$routes->get('salud', 'Salud::index');

// Autenticación
$routes->get('login', 'AuthController::index');
$routes->post('auth/login', 'AuthController::login');
$routes->get('auth/logout', 'AuthController::logout');
$routes->get('registro', 'AuthController::registro');
$routes->post('auth/registrar', 'AuthController::registrar');

// Rutas protegidas para Administradores
$routes->group('admin', ['filter' => 'adminAuth'], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    
    // Rutas de Gestión de Productos
    $routes->get('productos', 'AdminController::productos');
    $routes->post('productos/guardar', 'AdminController::guardarProducto');
    $routes->post('productos/actualizar/(:num)', 'AdminController::actualizarProducto/$1');
    $routes->get('productos/eliminar/(:num)', 'AdminController::eliminarProducto/$1');

    // Rutas de Gestión de Administradores
    $routes->get('usuarios', 'AdminController::usuarios');
    $routes->post('usuarios/guardar', 'AdminController::guardarUsuario');
    $routes->get('usuarios/eliminar/(:num)', 'AdminController::eliminarUsuario/$1');
});

// Rutas del Carrito de Compras (Requieren estar logueado, preferiblemente como cliente)
$routes->group('carrito', function ($routes) {
    $routes->get('test', 'CarritoController::test');
    $routes->post('agregar', 'CarritoController::agregar');
    $routes->get('obtener', 'CarritoController::obtener');
    $routes->post('actualizar', 'CarritoController::actualizar');
    $routes->post('eliminar', 'CarritoController::eliminar');
    $routes->post('vaciar', 'CarritoController::vaciar');
    $routes->get('checkout', 'CarritoController::checkout');
    $routes->post('procesar_pago', 'CarritoController::procesar_pago');
    $routes->post('cancelar_pago', 'CarritoController::cancelar_pago');
    $routes->get('exito', 'CarritoController::exito');
});

// Rutas del Módulo de Servicios (solicitudes de servicio)
$routes->get('servicios', 'SolicitudController::index');
$routes->get('servicio/nuevo', 'SolicitudController::nuevo');
// El formulario de registro de solicitudes exige token CSRF (DEF-05)
$routes->post('servicio/guardar', 'SolicitudController::guardar', ['filter' => 'csrf']);
$routes->get('servicio/consultar', 'SolicitudController::consultar');
$routes->get('servicio/vehiculos/(:num)', 'SolicitudController::vehiculosDeCliente/$1');
