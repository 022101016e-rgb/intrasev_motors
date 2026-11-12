<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pago Exitoso!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f1115;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .success-card {
            background-color: #1a1d24;
            border: 1px solid #2a2e37;
            border-radius: 16px;
            padding: 50px 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .icon-circle {
            width: 100px;
            height: 100px;
            background-color: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            margin: 0 auto 30px auto;
            animation: scaleIn 0.5s ease-out forwards;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        .btn-home {
            background-color: #facc15;
            color: #000;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: all 0.2s;
        }
        .btn-home:hover {
            background-color: #eab308;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="icon-circle">
        <i class="bi bi-check-lg"></i>
    </div>
    <h2 class="fw-bold mb-3">¡Pago Exitoso!</h2>
    <p class="text-muted mb-4">
        Gracias por tu compra, <strong><?= esc(session()->get('usuario')) ?></strong>. 
        Hemos recibido tu pedido correctamente y el pago ha sido aprobado.
    </p>
    
    <div class="bg-dark p-3 rounded-3 mb-4 text-start border border-secondary">
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Estado del Pedido:</span>
            <span class="text-success fw-bold"><i class="bi bi-circle-fill small me-1"></i> Pagado</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Método:</span>
            <span>Seguro / Encriptado</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-muted">Fecha:</span>
            <span><?= date('d/m/Y H:i') ?></span>
        </div>
    </div>

    <a href="<?= base_url('index.php') ?>" class="btn-home">
        <i class="bi bi-bag-check me-2"></i> Seguir Comprando
    </a>
</div>

</body>
</html>
