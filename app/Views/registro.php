<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Intrasev Motors</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f1115;
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .register-container {
            width: 100%;
            max-width: 500px;
        }
        .brand-text {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }
        .brand-text span {
            color: #facc15; /* Amarillo de la tienda */
        }
        .card.register-card {
            background-color: #1a1d24;
            border: 1px solid #2a2e37;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .form-control {
            background-color: #0f1115 !important;
            border: 1px solid #334155 !important;
            color: #fff !important;
            padding: 12px;
            border-radius: 8px;
        }
        .form-control:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }
        .btn-register {
            background-color: #0d6efd;
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s;
            border: none;
        }
        .btn-register:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            color: #ffffff;
        }
        .hover-white:hover {
            color: #fff !important;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="brand-text">INTRASEV <span>MOTORS</span></div>
    
    <div class="card register-card text-white">
        <div class="card-body p-4 p-md-5">
            <h2 class="text-center mb-4" style="font-weight: 700;">
                Crear <span style="color: #0d6efd;">Cuenta</span>
            </h2>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 rounded-3 small p-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('auth/registrar') ?>" method="POST" id="registroForm">
                <div class="mb-3">
                    <label class="form-label text-secondary small mb-1">Nombre Completo</label>
                    <input type="text" class="form-control" name="nombre_completo" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-secondary small mb-1">Usuario (Login)</label>
                    <input type="text" class="form-control" name="usuario" required>
                </div>
                
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label text-secondary small mb-1">Correo Electrónico</label>
                        <input type="email" class="form-control" name="correo_electronico" required>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label text-secondary small mb-1">Teléfono</label>
                        <input type="text" class="form-control" name="telefono">
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 mb-4">
                        <label class="form-label text-secondary small mb-1">Contraseña</label>
                        <input type="password" class="form-control" name="contrasena" id="contrasena" required placeholder="Escribe la contraseña">
                    </div>
                    <div class="col-sm-6 mb-4">
                        <label class="form-label text-secondary small mb-1">Confirmar Contraseña</label>
                        <input type="password" class="form-control" name="confirmar_contrasena" id="confirmar_contrasena" required placeholder="Repite la contraseña">
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top border-secondary pt-3 mt-2">
                    <a href="<?= site_url('login') ?>" class="btn btn-outline-light me-2" style="border-radius: 8px;">Cancelar</a>
                    <button type="submit" class="btn btn-register">
                        Registrarse
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4 pt-3 border-top border-secondary">
                <span class="text-secondary">¿Ya tienes una cuenta?</span>
                <a href="<?= site_url('login') ?>" class="text-decoration-none hover-white ms-1" style="color: #0d6efd; font-weight: 500;">
                    Inicia Sesión
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('registroForm').addEventListener('submit', function(e) {
        var p1 = document.getElementById('contrasena').value;
        var p2 = document.getElementById('confirmar_contrasena').value;
        if (p1 !== p2) {
            e.preventDefault();
            alert('Las contraseñas no coinciden. Por favor, verifica.');
        }
    });
</script>
</body>
</html>
