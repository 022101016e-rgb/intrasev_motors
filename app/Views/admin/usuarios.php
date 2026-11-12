<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Administradores | INTRASEV MOTORS</title>
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
        <a class="navbar-brand" href="<?= site_url('admin/dashboard') ?>" style="font-family: 'Outfit', sans-serif; font-weight: 800;">
            INTRASEV <span style="color: #e50914;">ADMIN</span>
        </a>
        <div class="d-flex">
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-badge-fill me-1"></i> Admin: <?= esc(session()->get('usuario')) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                    <li><a class="dropdown-item" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Panel de Control</a></li>
                    <li><a class="dropdown-item" href="<?= site_url('/') ?>"><i class="bi bi-shop me-2"></i>Ir a la Tienda</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-outline-secondary me-3" title="Volver al Dashboard">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0" style="font-family: 'Outfit', sans-serif; font-weight: 700;">Gestión de Administradores</h2>
        </div>
        <button class="btn btn-primary" onclick="abrirModalNuevo()">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Administrador
        </button>
    </div>

    <?php if(session()->getFlashdata('mensaje')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('mensaje') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card bg-black border-secondary" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3">Usuario (Login)</th>
                            <th class="py-3">Nombre Completo</th>
                            <th class="py-3">Correo</th>
                            <th class="py-3">Teléfono</th>
                            <th class="py-3">Creado el</th>
                            <th class="py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-secondary">No hay administradores registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usr): ?>
                            <tr>
                                <td class="px-4">#<?= $usr['id_usuario'] ?></td>
                                <td><span class="badge bg-primary px-2 py-1"><i class="bi bi-person-fill me-1"></i><?= esc($usr['usuario']) ?></span></td>
                                <td><?= esc($usr['nombre_completo']) ?></td>
                                <td><?= esc($usr['correo_electronico']) ?></td>
                                <td><?= esc($usr['telefono']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($usr['fecha_creacion'])) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info me-1" onclick='abrirModalEditar(<?= json_encode($usr) ?>)' title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <?php if($usr['id_usuario'] != session()->get('id_usuario')): ?>
                                        <a href="<?= site_url('admin/usuarios/eliminar/' . $usr['id_usuario']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar a este administrador? Perderá el acceso permanentemente.');" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="No puedes eliminarte a ti mismo"><i class="bi bi-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Formulario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="modalUsuarioLabel" style="font-family: 'Outfit', sans-serif;">Administrador</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/usuarios/guardar') ?>" method="POST" id="formUsuario">
                <div class="modal-body">
                    <input type="hidden" name="id_usuario" id="id_usuario">
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary">Nombre Completo</label>
                        <input type="text" class="form-control bg-black text-white border-secondary" name="nombre_completo" id="nombre_completo" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary">Usuario (Login)</label>
                        <input type="text" class="form-control bg-black text-white border-secondary" name="usuario" id="usuario" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary">Correo Electrónico</label>
                            <input type="email" class="form-control bg-black text-white border-secondary" name="correo_electronico" id="correo_electronico" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary">Teléfono</label>
                            <input type="text" class="form-control bg-black text-white border-secondary" name="telefono" id="telefono" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary">Contraseña</label>
                            <input type="password" class="form-control bg-black text-white border-secondary" name="contrasena_hash" id="contrasena_hash" placeholder="Escribe la contraseña">
                            <small class="text-muted" id="password-help">Para editar, déjala vacía si no quieres cambiarla.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-secondary">Confirmar Contraseña</label>
                            <input type="password" class="form-control bg-black text-white border-secondary" id="contrasena_confirmar" placeholder="Repite la contraseña">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Administrador</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));

    function abrirModalNuevo() {
        document.getElementById('modalUsuarioLabel').innerText = 'Nuevo Administrador';
        document.getElementById('id_usuario').value = '';
        document.getElementById('nombre_completo').value = '';
        document.getElementById('usuario').value = '';
        document.getElementById('correo_electronico').value = '';
        document.getElementById('telefono').value = '';
        document.getElementById('contrasena_hash').value = '';
        document.getElementById('contrasena_confirmar').value = '';
        document.getElementById('contrasena_hash').required = true;
        modalUsuario.show();
    }

    function abrirModalEditar(usr) {
        document.getElementById('modalUsuarioLabel').innerText = 'Editar Administrador';
        document.getElementById('id_usuario').value = usr.id_usuario;
        document.getElementById('nombre_completo').value = usr.nombre_completo;
        document.getElementById('usuario').value = usr.usuario;
        document.getElementById('correo_electronico').value = usr.correo_electronico;
        document.getElementById('telefono').value = usr.telefono;
        document.getElementById('contrasena_hash').value = '';
        document.getElementById('contrasena_confirmar').value = '';
        document.getElementById('contrasena_hash').required = false;
        modalUsuario.show();
    }

    // Validación de contraseñas
    document.getElementById('formUsuario').addEventListener('submit', function(e) {
        const pass1 = document.getElementById('contrasena_hash').value;
        const pass2 = document.getElementById('contrasena_confirmar').value;

        if (pass1 !== pass2) {
            e.preventDefault(); // Detiene el envío del formulario
            alert('Las contraseñas no coinciden. Por favor, verifica e inténtalo de nuevo.');
        }
    });
</script>
</body>
</html>
