<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administración | INTRASEV MOTORS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="bg-dark text-white">

<nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="#" style="font-family: 'Outfit', sans-serif; font-weight: 800;">
            INTRASEV <span style="color: #e50914;">ADMIN</span>
        </a>
        <div class="d-flex">
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-badge-fill me-1"></i> Admin: <?= esc(session()->get('usuario')) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                    <li><a class="dropdown-item" href="<?= site_url('/') ?>"><i class="bi bi-shop me-2"></i>Ir a la Tienda</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 style="font-family: 'Outfit', sans-serif; font-weight: 700;">Bienvenido al Panel de Control</h1>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card bg-black border-secondary text-center h-100 py-4" style="border-radius: 12px;">
                <div class="card-body">
                    <i class="bi bi-box-seam" style="font-size: 3rem; color: #0d6efd;"></i>
                    <h4 class="mt-3">Productos</h4>
                    <p class="text-secondary">Gestiona tu inventario.</p>
                    <a href="<?= site_url('admin/productos') ?>" class="btn btn-outline-light mt-2">Gestionar Productos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card bg-black border-secondary text-center h-100 py-4" style="border-radius: 12px;">
                <div class="card-body">
                    <i class="bi bi-people" style="font-size: 3rem; color: #0d6efd;"></i>
                    <h4 class="mt-3">Usuarios</h4>
                    <p class="text-secondary">Administra los accesos.</p>
                    <a href="<?= site_url('admin/usuarios') ?>" class="btn btn-outline-light mt-2">Gestionar Administradores</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
