<main class="carrito-page" id="main-content">
    <div class="contenedor">
        <div class="carrito-top">
            <div>
                <h1>Tu carrito</h1>
                <p class="carrito-subtexto">Selecciona, edita cantidades y elimina productos en un solo lugar.</p>
            </div>
            <div class="carrito-top-actions">
                <form method="POST" action="/carrito.php">
                    <input type="hidden" name="accion" value="seleccionar_todo">
                    <input type="hidden" name="return_to" value="/carrito.php">
                    <button class="btn btn-ghost" type="submit">Seleccionar todo</button>
                </form>
                <form method="POST" action="/carrito.php">
                    <input type="hidden" name="accion" value="limpiar_seleccion">
                    <input type="hidden" name="return_to" value="/carrito.php">
                    <button class="btn btn-ghost" type="submit">Limpiar seleccion</button>
                </form>
            </div>
        </div>

        <div class="carrito-grid">
            <section class="carrito-lista">
                <?php if(empty($carritoDetalle)): ?>
                    <div class="carrito-vacio tarjeta">
                        <h2>Tu carrito esta vacio</h2>
                        <p>Explora categorias y agrega productos para continuar.</p>
                        <a class="btn btn-azul" href="/index.php">Ir a la tienda</a>
                    </div>
                <?php else: ?>
                    <?php foreach($carritoDetalle as $producto): ?>
                        <div class="carrito-item tarjeta">
                            <form class="carrito-item-form" method="POST" action="/carrito.php">
                                <input type="hidden" name="productos_id" value="<?php echo $producto['productos_id']; ?>">
                                <input type="hidden" name="return_to" value="/carrito.php">
                                <label class="carrito-select">
                                    <input type="checkbox" name="seleccionado" value="1" <?php echo $producto['seleccionado'] ? 'checked' : ''; ?> data-cart-select>
                                    <span class="carrito-check">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M5 12l4 4L19 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </label>
                                <img class="carrito-img" src="<?php echo obtenerImagenProducto($producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>" loading="lazy">
                                <div class="carrito-info">
                                    <p class="carrito-nombre"><?php echo $producto['nombre']; ?></p>
                                    <p class="carrito-precio">$<?php echo number_format($producto['precio'], 2, '.', ','); ?></p>
                                    <div class="carrito-controles">
                                        <button type="submit" name="accion" value="decrementar" class="carrito-btn" aria-label="Restar cantidad">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M6 12h12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                            </svg>
                                        </button>
                                        <span class="carrito-cantidad"><?php echo $producto['cantidad']; ?></span>
                                        <button type="submit" name="accion" value="incrementar" class="carrito-btn" aria-label="Sumar cantidad">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M12 6v12M6 12h12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="carrito-subtotal">$<?php echo number_format($producto['precio'] * $producto['cantidad'], 2, '.', ','); ?></p>
                                <button type="submit" name="accion" value="eliminar" class="carrito-btn carrito-eliminar" aria-label="Eliminar producto">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M4 7h16M9 7V5h6v2M8 7l1 12h6l1-12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <aside class="carrito-resumen tarjeta">
                <h2>Resumen</h2>
                <div class="carrito-resumen-linea">
                    <span>Productos</span>
                    <span><?php echo $totalCantidad; ?></span>
                </div>
                <div class="carrito-resumen-linea">
                    <span>Seleccionados</span>
                    <span><?php echo $cantidadSeleccion; ?></span>
                </div>
                <div class="carrito-resumen-linea total">
                    <span>Total seleccionado</span>
                    <span>$<?php echo number_format($seleccionActiva ? $totalSeleccion : $totalCarrito, 2, '.', ','); ?></span>
                </div>
                <?php if($seleccionActiva): ?>
                    <div class="carrito-resumen-linea secundaria">
                        <span>Total general</span>
                        <span>$<?php echo number_format($totalCarrito, 2, '.', ','); ?></span>
                    </div>
                <?php endif; ?>
                <a class="btn btn-verde <?php echo $puedePagar ? '' : 'is-disabled'; ?>" href="/pagar.php" <?php echo $puedePagar ? '' : 'aria-disabled="true" tabindex="-1"'; ?>>Pagar seleccionados</a>
            </aside>
        </div>
    </div>
</main>
