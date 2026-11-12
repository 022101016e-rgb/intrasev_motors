<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Pago Seguro</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f1115;
            color: #e2e8f0;
        }
        .checkout-container {
            max-width: 1100px;
            margin: 40px auto;
        }
        .card-panel {
            background-color: #1a1d24;
            border: 1px solid #2a2e37;
            border-radius: 12px;
            padding: 30px;
        }
        .summary-panel {
            background: linear-gradient(145deg, #1e222a, #16191f);
            border: 1px solid #2a2e37;
            border-radius: 12px;
            padding: 30px;
            position: sticky;
            top: 20px;
        }
        .nav-pills .nav-link {
            color: #94a3b8;
            border: 1px solid transparent;
        }
        .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
            color: #000;
            background-color: #facc15;
            font-weight: 600;
        }
        .form-control {
            background-color: #0f1115;
            border: 1px solid #334155;
            color: #fff;
        }
        .form-control:focus {
            background-color: #0f1115;
            color: #fff;
            border-color: #facc15;
            box-shadow: 0 0 0 0.25rem rgba(250, 204, 21, 0.25);
        }
        .btn-pay {
            background-color: #facc15;
            color: #000;
            font-weight: 700;
            border: none;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-pay:hover {
            background-color: #eab308;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(250, 204, 21, 0.3);
        }
        .item-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #2a2e37;
        }
        .qr-placeholder {
            width: 200px;
            height: 200px;
            background-color: #fff;
            margin: 0 auto;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="container checkout-container">
    <div class="mb-4">
        <a href="<?= base_url('index.php') ?>" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Volver a la tienda
        </a>
    </div>

    <div class="row g-5">
        <!-- Columna Izquierda: Formulario de Pago -->
        <div class="col-lg-7">
            <h2 class="mb-4 fw-bold">Pago Seguro</h2>
            
            <div class="card-panel">
                <h5 class="mb-3">Método de pago</h5>
                
                <ul class="nav nav-pills nav-fill mb-4" id="paymentTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="card-tab" data-bs-toggle="tab" data-bs-target="#card" type="button" role="tab"><i class="bi bi-credit-card me-2"></i>Tarjeta</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="yape-tab" data-bs-toggle="tab" data-bs-target="#yape" type="button" role="tab" style="background-color: #740f4b; color: white; margin-left: 10px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= base_url('imagenes/yape_logo.png') ?>" alt="Yape" height="20" class="me-2" onerror="this.style.display='none'">Yape
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="plin-tab" data-bs-toggle="tab" data-bs-target="#plin" type="button" role="tab" style="background-color: #00d3ff; color: #000; margin-left: 10px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= base_url('imagenes/plin_logo.png') ?>" alt="Plin" height="20" class="me-2" onerror="this.style.display='none'">Plin
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="paymentTabContent">
                    <!-- Tarjeta -->
                    <div class="tab-pane fade show active" id="card" role="tabpanel">
                        <form id="formTarjeta">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Número de Tarjeta</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-credit-card"></i></span>
                                    <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required id="cc-number">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Nombre en la tarjeta</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small">Vencimiento</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" maxlength="5" required id="cc-exp">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small">CVV</label>
                                    <input type="password" class="form-control" maxlength="4" required>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Yape -->
                    <div class="tab-pane fade text-center" id="yape" role="tabpanel">
                        <div class="p-3">
                            <h5 class="text-white mb-4">Escanea el QR para pagar con <img src="<?= base_url('imagenes/yape_logo.png') ?>" alt="Yape" height="24" onerror="this.style.display='none'"> Yape</h5>
                            <div class="qr-placeholder mb-3">
                                <!-- Generador de QR falso para diseño -->
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=IntrasevMotorsYape" alt="QR Yape" class="img-fluid">
                            </div>
                            <p class="text-muted small">Número: <strong>987 654 321</strong><br>Titular: Intrasev Motors</p>
                        </div>
                    </div>

                    <!-- Plin -->
                    <div class="tab-pane fade text-center" id="plin" role="tabpanel">
                        <div class="p-3">
                            <h5 class="text-white mb-4">Escanea el QR para pagar con <img src="<?= base_url('imagenes/plin_logo.png') ?>" alt="Plin" height="24" onerror="this.style.display='none'"> Plin</h5>
                            <div class="qr-placeholder mb-3">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=IntrasevMotorsPlin" alt="QR Plin" class="img-fluid">
                            </div>
                            <p class="text-muted small">Número: <strong>987 654 321</strong><br>Titular: Intrasev Motors</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-pay w-100 fs-5 mb-3" id="btn-procesar" onclick="procesarPago()">
                        <i class="bi bi-lock-fill me-2"></i> Pagar S/ <?= number_format($total, 2) ?>
                    </button>
                    <p class="text-center text-muted small mt-3 mb-0"><i class="bi bi-shield-check text-success"></i> Pago encriptado y seguro.</p>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Resumen -->
        <div class="col-lg-5">
            <div class="summary-panel">
                <h4 class="mb-4">Resumen de la orden</h4>
                
                <div class="items-container mb-4" style="max-height: 400px; overflow-y: auto;">
                    <?php foreach($items as $item): 
                        $baseImgUrl = base_url(); // Ajuste para URL correcta
                        $imgUrl = (strpos($item['imagen'], 'http') === 0) ? $item['imagen'] : rtrim(str_replace('index.php', '', base_url()), '/') . '/' . $item['imagen'];
                    ?>
                    <div class="item-row">
                        <img src="<?= $imgUrl ?>" alt="<?= esc($item['nombre']) ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" class="me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-white"><?= esc($item['nombre']) ?></h6>
                            <span class="text-muted small">Cant: <?= $item['cantidad'] ?></span>
                        </div>
                        <div class="fw-bold">
                            S/ <?= number_format($item['precio'] * $item['cantidad'], 2) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-between mb-2 text-muted">
                    <span>Subtotal</span>
                    <span>S/ <?= number_format($total, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-muted">
                    <span>Envío</span>
                    <span class="text-success">Gratis</span>
                </div>
                <hr class="border-secondary">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="fs-5 fw-bold text-white">Total</span>
                    <span class="fs-3 fw-bold text-warning">S/ <?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const SITE_URL = '<?= base_url('index.php') ?>';

    // Simple auto-formateo para la tarjeta
    document.getElementById('cc-number').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '').substring(0,16);
        value = value != '' ? value.match(/.{1,4}/g).join(' ') : '';
        e.target.value = value;
    });

    document.getElementById('cc-exp').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '').substring(0,4);
        if (value.length >= 2) {
            value = value.substring(0,2) + '/' + value.substring(2,4);
        }
        e.target.value = value;
    });

    function procesarPago() {
        // Validacion basica si es tarjeta
        const isCard = document.getElementById('card-tab').classList.contains('active');
        if (isCard) {
            const ccNumber = document.getElementById('cc-number').value.replace(/\s/g, '');
            if (ccNumber.length < 15) {
                alert("Por favor, ingresa un número de tarjeta válido.");
                return;
            }
            if (document.getElementById('cc-exp').value.length < 5) {
                alert("Por favor, ingresa una fecha de vencimiento válida.");
                return;
            }
        }

        // Mostrar alerta de confirmación / cancelación
        if (confirm("¿Confirmar el pago de tu pedido?")) {
            // Flujo de pago
            const btn = document.getElementById('btn-procesar');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando pago de forma segura...';

            fetch(SITE_URL + '/carrito/procesar_pago', {
                method: 'POST'
            })
            .then(response => response.text())
            .then(text => {
                let data;
                try { data = JSON.parse(text); } catch(e) {
                    alert('Error del servidor.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Pagar S/ <?= number_format($total, 2) ?>';
                    return;
                }

                if (data.success) {
                    window.location.href = SITE_URL + '/carrito/exito';
                } else {
                    alert(data.message || 'Error al procesar el pago.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Pagar S/ <?= number_format($total, 2) ?>';
                }
            })
            .catch(error => {
                alert('Error de conexión.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Pagar S/ <?= number_format($total, 2) ?>';
            });
        } else {
            // Flujo de cancelación
            const btn = document.getElementById('btn-procesar');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cancelando compra...';

            fetch(SITE_URL + '/carrito/cancelar_pago', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                window.location.href = SITE_URL;
            })
            .catch(error => {
                alert("Error al cancelar");
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Pagar S/ <?= number_format($total, 2) ?>';
            });
        }
    }
</script>
</body>
</html>
