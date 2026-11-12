document.addEventListener('DOMContentLoaded', () => {
    actualizarVistaCarrito();
});

function mostrarToast(titulo, mensaje, tipo = 'success') {
    const iconos = {
        'success': 'bi-cart-check-fill',
        'danger': 'bi-exclamation-triangle-fill',
        'warning': 'bi-exclamation-circle-fill'
    };
    const icono = iconos[tipo] || iconos['success'];
    
    const toastHtml = `
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
          <div class="toast cart-toast text-bg-${tipo}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-${tipo} text-white border-0">
              <i class="bi ${icono} me-2"></i>
              <strong class="me-auto">${titulo}</strong>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-dark text-white">
              ${mensaje}
            </div>
          </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', toastHtml);
    const toastElements = document.querySelectorAll('.cart-toast');
    const lastToast = toastElements[toastElements.length - 1];
    const toast = new bootstrap.Toast(lastToast);
    toast.show();
    lastToast.addEventListener('hidden.bs.toast', () => lastToast.parentElement.remove());
}

function agregarAlCarrito(id_producto, nombre, precio, imagen, stock) {
    if (stock <= 0) {
        mostrarToast('Agotado', 'Este producto está agotado y no puede ser añadido.', 'danger');
        return;
    }

    const formData = new FormData();
    formData.append('id_producto', id_producto);

    fetch(SITE_URL + '/carrito/agregar', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Error del servidor: ' + response.status);
        return response.text();
    })
    .then(text => {
        let data;
        try { data = JSON.parse(text); } catch(e) {
            console.error('Respuesta no es JSON:', text);
            mostrarToast('Error', 'Error del servidor. Revisa la consola.', 'danger');
            return;
        }

        if (data.success) {
            actualizarVistaCarrito();
            mostrarToast('Agregado al Carrito', `${nombre} se añadió correctamente.`, 'success');
        } else {
            mostrarToast('Aviso', data.message || 'No se pudo agregar al carrito.', 'warning');
        }
    })
    .catch(error => {
        console.error('Error de red:', error);
        mostrarToast('Error', 'Error de conexión con el servidor.', 'danger');
    });
}

function actualizarVistaCarrito() {
    fetch(SITE_URL + '/carrito/obtener')
    .then(response => {
        if (!response.ok) return null;
        return response.text();
    })
    .then(text => {
        if (!text) return;
        let data;
        try {
            data = JSON.parse(text);
        } catch(e) {
            return; // Silencioso si no hay JSON válido
        }

        if (data.success) {
            renderizarCarrito(data.items, data.total);
        } else {
            // Usuario no logueado o admin
            const contenedor = document.getElementById('cart-items-container');
            const badge = document.getElementById('cart-count');
            if (contenedor) contenedor.innerHTML = '<p class="text-secondary text-center my-4">Inicia sesión para usar el carrito.</p>';
            if (badge) badge.innerText = '0';
        }
    })
    .catch(() => {}); // Silencioso
}

function renderizarCarrito(items, total) {
    const contenedor = document.getElementById('cart-items-container');
    const badge = document.getElementById('cart-count');
    const contenedorTotal = document.getElementById('cart-total');
    const footer = document.getElementById('cart-footer');

    contenedor.innerHTML = '';
    let totalCantidad = 0;

    if (items.length === 0) {
        contenedor.innerHTML = '<p class="text-secondary text-center my-4"><i class="bi bi-cart-x" style="font-size: 2rem;"></i><br>Tu carrito está vacío.</p>';
        badge.innerText = '0';
        badge.style.display = 'none';
        contenedorTotal.innerText = 'S/ 0.00';
        if (footer) footer.style.display = 'none';
        return;
    }

    if (footer) footer.style.display = 'block';

    // Construir la URL base para las imágenes locales
    const baseImgUrl = SITE_URL.replace('/index.php', '');

    items.forEach(item => {
        totalCantidad += parseInt(item.cantidad);
        const imgUrl = (item.imagen && item.imagen.startsWith('http')) ? item.imagen : baseImgUrl + '/' + item.imagen;
        
        const isMaxStock = parseInt(item.cantidad) >= parseInt(item.stock);
        
        const html = `
            <div class="cart-item d-flex align-items-center mb-3 p-2 border-bottom border-secondary">
                <img src="${imgUrl}" alt="${item.nombre}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;" class="me-3">
                <div class="flex-grow-1">
                    <h6 class="mb-1" style="font-size: 0.9rem;">${item.nombre}</h6>
                    <span class="text-danger" style="font-size: 0.9rem; font-weight: 600;">S/ ${parseFloat(item.precio).toFixed(2)}</span>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <div class="btn-group btn-group-sm mb-1 d-flex align-items-center" role="group">
                        <button type="button" class="btn btn-outline-light px-2 py-0" onclick="actualizarCantidad(${item.id_carrito}, 'restar')">-</button>
                        <input type="number" class="form-control form-control-sm text-center mx-1" 
                               value="${item.cantidad}" 
                               min="1" max="${item.stock}" 
                               style="width: 50px; background: #212529; color: white; border: 1px solid #495057; padding: 2px;"
                               onchange="cambiarCantidadManual(${item.id_carrito}, this.value, ${item.stock})"
                               onfocus="this.select()">
                        <button type="button" class="btn ${isMaxStock ? 'btn-secondary text-muted' : 'btn-outline-light'} px-2 py-0" 
                                onclick="actualizarCantidad(${item.id_carrito}, 'sumar')" ${isMaxStock ? 'disabled' : ''}>+</button>
                    </div>
                    <button class="btn btn-link text-secondary p-0 mt-1" style="font-size: 0.8rem;" onclick="eliminarDelCarrito(${item.id_carrito})"><i class="bi bi-trash"></i> Eliminar</button>
                </div>
            </div>
        `;
        contenedor.insertAdjacentHTML('beforeend', html);
    });

    badge.innerText = totalCantidad;
    badge.style.display = 'flex';
    badge.classList.remove('badge-bounce');
    void badge.offsetWidth; // Forzar reflow para reiniciar animación
    badge.classList.add('badge-bounce');
    contenedorTotal.innerText = 'S/ ' + parseFloat(total).toFixed(2);
}

function actualizarCantidad(id_carrito, accion) {
    const formData = new FormData();
    formData.append('id_carrito', id_carrito);
    formData.append('accion', accion);

    fetch(SITE_URL + '/carrito/actualizar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try { data = JSON.parse(text); } catch(e) { return; }
        if (data.success) {
            actualizarVistaCarrito();
        } else {
            mostrarToast('Aviso de Stock', data.message || 'Límite alcanzado.', 'warning');
        }
    });
}

function cambiarCantidadManual(id_carrito, cantidad, stock) {
    let cant = parseInt(cantidad);
    
    // Validaciones
    if (isNaN(cant) || cant <= 0) {
        actualizarVistaCarrito(); // Revertir si el input es inválido
        return;
    }
    
    if (cant > stock) {
        cant = stock;
        mostrarToast('Stock máximo', `Solo hay ${stock} unidades disponibles de este producto.`, 'warning');
    }

    const formData = new FormData();
    formData.append('id_carrito', id_carrito);
    formData.append('cantidad', cant); // Nuevo endpoint o lógica requerida, pero adaptemos el actual:
    formData.append('accion', 'fijar'); // Vamos a necesitar agregar 'fijar' al controlador

    fetch(SITE_URL + '/carrito/actualizar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try { data = JSON.parse(text); } catch(e) { return; }
        if (data.success) {
            actualizarVistaCarrito();
        } else {
            mostrarToast('Error', data.message || 'Error al actualizar.', 'danger');
            actualizarVistaCarrito();
        }
    });
}

function eliminarDelCarrito(id_carrito) {
    const formData = new FormData();
    formData.append('id_carrito', id_carrito);

    fetch(SITE_URL + '/carrito/eliminar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try { data = JSON.parse(text); } catch(e) { return; }
        if (data.success) {
            actualizarVistaCarrito();
        }
    });
}

function vaciarCarrito() {
    if (!confirm('¿Estás seguro de vaciar todo el carrito?')) return;

    fetch(SITE_URL + '/carrito/vaciar', {
        method: 'POST'
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try { data = JSON.parse(text); } catch(e) { return; }
        if (data.success) {
            actualizarVistaCarrito();
        }
    });
}

function procesarCompra() {
    const btn = event.target.closest('.btn-checkout');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Preparando...';
    
    // Redirigir a la página de checkout
    window.location.href = SITE_URL + '/carrito/checkout';
}





