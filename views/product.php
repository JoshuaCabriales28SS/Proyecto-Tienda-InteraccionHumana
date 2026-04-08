<main class="contenedor fondo-gris" id="main-content">
    <a class="btn btn-naranja btn-volver" href="/categoria.php?id=<?php echo $producto['categorias_id']; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 14l-4 -4l4 -4" />
            <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
        </svg>
    </a>
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="/index.php">Inicio</a>
            <span>/</span>
            <?php if($categoriaProducto): ?>
                <a href="/categoria.php?id=<?php echo $categoriaProducto['id']; ?>"><?php echo $categoriaProducto['nombre']; ?></a>
                <span>/</span>
            <?php endif; ?>
            <span><?php echo $producto['nombre']; ?></span>
        </nav>

        <?php foreach($errores as $error): ?>
            <div class="alerta error" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>
        <?php if($sinStock): ?>
            <div class="alerta error" role="alert">Producto sin stock.</div>
        <?php endif; ?>
        <?php if($agregado): ?>
            <div class="alerta exito" role="status">Producto agregado al carrito.</div>
        <?php endif; ?>

        <div class="producto">
            <div class="producto-media">
                <img src="<?php echo obtenerImagenProducto($producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>" loading="lazy">
            </div>
            <div class="producto-info">
                <p class="producto-nombre"><?php echo $producto['nombre']; ?></p>
                <div class="product-rating">
                    <div class="product-stars" aria-hidden="true">
                        <?php echo renderStars(obtenerMetricasProducto((int) $producto['id'])['rating']); ?>
                    </div>
                    <span class="rating-text"><?php echo obtenerMetricasProducto((int) $producto['id'])['rating']; ?> (<?php echo obtenerMetricasProducto((int) $producto['id'])['reviews']; ?> opiniones)</span>
                </div>
                <p class="producto-precio">$<?php echo number_format($producto['precio'], 2, '.', ','); ?></p>
                <p class="producto-stock">Stock disponible: <?php echo $producto['stock']; ?></p>
                <div class="producto-beneficios">
                    <p>Envio rapido y seguimiento en tiempo real.</p>
                    <p>Devolucion gratis dentro de 30 dias.</p>
                </div>
                <h4>Descripcion:</h4>
                <p class="producto-descripcion"><?php echo $producto['descripcion']; ?></p>
                <form action="/producto.php?id=<?php echo $producto['id']; ?>" class="producto-form" method="POST">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" name="cantidad" id="cantidad" min="1" max="<?php echo $producto['stock']; ?>" value="1" required <?php echo $sinStock ? 'disabled' : ''; ?> >
                    <button class="btn btn-comprar" type="submit" <?php echo $sinStock ? 'disabled' : ''; ?> >
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path d="M12.5 17h-6.5v-14h-2" />
                            <path d="M6 5l14 1l-.86 6.017m-2.64 .983h-10.5" />
                            <path d="M16 19h6" />
                            <path d="M19 16v6" />
                        </svg>
                        <h3>Agregar al carrito</h3>
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
