<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="INTRASEV MOTORS — Repuestos de alto rendimiento, tuning y autos modificados. Componentes de élite para máquinas que desafían lo establecido.">
    <title>INTRASEV MOTORS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?= base_url('estilos3.css') ?>">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
            <div class="container">
                <a class="navbar-brand" href="#">INTRASEV <span>MOTORS</span></a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Abrir menú de navegación">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="#inicio">Inicio</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">| Productos</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#repuestos">Repuestos</a></li>
                                <li><a class="dropdown-item" href="#autos">Autos</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#conocenos">| Conocenos</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('servicios') ?>">| Servicios</a></li>
                    </ul>

                    <div class="d-flex align-items-center">
                        <form class="d-flex me-3" role="search">
                            <input href="#repuestos" class="form-control search-input" type="search" placeholder="Buscar productos..." aria-label="Buscar productos">
                            <button class="btn btn-search" type="submit" aria-label="Buscar"><i class="bi bi-search"></i></button>
                        </form>

                        <style>
                            /* Ocultar las horribles flechas del input de cantidad */
                            input[type="number"]::-webkit-inner-spin-button, 
                            input[type="number"]::-webkit-outer-spin-button { 
                                -webkit-appearance: none; 
                                margin: 0; 
                            }
                            input[type="number"] {
                                -moz-appearance: textfield; /* Para Firefox */
                            }
                        </style>

                        <?php if(session()->has('id_usuario')): ?>
                            <?php if(session()->get('id_rol') == 1): ?>
                                <button class="btn me-3 admin-cart" type="button" aria-label="Carrito deshabilitado para administradores" onclick="return false;" style="background-color: #1a1d24; border-color: #334155; color: #94a3b8; cursor: default; transition: all 0.3s; padding: 8px 16px; border-radius: 8px;">
                                    <i class="bi bi-cart-x" style="font-size: 1.2rem;"></i>
                                </button>
                                <style>
                                    .admin-cart:hover {
                                        background-color: #334155 !important;
                                        color: #cbd5e1 !important;
                                        box-shadow: 0 0 15px rgba(255, 255, 255, 0.3) !important;
                                    }
                                </style>
                            <?php else: ?>
                                <button class="btn btn-cart-trigger me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-label="Abrir carrito de compras">
                                    <i class="bi bi-cart3"></i>
                                    <span id="cart-count" class="badge-cart">0</span>
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= base_url('index.php/login') ?>" class="btn btn-cart-trigger me-3" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" aria-label="Iniciar sesión para comprar">
                                <i class="bi bi-cart3"></i>
                            </a>
                        <?php endif; ?>

                        <?php if(session()->has('id_usuario')): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px;">
                                    <i class="bi bi-person-circle me-1"></i> <?= esc(session()->get('usuario')) ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                                    <?php if(session()->get('id_rol') == 1): ?>
                                        <li><a class="dropdown-item" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Panel de Control</a></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="<?= site_url('login') ?>" class="btn btn-login-header">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasCartLabel"><i class="bi bi-bag-check-fill me-2"></i>ORDEN DE PEDIDO</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar carrito"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <div id="cart-items-container" class="flex-grow-1">
                <div class="carrito-vacio">
                    <i class="bi bi-cart-x"></i>
                    <p>No hay productos seleccionados</p>
                    <span>¡Explora nuestro catálogo y añade productos!</span>
                </div>
            </div>
            
            <div id="cart-footer" class="cart-footer" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="cart-footer-label">TOTAL:</span>
                    <span id="cart-total" class="cart-footer-total">S/ 0.00</span>
                </div>
                <button class="btn btn-outline-danger w-100 mb-2" onclick="vaciarCarrito()" style="border-radius: 8px; font-size: 13px;"><i class="bi bi-trash3 me-2"></i>VACIAR CARRITO</button>
                <button class="btn btn-checkout" aria-label="Finalizar compra" onclick="procesarCompra()"><i class="bi bi-credit-card me-2"></i>FINALIZAR COMPRA</button>
            </div>
        </div>
    </div>