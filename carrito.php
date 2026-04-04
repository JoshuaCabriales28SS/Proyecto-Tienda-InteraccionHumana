<?php
    require 'includes/app.php';
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    $db = conectarDB();
    function obtenerIdsCarrito(mysqli $db): array{
        $ids = [];
        $resultado = mysqli_query($db, "SELECT productos_id FROM carrito");
        if($resultado){
            while($fila = mysqli_fetch_assoc($resultado)){
                $ids[] = (int) $fila['productos_id'];
            }
        }
        return $ids;
    }
    function sanitizarReturnTo(string $returnTo): string{
        $returnTo = trim($returnTo);
        if($returnTo === '' || substr($returnTo, 0, 1) !== '/' || substr($returnTo, 0, 2) === '//'){
            return '/carrito.php';
        }
        return $returnTo;
    }
    $accion = $_POST['accion'] ?? null;
    $productoId = filter_var($_POST['productos_id'] ?? null, FILTER_VALIDATE_INT);
    $returnTo = sanitizarReturnTo($_POST['return_to'] ?? '/carrito.php');

    if($_SERVER['REQUEST_METHOD'] === 'POST' || $accion){
        if($accion === 'eliminar' && $productoId){
            $query = "DELETE FROM carrito WHERE productos_id = $productoId";
            mysqli_query($db, $query);

            if(isset($_SESSION['carrito_seleccion']) && is_array($_SESSION['carrito_seleccion'])){
                $_SESSION['carrito_seleccion'] = array_values(array_diff($_SESSION['carrito_seleccion'], [$productoId]));
            }
        }
        if(($accion === 'incrementar' || $accion === 'decrementar') && $productoId){
            $query = "SELECT carrito.id, carrito.cantidad, productos.stock
                      FROM carrito
                      JOIN productos ON carrito.productos_id = productos.id
                      WHERE carrito.productos_id = $productoId
                      LIMIT 1";
            $resultado = mysqli_query($db, $query);
            $item = $resultado ? mysqli_fetch_assoc($resultado) : null;

            if($item){
                $cantidadActual = (int) $item['cantidad'];
                $stock = (int) $item['stock'];

                if($accion === 'incrementar' && $cantidadActual < $stock){
                    $nuevaCantidad = $cantidadActual + 1;
                    $query = "UPDATE carrito SET cantidad = $nuevaCantidad WHERE id = {$item['id']}";
                    mysqli_query($db, $query);
                }
                if($accion === 'decrementar'){
                    if($cantidadActual > 1){
                        $nuevaCantidad = $cantidadActual - 1;
                        mysqli_query($db, "UPDATE carrito SET cantidad = $nuevaCantidad WHERE id = {$item['id']}");
                    }else{
                        mysqli_query($db, "DELETE FROM carrito WHERE id = {$item['id']}");
                        if(isset($_SESSION['carrito_seleccion']) && is_array($_SESSION['carrito_seleccion'])){
                            $_SESSION['carrito_seleccion'] = array_values(array_diff($_SESSION['carrito_seleccion'], [$productoId]));
                        }
                    }
                }
            }
        }
        if($accion === 'seleccionar' && $productoId){
            $seleccion = $_SESSION['carrito_seleccion'] ?? null;
            $seleccionado = isset($_POST['seleccionado']);

            if(!is_array($seleccion)){ $seleccion = obtenerIdsCarrito($db); }
            $seleccion = array_map('intval', $seleccion);
            if($seleccionado && !in_array($productoId, $seleccion, true)){ $seleccion[] = $productoId; }
            if(!$seleccionado){ $seleccion = array_values(array_diff($seleccion, [$productoId])); }
            $_SESSION['carrito_seleccion'] = $seleccion;
        }

        if($accion === 'seleccionar_todo'){ $_SESSION['carrito_seleccion'] = obtenerIdsCarrito($db); }
        if($accion === 'limpiar_seleccion'){ $_SESSION['carrito_seleccion'] = []; }
        $idsActuales = obtenerIdsCarrito($db);
        if(empty($idsActuales)){ unset($_SESSION['carrito_seleccion']); }
        header('Location: ' . $returnTo);
        exit;
    }

    $query = "SELECT carrito.id, carrito.cantidad, productos.id AS productos_id, productos.nombre,
                     productos.precio, productos.imagen
              FROM carrito
              JOIN productos ON carrito.productos_id = productos.id";
    $resultado = mysqli_query($db, $query);
    $carrito = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
    $seleccionActiva = array_key_exists('carrito_seleccion', $_SESSION) && is_array($_SESSION['carrito_seleccion']);
    $seleccion = $seleccionActiva ? array_map('intval', $_SESSION['carrito_seleccion']) : [];
    $carritoDetalle = [];
    $totalCarrito = 0;
    $totalCantidad = 0;
    $totalSeleccion = 0;
    $cantidadSeleccion = 0;

    foreach($carrito as $producto){
        $totalCarrito += (float) $producto['precio'] * (int) $producto['cantidad'];
        $totalCantidad += (int) $producto['cantidad'];
        $seleccionado = !$seleccionActiva || in_array((int) $producto['productos_id'], $seleccion, true);
        $producto['seleccionado'] = $seleccionado;
        if($seleccionado){
            $totalSeleccion += (float) $producto['precio'] * (int) $producto['cantidad'];
            $cantidadSeleccion += (int) $producto['cantidad'];
        }
        $carritoDetalle[] = $producto;
    }
    if(empty($carrito) && $seleccionActiva){
        unset($_SESSION['carrito_seleccion']);
        $seleccionActiva = false;
        $seleccion = [];
    }
    $puedePagar = $seleccionActiva ? $cantidadSeleccion > 0 : !empty($carrito);

    incluirTemplate('header');
?>

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
                                    <p class="carrito-precio">$<?php echo number_format($producto['precio'], 2, ".", ","); ?></p>
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
                                <p class="carrito-subtotal">$<?php echo number_format($producto['precio'] * $producto['cantidad'], 2, ".", ","); ?></p>
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
                    <span>$<?php echo number_format($seleccionActiva ? $totalSeleccion : $totalCarrito, 2, ".", ","); ?></span>
                </div>
                <?php if($seleccionActiva): ?>
                    <div class="carrito-resumen-linea secundaria">
                        <span>Total general</span>
                        <span>$<?php echo number_format($totalCarrito, 2, ".", ","); ?></span>
                    </div>
                <?php endif; ?>
                <a class="btn btn-verde <?php echo $puedePagar ? '' : 'is-disabled'; ?>" href="/pagar.php" <?php echo $puedePagar ? '' : 'aria-disabled="true" tabindex="-1"'; ?>>Pagar seleccionados</a>
            </aside>
        </div>
    </div>
</main>

<?php
    incluirTemplate('footer');
?>
