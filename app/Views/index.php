<?= $this->include('header') ?>

<main>
    <!-- ═══════════════════════════════════════════
         HERO — Texto de bienvenida
         ═══════════════════════════════════════════ -->
    <section class="hero-text-section" id="inicio">
        <div class="hero-content">
            <span class="hero-badge">PERFORMANCE & TUNING</span>
            <h1 class="hero-titulo">INTRASEV <span>MOTORS</span></h1>
            <p class="hero-slogan">"No pares hasta ser leyenda"</p>
            <p class="hero-descripcion">Donde el asfalto se rinde ante la potencia. Repuestos de alto rendimiento, tuning profesional y componentes de élite para máquinas que desafían lo establecido.</p>
            <div class="hero-acciones">
                <a href="#repuestos" class="btn-hero-principal"><i class="bi bi-bag-fill me-2"></i>VER CATÁLOGO</a>
                <a href="#conocenos" class="btn-hero-secundario"><i class="bi bi-info-circle me-2"></i>CONOCER MÁS</a>
            </div>
            <div class="hero-redes">
                <a href="#" class="hero-red facebook" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="hero-red whatsapp" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                <a href="#" class="hero-red instagram" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="hero-red youtube" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         CARRUSEL PROMOCIONAL — Imágenes limpias
         ═══════════════════════════════════════════ -->
    <section class="carrusel-promocional">
        <div id="carouselHero" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="3" aria-label="Slide 4"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="<?= base_url('imagenes/publicidad.jpeg') ?>" class="d-block w-100" alt="Publicidad Intrasev Motors - Repuestos de alto rendimiento">
                </div>
                <div class="carousel-item">
                    <img src="<?= base_url('imagenes/publicidad2.jpeg') ?>" class="d-block w-100" alt="Publicidad Intrasev Motors - Tuning profesional">
                </div>
                <div class="carousel-item">
                    <img src="<?= base_url('imagenes/publicidad3.jpeg') ?>" class="d-block w-100" alt="Publicidad Intrasev Motors - Performance extremo">
                </div>
                <div class="carousel-item">
                    <img src="<?= base_url('imagenes/publicidad4.jpeg') ?>" class="d-block w-100" alt="Publicidad Intrasev Motors - Componentes de élite">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselHero" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselHero" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         BARRA DE BENEFICIOS
         ═══════════════════════════════════════════ -->
    <section class="barra-beneficios">
        <div class="beneficio">
            <i class="bi bi-truck"></i>
            <div>
                <strong>Envío a todo el Perú</strong>
                <span>Despacho rápido y seguro</span>
            </div>
        </div>
        <div class="beneficio">
            <i class="bi bi-shield-check"></i>
            <div>
                <strong>Garantía en cada pieza</strong>
                <span>Componentes certificados</span>
            </div>
        </div>
        <div class="beneficio">
            <i class="bi bi-wrench-adjustable"></i>
            <div>
                <strong>Instalación profesional</strong>
                <span>Servicio técnico especializado</span>
            </div>
        </div>
        <div class="beneficio">
            <i class="bi bi-headset"></i>
            <div>
                <strong>Asesoría personalizada</strong>
                <span>Te guiamos en tu proyecto</span>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         CATÁLOGO DE PRODUCTOS
         ═══════════════════════════════════════════ -->
    <section class="seccion-productos" id="repuestos">
        <div class="encabezado-tienda">
            <h2>PRODUCTOS <span>High Performance</span></h2>
            <p>El mejor hardware para tu máquina — Componentes seleccionados para quienes exigen el máximo rendimiento</p>
        </div>

        <style>
            .producto-agotado {
                opacity: 0.5;
                filter: grayscale(80%);
                pointer-events: none; /* Evita hover y clics en toda la tarjeta si se desea, aunque lo controlaremos en el botón */
            }
            .producto-agotado .tag-disponible {
                background-color: #6c757d !important;
                color: #fff !important;
            }
            .btn-admin-hover:hover {
                background: #6c757d !important; /* Gris sólido, sobreescribe el gradient */
                color: #ffffff !important;
                transform: none !important;
                box-shadow: none !important;
            }
        </style>
        <div class="grilla-productos">
            <?php foreach ($productos as $producto): ?>
                <?php 
                    $agotado = ($producto['stock'] <= 0);
                    $claseTarjeta = $agotado ? 'tarjeta-producto producto-agotado' : 'tarjeta-producto';
                ?>
                <div class="<?= $claseTarjeta ?>">
                    <div class="imagen-producto">
                        <?php if ($agotado): ?>
                            <span class="tag-disponible" style="background: rgba(0,0,0,0.8);"><i class="bi bi-x-circle-fill me-1"></i>Agotado</span>
                        <?php else: ?>
                            <span class="tag-disponible"><i class="bi bi-check-circle-fill me-1"></i>Disponible</span>
                        <?php endif; ?>
                        <img src="<?= base_url(!empty($producto['imagen']) ? esc($producto['imagen']) : 'imagenes/repuesto1.jpeg') ?>" alt="<?= esc($producto['nombre']) ?>">
                    </div>
                    <div class="info-producto">
                        <h3 class="titulo-producto"><?= $producto['nombre'] ?></h3>
                        <p class="descripcion-producto"><?= $producto['descripcion'] ?></p>
                        <div class="base-producto">
                            <span class="precio-producto">S/ <?= number_format($producto['precio'], 2) ?></span>
                            
                            <?php if ($agotado): ?>
                                <button class="btn-comprar btn-secondary" disabled>
                                    <i class="bi bi-slash-circle me-1"></i> Sin Stock
                                </button>
                            <?php elseif (!session()->has('id_usuario')): ?>
                                <button class="btn-comprar" onclick="window.location.href='<?= site_url('login') ?>'">
                                    <i class="bi bi-cart-plus me-1"></i> Comprar
                                </button>
                            <?php elseif (session()->get('id_rol') == 1): ?>
                                <button class="btn-comprar btn-admin-hover" onclick="return false;">
                                    <i class="bi bi-cart-plus me-1"></i> Comprar
                                </button>
                            <?php else: ?>
                                <button class="btn-comprar" onclick="agregarAlCarrito(<?= $producto['id_producto'] ?>, '<?= addslashes($producto['nombre']) ?>', <?= $producto['precio'] ?>, '<?= addslashes(!empty($producto['imagen']) ? base_url($producto['imagen']) : base_url('imagenes/repuesto1.jpeg')) ?>', <?= $producto['stock'] ?>)">
                                    <i class="bi bi-cart-plus me-1"></i> Comprar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         NUESTRA MARCA — Identidad Intrasev
         ═══════════════════════════════════════════ -->
    <section class="seccion-marca" id="marca">
        <div class="contenido-marca">
            <div class="texto-marca">
                <span class="marca-badge"><i class="bi bi-lightning-charge-fill me-1"></i>NUESTRA FILOSOFÍA</span>
                <h2>Donde el asfalto se rinde <span>ante la potencia</span></h2>
                <p>En Intrasev Motor, no solo vendemos piezas; construimos máquinas que desafían lo establecido. Nacimos en el corazón de las pistas y nos criamos entre el rugido de los motores y el olor a neumático quemado. Nuestra esencia es la competición y nuestra obsesión es el tuning de alto nivel.</p>
                <p><strong>Repuestos de Alto Rendimiento:</strong> Solo componentes de élite diseñados para soportar el castigo de la pista y maximizar cada caballo de fuerza. Trabajamos con las marcas más reconocidas del mundo del motorsport y seleccionamos cada pieza con la precisión de un ingeniero de Fórmula 1. Desde sistemas de escape de alto flujo hasta kits de embrague reforzado, cada componente que ofrecemos ha sido probado bajo condiciones extremas para garantizar que tu motor entregue su máximo potencial sin compromisos.</p>
                <p><strong>Reprogramaciones Extremas:</strong> Optimizamos la electrónica de tu motor con configuraciones personalizadas. Nuestro equipo de especialistas en ECU tuning analiza cada parámetro de tu unidad de control electrónico para extraer hasta el último caballo de fuerza oculto. Ya sea que necesites una reprogramación Stage 1 para uso diario con ese extra de potencia, o un Stage 3 completo para competición, en Intrasev Motor convertimos datos en velocidad pura.</p>
            </div>
            <div class="imagenes-marca">
                <div class="img-marca-wrapper">
                    <img src="<?= base_url('imagenes/repuesto7.jpeg') ?>" alt="Repuesto de alto rendimiento Intrasev Motors">
                </div>
                <div class="img-marca-wrapper">
                    <img src="<?= base_url('imagenes/repuesto2.jpeg') ?>" alt="Componente de competición Intrasev Motors">
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         SHOWROOM DE AUTOS
         ═══════════════════════════════════════════ -->
    <section class="seccion-autos" id="autos">
        <div class="encabezado-autos">
            <h2>Showroom <span>de Proyectos</span></h2>
            <p>Máquinas que hemos llevado al límite — Cada proyecto cuenta nuestra historia y demuestra nuestra pasión por el rendimiento extremo</p>
        </div>
        <div class="grilla-autos">
            <div class="tarjeta-auto">
                <div class="imagen-auto">
                    <span class="badge-proyecto"><i class="bi bi-trophy-fill me-1"></i>PROYECTO</span>
                    <img src="https://i.pinimg.com/736x/3b/ce/02/3bce029787465ec39fd67afe1e63f31b.jpg" alt="Honda Civic 1998 modificado por Intrasev Motors">
                </div>
                <div class="info-auto">
                    <h3 class="titulo-auto">Honda Civic 1998</h3>
                    <p class="texto-historia-auto">El proyecto que nos enseñó a exprimir cada caballo de fuerza. Tomamos este ícono japonés y nos aseguramos de que su legendario motor VTEC cantara hasta el corte de inyección. Con un peso pluma y modificaciones precisas en la admisión y escape, este Civic dejó de ser un auto de calle para convertirse en un misil de asfalto. Pura escuela JDM. Este proyecto marcó el inicio de nuestra filosofía: respetar la esencia del auto mientras se lleva su rendimiento a territorios inexplorados. Le instalamos un sistema de escape completo en acero inoxidable, cabezote trabajado con levas de competición y una ECU reprogramada que le dio vida a cada revolución.</p>
                    <a href="#" class="btn-consultar-auto"><i class="bi bi-chat-dots me-1"></i>Consultar proyecto</a>
                </div>
            </div>

            <div class="tarjeta-auto">
                <div class="imagen-auto">
                    <span class="badge-proyecto"><i class="bi bi-trophy-fill me-1"></i>PROYECTO</span>
                    <img src="https://i.pinimg.com/736x/d6/d2/71/d6d2717f4431d0ee2f6f0613deb7ec11.jpg" alt="VW Golf MK3 modificado por Intrasev Motors">
                </div>
                <div class="info-auto">
                    <h3 class="titulo-auto">VW Golf MK3</h3>
                    <p class="texto-historia-auto">Actitud europea pura. A este MK3 no solo le dimos una estética agresiva con una postura pegada al piso, sino que le inyectamos vida nueva con una <strong>reprogramación Stage1</strong>. Logramos el equilibrio perfecto entre un daily drive con estilo y un hatch que responde con contundencia cuando pisas el acelerador a fondo. Un clásico que nunca pasa de moda. El trabajo incluyó suspensión coilover regulable, rines de aleación liviana y un sistema de frenos mejorado con discos ventilados y pastillas de competición que le dan la confianza necesaria para atacar cualquier curva sin pensarlo dos veces.</p>
                    <a href="#" class="btn-consultar-auto"><i class="bi bi-chat-dots me-1"></i>Consultar proyecto</a>
                </div>
            </div>

            <div class="tarjeta-auto">
                <div class="imagen-auto">
                    <span class="badge-proyecto"><i class="bi bi-trophy-fill me-1"></i>PROYECTO</span>
                    <img src="https://i.pinimg.com/736x/30/b1/23/30b12378829723bef299e80fe6431c33.jpg" alt="Subaru Impreza 98 modificado por Intrasev Motors">
                </div>
                <div class="info-auto">
                    <h3 class="titulo-auto">Subaru Impreza 98</h3>
                    <p class="texto-historia-auto">El monstruo del rally llevado a la calle. Trabajar en su sistema de tracción integral (AWD) y escuchar ese motor bóxer rugir después de ajustarle los fierros es otra experiencia. Reforzamos su estructura, mejoramos el soplado del turbo y lo convertimos en una máquina capaz de tomar curvas a velocidades que asustan. Una verdadera leyenda de los 90s. Le montamos un intercooler frontal de mayor capacidad, un downpipe de 3 pulgadas y una wastegate externa que le da ese sonido característico de rally que eriza la piel. La transmisión fue reforzada con un embrague de competición de disco cerámico para soportar el torque brutal que entrega este bóxer turboalimentado.</p>
                    <a href="#" class="btn-consultar-auto"><i class="bi bi-chat-dots me-1"></i>Consultar proyecto</a>
                </div>
            </div>
        </div> 
    </section>

    <!-- ═══════════════════════════════════════════
         NUESTRA HISTORIA
         ═══════════════════════════════════════════ -->
    <section class="seccion-conocenos" id="conocenos">
        <div class="encabezado-conocenos">
            <h2>Nuestra <span>Historia</span></h2>
            <p>Más que un negocio, una pasión que se vive en cada proyecto</p>
        </div>

        <!-- Stats visuales -->
        <div class="stats-row">
            <div class="stat-item">
                <h3>3+</h3>
                <p>Años en el mercado</p>
            </div>
            <div class="stat-item">
                <h3>500+</h3>
                <p>Clientes satisfechos</p>
            </div>
            <div class="stat-item">
                <h3>1000+</h3>
                <p>Repuestos vendidos</p>
            </div>
            <div class="stat-item">
                <h3>50+</h3>
                <p>Proyectos completados</p>
            </div>
        </div>

        <div class="contenedor-historia">
            <div class="texto-historia">
                <p><strong>Intrasev Motor</strong> no es solo un catálogo de piezas, es el legado de la velocidad. Fundada por <span class="resaltado-azul">Richard Tirveñso</span>, llevamos <strong>3 años en el mercado</strong> demostrando que los límites de fábrica son solo una sugerencia para quienes no se conforman.</p> 
                <p>No hablamos por hablar; nuestros proyectos en el taller hablan por nosotros. Nos hemos ensuciado las manos llevando máquinas al límite: desde exprimir al máximo un <strong>VW Gol con una reprogramación Stage 1</strong> que rompe esquemas, hasta domar la tracción integral de <strong>varios Subarus</strong> preparados para devorar la pista y la calle. </p>
                 <p>Esa misma obsesión por el control, el performance y la precisión es lo que nos llevó a integrar productos de nivel competitivo, como nuestro aclamado <strong>CLM-2000</strong>. Sabemos que la conexión entre el piloto y la máquina es sagrada, y cada tuerca que apretamos, cada línea de código que modificamos en una ECU y cada repuesto que vendemos tiene que cumplir con esa filosofía: SI SE ROMPE SE ARREGLA.</p>
                 <p>Nuestro compromiso va más allá de la venta. Cada cliente que cruza nuestras puertas se convierte en parte de la familia Intrasev, una comunidad de apasionados por la velocidad que comparten la misma visión: que un auto no es solo un medio de transporte, sino una extensión de quien lo conduce. Por eso ofrecemos asesoría personalizada, instalación profesional y seguimiento post-venta, porque tu máquina merece el mismo cuidado y dedicación que le ponemos a las nuestras.</p>
            </div>
            <div class="visual-historia">
                <div class="imagen-taller">
                    <img src="<?= base_url('imagenes/RR.jpeg') ?>" alt="Taller de Intrasev Motors - Donde nacen las leyendas">
                </div>
            </div>
        </div>
    </section>
</main>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
    <div id="toastCompra" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header" style="background-color: #ffd000; color: #000;">
            <i class="bi bi-cart-check-fill me-2"></i>
            <strong class="me-auto">Intrasev Motors</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
        <div class="toast-body">
            <span id="mensajeToast"></span>
        </div>
    </div>
</div>

<?= $this->include('footer') ?>