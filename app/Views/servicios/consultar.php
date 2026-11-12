<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consultar Solicitud | INTRASEV MOTORS</title>
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
            <a href="<?= site_url('servicios') ?>" class="btn btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Volver</a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="font-family: 'Outfit', sans-serif; font-weight: 700;">Consultar Solicitud</h2>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-md-5">
            <form action="<?= site_url('servicio/consultar') ?>" method="GET" class="d-flex">
                <input type="text" class="form-control me-2 border-secondary" name="codigo" placeholder="Código correlativo (SOL-0001)" value="<?= esc($codigo) ?>" style="background: #161b22; color: #fff;">
                <button class="btn btn-outline-info flex-shrink-0" type="submit"><i class="bi bi-search me-1"></i>Buscar por código</button>
            </form>
        </div>
        <div class="col-md-5">
            <form action="<?= site_url('servicio/consultar') ?>" method="GET" class="d-flex">
                <input type="text" class="form-control me-2 border-secondary" name="placa" placeholder="Placa (ABC-123)" value="<?= esc($placa) ?>" style="background: #161b22; color: #fff;">
                <button class="btn btn-outline-info flex-shrink-0" type="submit"><i class="bi bi-search me-1"></i>Buscar por placa</button>
            </form>
        </div>
    </div>

    <?php if ($criterio !== ''): ?>
        <?php if (empty($resultados)): ?>
            <div class="alert alert-warning border-0 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>No se encontraron solicitudes para <strong><?= esc($criterio) ?></strong>.
            </div>
        <?php else: ?>
            <div class="alert alert-info border-0 rounded-3" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i><?= count($resultados) ?> solicitud(es) encontrada(s) para <strong><?= esc($criterio) ?></strong>.
            </div>

            <div class="card bg-black border-secondary" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4">Código</th>
                                    <th class="py-3">Cliente</th>
                                    <th class="py-3">Correo</th>
                                    <th class="py-3">Vehículo (Placa)</th>
                                    <th class="py-3">Bloque</th>
                                    <th class="py-3">Fecha</th>
                                    <th class="py-3">Hora</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3">Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultados as $sol): ?>
                                <tr>
                                    <td class="px-4"><span class="badge bg-primary"><?= esc($sol['codigo']) ?></span></td>
                                    <td><?= esc($sol['nombre_completo']) ?></td>
                                    <td><?= esc($sol['correo_electronico']) ?></td>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-5 text-secondary">
            <i class="bi bi-search" style="font-size: 3rem;"></i>
            <p class="mt-3 mb-0">Ingresa un <strong>código correlativo</strong> o una <strong>placa</strong> para buscar solicitudes.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>