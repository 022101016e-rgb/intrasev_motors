<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - INTRASEV MOTORS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= base_url('estilos3.css') ?>">
    <style>
        body {
            background-color: #0d1117; /* Fondo oscuro estilo Intrasev */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }
        .login-card {
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        .brand-text {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: #ffffff;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            letter-spacing: 1px;
        }
        .brand-text span {
            color: #0d6efd;
        }
        /* Ajuste sutil a los inputs para que resalten un poco más */
        .form-control:focus {
            background-color: #1c2128 !important;
            border-color: #0d6efd !important;
            color: white !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="brand-text">INTRASEV <span>MOTORS</span></div>
    
    <div class="card login-card text-white">
        <div class="card-body p-4 p-md-5">
            <h2 class="text-center mb-4" style="font-family: 'Outfit', sans-serif; font-weight: 700;">
                Iniciar <span style="color: #0d6efd;">Sesión</span>
            </h2>

            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success border-0 rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('auth/login') ?>" method="POST">
                <div class="mb-3">
                    <label for="usuario" class="form-label text-secondary">Usuario</label>
                    <input type="text" class="form-control bg-dark text-white border-secondary" id="usuario" name="usuario" required placeholder="Ingresa tu usuario">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label text-secondary">Contraseña</label>
                    <input type="password" class="form-control bg-dark text-white border-secondary" id="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn w-100" style="background-color: #0d6efd; color: #ffffff !important; font-weight: 600; border-radius: 8px; padding: 12px; transition: 0.3s;">
                    Ingresar al Sistema
                </button>
            </form>
            
            <div class="d-flex justify-content-between mt-4 pt-3 border-top border-secondary">
                <a href="<?= site_url('/') ?>" class="text-decoration-none text-secondary hover-white">
                    <i class="bi bi-arrow-left me-1"></i> Volver a la Tienda
                </a>
                <a href="<?= site_url('registro') ?>" class="text-decoration-none" style="color: #0d6efd; font-weight: 500;">
                    Registrarse <i class="bi bi-person-plus ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
