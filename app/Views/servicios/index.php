<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitudes de Servicio | INTRASEV MOTORS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-dark { --bs-table-bg: #161b22; }
    </style>
</head>
<body class="bg-dark text-white">

<nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="<?= site_url('servicios') ?>" style="font-family: 'Outfit', sans-serif; font-weight: 800;">
            INTRASEV <span style="color: #e50914;">SERVICIOS</span>
        </a>
        <div class="d-flex">
            <a href="<?= site_url('/') ?>" class="btn btn-outline-light me-2"><i class="bi bi-shop me-1"></i>Tienda</a>
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> <?= esc(session()->get('usuario')) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                    <?php if(session()->get('id_rol') == 1): ?>
                        <li><a class="dropdown-item" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Panel de Control</a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary me-3" title="Volver a la tienda">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0" style="font-family: 'Outfit', sans-serif; font-weight: 700;">Solicitudes de Servicio</h2>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="<?= site_url('servicio/consultar') ?>" method="GET" class="d-flex">
                <input type="text" class="form-control me-2 border-secondary" name="codigo" placeholder="Código (SOL-0001)" value="" style="max-width: 160px; background: #161b22; color: #fff;">
                <input type="text" class="form-control me-2 border-secondary" name="placa" placeholder="Placa (ABC-123)" value="" style="max-width: 160px; background: #161b22; color: #fff;">
                <button class="btn btn-outline-info" type="submit" title="Consultar por código o placa"><i class="bi bi-search"></i></button>
            </form>
            <a href="<?= site_url('servicio/nuevo') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nueva Solicitud
            </a>
        </div>
    </div>

    <?php if(session()->getFlashdata('mensaje')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('mensaje')) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card bg-black border-secondary" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="py-3 px-4">Código</th>
                            <th class="py-3">Cliente</th>
                            <th class="py-3">Vehículo</th>
                            <th class="py-3">Bloque</th>
                            <th class="py-3">Fecha</th>
                            <th class="py-3">Hora</th>
                            <th class="py-3">Estado</th>
                            <th class="py-3">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($solicitudes)): ?>
                            <tr><td colspan="8" class="text-center py-4 text-secondary">No hay solicitudes registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($solicitudes as $sol): ?>
                            <tr>
                                <td class="px-4"><span class="badge bg-primary"><?= esc($sol['codigo']) ?></span></td>
                                <td><?= esc($sol['nombre_completo']) ?></td>
                                <td><?= esc($sol['marca']) ?> <?= esc($sol['modelo']) ?> <span class="text-secondary">(<?= esc($sol['placa']) ?>)</span></td>
                                <td><?= esc($sol['bloque']) ?></td>
                                <td><?= date('d/m/Y', strtotime($sol['fecha'])) ?></td>
                                <td><?= date('H:i', strtotime($sol['hora'])) ?></td>
                                <td>
                                    <?php if($sol['estado'] === 'Pendiente'): ?>
                                        <span class="badge bg-warning text-dark"><?= esc($sol['estado']) ?></span>
                                    <?php elseif($sol['estado'] === 'Pagado' || $sol['estado'] === 'Completado'): ?>
                                        <span class="badge bg-success"><?= esc($sol['estado']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= esc($sol['estado']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($sol['detalle']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>