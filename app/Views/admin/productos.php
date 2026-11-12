<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Productos | INTRASEV MOTORS</title>
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
            <h2 class="mb-0" style="font-family: 'Outfit', sans-serif; font-weight: 700;">Gestión de Productos</h2>
        </div>
        <button class="btn btn-primary" onclick="abrirModalNuevo()">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
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
                            <th class="py-3">Imagen</th>
                            <th class="py-3">Nombre</th>
                            <th class="py-3">Precio (S/)</th>
                            <th class="py-3">Stock</th>
                            <th class="py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-secondary">No hay productos registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td class="px-4">#<?= $producto['id_producto'] ?></td>
                                <td>
                                    <img src="<?= base_url(!empty($producto['imagen']) ? esc($producto['imagen']) : 'imagenes/repuesto1.jpeg') ?>" alt="<?= esc($producto['nombre']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                </td>
                                <td><?= esc($producto['nombre']) ?></td>
                                <td><?= number_format($producto['precio'], 2) ?></td>
                                <td>
                                    <?php if($producto['stock'] > 0): ?>
                                        <span class="badge bg-success"><?= $producto['stock'] ?> en stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Agotado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info me-1" onclick='abrirModalEditar(<?= json_encode($producto) ?>)' title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <a href="<?= site_url('admin/productos/eliminar/' . $producto['id_producto']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar este producto?');" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </a>
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
<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="modalProductoLabel" style="font-family: 'Outfit', sans-serif;">Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/productos/guardar') ?>" method="POST" enctype="multipart/form-data" id="formProducto">
                <div class="modal-body">
                    <input type="hidden" name="id_producto" id="id_producto">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label text-secondary">Nombre del Producto</label>
                            <input type="text" class="form-control bg-black text-white border-secondary" name="nombre" id="nombre" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-secondary">Precio (S/)</label>
                            <input type="number" step="0.01" class="form-control bg-black text-white border-secondary" name="precio" id="precio" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary">Descripción</label>
                        <textarea class="form-control bg-black text-white border-secondary" name="descripcion" id="descripcion" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label text-secondary">Imagen del Producto</label>
                            <input type="file" class="form-control bg-black text-white border-secondary" name="imagen_archivo" id="imagen_archivo" accept="image/*">
                            <small class="text-muted" id="img-help">Sube un archivo de imagen (JPG, PNG). Para editar, déjalo vacío si no quieres cambiar la imagen actual.</small>
                            <input type="hidden" name="imagen_actual" id="imagen_actual">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-secondary">Stock</label>
                            <input type="number" class="form-control bg-black text-white border-secondary" name="stock" id="stock" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));

    function abrirModalNuevo() {
        document.getElementById('modalProductoLabel').innerText = 'Nuevo Producto';
        document.getElementById('formProducto').action = '<?= site_url('admin/productos/guardar') ?>';
        document.getElementById('id_producto').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('precio').value = '';
        document.getElementById('descripcion').value = '';
        document.getElementById('imagen_archivo').value = '';
        document.getElementById('imagen_archivo').required = true;
        document.getElementById('imagen_actual').value = '';
        document.getElementById('stock').value = '';
        modalProducto.show();
    }

    function abrirModalEditar(producto) {
        document.getElementById('modalProductoLabel').innerText = 'Editar Producto';
        document.getElementById('formProducto').action = '<?= site_url('admin/productos/actualizar/') ?>' + producto.id_producto;
        document.getElementById('id_producto').value = producto.id_producto;
        document.getElementById('nombre').value = producto.nombre;
        document.getElementById('precio').value = producto.precio;
        document.getElementById('descripcion').value = producto.descripcion;
        document.getElementById('imagen_archivo').value = '';
        document.getElementById('imagen_archivo').required = false;
        document.getElementById('imagen_actual').value = producto.imagen;
        document.getElementById('stock').value = producto.stock;
        modalProducto.show();
    }
</script>
</body>
</html>
