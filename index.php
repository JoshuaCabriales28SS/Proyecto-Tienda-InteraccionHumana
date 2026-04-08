<?php
    require 'includes/app.php';

    $db = conectarDB();

    $busqueda = trim($_GET['q'] ?? '');
    $categoriaBusqueda = filter_var($_GET['categoria'] ?? null, FILTER_VALIDATE_INT);
    $condiciones = [];

    if($busqueda !== ''){
        $busquedaSegura = mysqli_real_escape_string($db, $busqueda);
        $like = '%' . $busquedaSegura . '%';
        $condiciones[] = "(nombre LIKE '$like' OR descripcion LIKE '$like')";
    }

    if($categoriaBusqueda){
        $condiciones[] = "categorias_id = $categoriaBusqueda";
    }

    $where = !empty($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '';
    $limite = !empty($condiciones) ? 24 : 12;

    $queryProductos = "SELECT * FROM productos $where ORDER BY id DESC LIMIT $limite";
    $resultadoProductos = mysqli_query($db, $queryProductos);
    $productos = $resultadoProductos ? mysqli_fetch_all($resultadoProductos, MYSQLI_ASSOC) : [];

    $queryCategorias = "SELECT * FROM categorias";
    $resultadoCategorias = mysqli_query($db, $queryCategorias);
    $categorias = $resultadoCategorias ? mysqli_fetch_all($resultadoCategorias, MYSQLI_ASSOC) : [];

    incluirTemplate('header');
?>
<main class="home" id="main-content">
    <div class="contenedor">
        <section class="market-hero">
            <div class="market-hero-content">
                <p class="hero-tag">Compra protegida</p>
                <h1>Una tienda completa, rapida y segura</h1>
                <p class="hero-text">
                    Descubre ofertas del dia, envios rapidos y productos verificados en un solo lugar.
                </p>
                <div class="hero-actions">
                    <a class="btn btn-azul btn-hero" href="/categoria.php?id=1">Ver tecnologia</a>
                    <a class="btn btn-naranja btn-ghost" href="/categoria.php?id=3">Hogar y cocina</a>
                </div>
            </div>
            <div class="market-hero-banner">
                <img src="/public/build/img/banner.jpg" alt="Promociones destacadas" loading="lazy">
                <div class="hero-banner-info">
                    <p>Ofertas relampago</p>
                    <span>Hasta 40% off</span>
                </div>
            </div>
        </section>

        <section class="benefits">
            <div class="benefit-card">
                <div class="benefit-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 4h16l-1.5 9h-13z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        <path d="M7 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM17 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" fill="currentColor" />
                    </svg>
                </div>
                <div>
                    <h3>Envio rapido</h3>
                    <p>Recibe tus productos en 24-48 horas.</p>
                </div>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3l8 4v5c0 4-3 7-8 9-5-2-8-5-8-9V7z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        <path d="M9 12l2 2 4-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <div>
                    <h3>Compra segura</h3>
                    <p>Pagos cifrados y garantia en cada orden.</p>
                </div>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 12h16M12 4v16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2" />
                    </svg>
                </div>
                <div>
                    <h3>Devoluciones faciles</h3>
                    <p>Cambios rapidos si el producto no es lo esperado.</p>
                </div>
            </div>
        </section>

        <section class="category-strip">
            <div class="section-header">
                <h2>Explora por categoria</h2>
                <a href="/index.php" class="section-link">Ver todas</a>
            </div>
            <div class="category-chips">
                <?php foreach($categorias as $categoria): ?>
                    <a class="category-chip" href="/categoria.php?id=<?php echo $categoria['id']; ?>">
                        <?php echo $categoria['nombre']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="product-section">
            <div class="section-header">
                <h2>
                    <?php if($busqueda !== ''): ?>
                        Resultados para "<?php echo htmlspecialchars($busqueda, ENT_QUOTES); ?>"
                    <?php else: ?>
                        Ofertas destacadas
                    <?php endif; ?>
                </h2>
                <a href="/index.php" class="section-link">Ver mas</a>
            </div>
            <?php if(empty($productos)): ?>
                <div class="empty-state">
                    <p>No encontramos productos con ese criterio.</p>
                    <a class="btn btn-azul" href="/index.php">Volver a inicio</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach($productos as $producto): ?>
                        <?php $metricas = obtenerMetricasProducto((int) $producto['id']); ?>
                        <article class="product-card">
                            <a href="/producto.php?id=<?php echo $producto['id']; ?>">
                                <div class="product-card-media">
                                    <img src="<?php echo obtenerImagenProducto($producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>" loading="lazy">
                                    <span class="product-badge">Envio gratis</span>
                                </div>
                                <div class="product-card-body">
                                    <p class="product-title"><?php echo $producto['nombre']; ?></p>
                                    <div class="product-rating">
                                        <div class="product-stars" aria-hidden="true">
                                            <?php echo renderStars($metricas['rating']); ?>
                                        </div>
                                        <span class="rating-text"><?php echo $metricas['rating']; ?> (<?php echo $metricas['reviews']; ?>)</span>
                                    </div>
                                    <p class="product-price">$<?php echo number_format($producto['precio'], 2, ".", ","); ?></p>
                                    <p class="product-meta">Disponible con envio rapido</p>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php
    // cerrar conexión con base de datos
    mysqli_close($db);

    incluirTemplate('footer');
?>
