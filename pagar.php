<?php
    require './includes/app.php';
    if(session_status() === PHP_SESSION_NONE){ session_start(); }
    $db = conectarDB();
    $seleccionActiva = array_key_exists('carrito_seleccion', $_SESSION) && is_array($_SESSION['carrito_seleccion']);
    $seleccion = $seleccionActiva ? array_map('intval', $_SESSION['carrito_seleccion']) : [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if($seleccionActiva){
            if(empty($seleccion)){
                header('Location: /carrito.php');
                exit;
            }
            $ids = implode(',', $seleccion);
            $query = "DELETE FROM carrito WHERE productos_id IN ($ids)";
            $resultado = mysqli_query($db, $query);

            if(isset($_SESSION['carrito_seleccion']) && is_array($_SESSION['carrito_seleccion'])){ $_SESSION['carrito_seleccion'] = array_values(array_diff($_SESSION['carrito_seleccion'], $seleccion)); }
        }else{
            $query = "DELETE FROM carrito";
            $resultado = mysqli_query($db, $query);
        }
        if($resultado){
            header('Location: /index.php');
            exit;
        }
    }
    if($seleccionActiva && empty($seleccion)){ $productosCarrito = []; }else{
        $query = "SELECT carrito.id, carrito.cantidad, productos.nombre, productos.precio, productos.imagen
                  FROM carrito
                  JOIN productos ON carrito.productos_id = productos.id";
        if($seleccionActiva){
            $ids = implode(',', $seleccion);
            $query .= " WHERE carrito.productos_id IN ($ids)";
        }
        $resultado = mysqli_query($db, $query);
        $productosCarrito = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
    }
    $cantidadSeleccion = 0;
    foreach($productosCarrito as $producto){ $cantidadSeleccion += (int) $producto['cantidad']; }
    $total = 0;
    incluirTemplate('header');
?>

    <main class="pago contenedor" id="main-content">
        <div class="contenedor">
            <div class="pago-contenedor">
                <h1>Forma de pago</h1>
                <?php if(!empty($productosCarrito)): ?>
                    <p class="pago-nota">
                        <?php if($seleccionActiva): ?>
                            Pagaras solo los productos seleccionados (<?php echo $cantidadSeleccion; ?>).
                        <?php else: ?>
                            Estas pagando todos los productos del carrito (<?php echo $cantidadSeleccion; ?>).
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <div class="productos_pago">
                    <?php if(empty($productosCarrito)): ?>
                        <div class="carrito-vacio">
                            <p>No hay productos seleccionados para pagar.</p>
                            <a class="btn btn-azul" href="/carrito.php">Ir al carrito</a>
                        </div>
                    <?php else: ?>
                        <?php foreach($productosCarrito as $producto): ?>
                            <div class="producto_pago">
                                <img class="producto_pago-img" src="<?php echo obtenerImagenProducto($producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>" loading="lazy">
                                <p class="nombre-producto_pago">
                                    <?php echo $producto['nombre']; ?>
                                </p>
                                <p>
                                    <?php echo "$".number_format($producto['precio'], 2, ".", ",") ." x ". $producto['cantidad']; ?>
                                </p>
                                <p>
                                <?php echo "$".number_format($subtotal = $producto['precio'] * $producto['cantidad'], 2, ".", ","); ?>
                                    <?php $total += $subtotal; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <hr>
                <h2 class="total-text">
                    Total: <?php echo "$".number_format($total, 2, ".", ","); ?>
                </h2>
            </div>
        </div>
        <div class="contenedor">
            <form method="POST" class="form-pago" action="/pagar.php">
                <fieldset>
                    <div class="campo">
                        <label for="tarjeta">Numero de tarjeta:</label>
                        <input type="text" id="tarjeta" name="tarjeta"  inputmode="numeric" autocomplete="cc-number" required maxlength="19" placeholder="1234 5678 1234 5678">
                    </div>
                    <div class="campo">
                        <label for="cvv">CVV:</label>
                        <input type="password" id="cvv" name="cvv" inputmode="numeric" autocomplete="cc-csc" required maxlength="3" placeholder="123">
                    </div>
                    <div class="campo">
                        <label for="expiracion">Fecha de expiracion:</label>
                        <input type="month" name="expiracion" id="expiracion" autocomplete="cc-exp" required>
                    </div>
                    <div class="campo">
                        <label for="codigo">Código Promocional:</label>
                        <input type="text" id="codigo">
                    </div>
                </fieldset>

                <div id="pagar">
                    <a class="btn btn-pagar btn-amarillo" id="mostrarPagar">Pagar</a>
                    <div class="ventana_pagar" id="ventanaPagar">
                        <div class="pagar-contenido">
                            <h2>¿Desea pagar su carrito?</h2>
                            <div class="btns_pagar">
                                <a class="btn btn-cancelar btn-gris" id="cerrarPagar">Cancelar pago</a>
                                <input class="btn btn-naranja btn-pagar-min" type="submit" value="Pagar" <?php echo empty($productosCarrito) ? 'disabled' : ''; ?>>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

<?php
    incluirTemplate('footer');
?>
