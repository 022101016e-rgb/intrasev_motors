<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Solicitud de Servicio | INTRASEV MOTORS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-dark text-white">

<nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="<?= site_url('servicios') ?>" style="font-family: 'Outfit', sans-serif; font-weight: 800;">
            INTRASEV <span style="color: #e50914;">SERVICIOS</span>
        </a>
        <div class="d-flex">
            <a href="<?= site_url('servicios') ?>" class="btn btn-outline-light me-2"><i class="bi bi-arrow-left me-1"></i>Volver</a>
            <a href="<?= site_url('/') ?>" class="btn btn-outline-light"><i class="bi bi-shop me-1"></i>Tienda</a>
        </div>
    </div>
</nav>

<div class="container" style="max-width: 760px;">
    <h2 class="mb-4 text-center" style="font-family: 'Outfit', sans-serif; font-weight: 700;">Registrar Solicitud de Servicio</h2>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card bg-black border-secondary" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="<?= site_url('servicio/guardar') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label text-secondary">Cliente</label>
                    <?php if ($esAdmin): ?>
                        <select class="form-select bg-dark text-white border-secondary" name="id_cliente" id="id_cliente" required>
                            <option value="">Seleccione un cliente...</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id_cliente'] ?>" <?= ((string) old('id_cliente') === (string) $c['id_cliente'] ? 'selected' : '') ?>>
                                    <?= esc($c['id_cliente']) ?> - Estado: <?= esc($c['estado']) ?> - Deuda: S/ <?= number_format($c['deudas'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Al cambiar el cliente se cargan sus vehículos automáticamente.</small>
                    <?php else: ?>
                        <input type="hidden" name="id_cliente" id="id_cliente" value="<?= esc($clienteAsignado['id_cliente']) ?>">
                        <input type="text" class="form-control bg-dark text-white border-secondary" value="<?= esc(session()->get('nombre_completo')) ?>" disabled>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary">Vehículo</label>
                    <select class="form-select bg-dark text-white border-secondary" name="id_vehiculo" id="id_vehiculo" required>
                        <?php if (!empty($vehiculos)): ?>
                            <?php foreach ($vehiculos as $v): ?>
                                <option value="<?= $v['id_vehiculo'] ?>" <?= ((string) old('id_vehiculo') === (string) $v['id_vehiculo'] ? 'selected' : '') ?>>
                                    <?= esc($v['placa']) ?> - <?= esc($v['marca']) ?> <?= esc($v['modelo']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No hay vehículos cargados</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary">Fecha (no anterior a hoy)</label>
                        <input type="date" class="form-control bg-dark text-white border-secondary" name="fecha" id="fecha" min="<?= esc($hoy) ?>" value="<?= old('fecha') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary">Bloque Horario</label>
                        <select class="form-select bg-dark text-white border-secondary" name="id_bloque" id="id_bloque" required>
                            <option value="">Seleccione el bloque...</option>
                            <?php foreach ($bloques as $b): ?>
                                <option value="<?= $b['id_bloque'] ?>" <?= ((string) old('id_bloque') === (string) $b['id_bloque'] ? 'selected' : '') ?>>
                                    <?= esc($b['nombre']) ?> (<?= date('H:i', strtotime($b['hora_inicio'])) ?> - <?= date('H:i', strtotime($b['hora_fin'])) ?>) - Capacidad máx: <?= esc($maxCuposPorBloque) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary">Detalle del servicio</label>
                    <textarea class="form-control bg-dark text-white border-secondary" name="detalle" id="detalle" rows="4"
                        placeholder="Describe el servicio solicitado..." required><?= old('detalle') ?></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= site_url('servicios') ?>" class="btn btn-outline-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Registrar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($esAdmin): ?>
<script>
    const urlVehiculos = '<?= site_url('servicio/vehiculos/') ?>';
    const selectCliente = document.getElementById('id_cliente');
    const selectVehiculo = document.getElementById('id_vehiculo');

    function cargarVehiculos(idCliente) {
        selectVehiculo.innerHTML = '<option value="">Cargando vehículos...</option>';
        fetch(urlVehiculos + idCliente)
            .then(r => r.json())
            .then(data => {
                if (!data.length) {
                    selectVehiculo.innerHTML = '<option value="">No tiene vehículos registrados</option>';
                    return;
                }
                selectVehiculo.innerHTML = '';
                data.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.id_vehiculo;
                    opt.text = v.placa + ' - ' + v.marca + ' ' + v.modelo;
                    selectVehiculo.appendChild(opt);
                });
            })
            .catch(() => {
                selectVehiculo.innerHTML = '<option value="">Error al cargar vehículos</option>';
            });
    }

    selectCliente.addEventListener('change', function() {
        if (this.value) cargarVehiculos(this.value);
    });
</script>
<?php endif; ?>
</body>
</html>