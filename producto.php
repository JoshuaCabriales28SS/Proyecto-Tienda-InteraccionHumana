<?php
    require 'includes/app.php';

    $db = conectarDB();

    //obtener id de la variable que se paso
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    
    //si no hay id, se redirecciona
    if(!$id){
        header('Location: /index.php');
        exit;
    }

    $query = "SELECT * FROM productos WHERE id=$id";
    $resultado = mysqli_query($db, $query);
    $producto = $resultado ? mysqli_fetch_assoc($resultado) : null;

    if(!$producto){
        header('Location: /index.php');
        exit;
    }
    $sinStock = (int) $producto['stock'] < 1;
    $metricas = obtenerMetricasProducto((int) $producto['id']);
    $categoriaProducto = null;
    $categoriaId = (int) $producto['categorias_id'];
    if($categoriaId){
        $queryCat = "SELECT nombre FROM categorias WHERE id=$categoriaId LIMIT 1";
        $resultadoCat = mysqli_query($db, $queryCat);
        $categoriaProducto = $resultadoCat ? mysqli_fetch_assoc($resultadoCat) : null;
    }

    $errores = [];
    $agregado = filter_var($_GET['agregado'] ?? null, FILTER_VALIDATE_BOOLEAN);

    $nombre = '';
    $precio = '';
    $cantidad = '';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $producto_id = (int) $producto['id'];
        $cantidad = filter_var($_POST['cantidad'] ?? null, FILTER_VALIDATE_INT);

        if(!$cantidad || $cantidad < 1){
            $errores[] = "Debes elegir una cantidad (minimo 1)";
        }elseif($cantidad > (int) $producto['stock']){
            $errores[] = "La cantidad supera el stock disponible";
        }

        if(empty($errores)){
            $queryExistente = "SELECT id, cantidad FROM carrito WHERE productos_id = $producto_id LIMIT 1";
            $resultadoExistente = mysqli_query($db, $queryExistente);
            $existente = $resultadoExistente ? mysqli_fetch_assoc($resultadoExistente) : null;

            if($existente){
                $nuevaCantidad = (int) $existente['cantidad'] + $cantidad;
                if($nuevaCantidad > (int) $producto['stock']){
                    $errores[] = "No hay suficiente stock para esa cantidad";
                }else{
                    $queryActualizar = "UPDATE carrito SET cantidad = $nuevaCantidad WHERE id = {$existente['id']}";
                    $resultadoProd = mysqli_query($db, $queryActualizar);
                }
            }else{
                $queryProd = "INSERT INTO carrito (productos_id, cantidad) VALUES ('$producto_id', '$cantidad')";
                $resultadoProd = mysqli_query($db, $queryProd);
            }

            if(empty($errores) && !empty($resultadoProd)){
                if(session_status() === PHP_SESSION_NONE){
                    session_start();
                }
                if(isset($_SESSION['carrito_seleccion']) && is_array($_SESSION['carrito_seleccion'])){
                    $seleccion = array_map('intval', $_SESSION['carrito_seleccion']);
                    if(!in_array($producto_id, $seleccion, true)){
                        $seleccion[] = $producto_id;
                        $_SESSION['carrito_seleccion'] = $seleccion;
                    }
                }

                header('Location: /producto.php?id=' . $producto_id . '&agregado=1');
                exit;
            }
        }
    }

    incluirTemplate('header');
?>

    <main class="contenedor fondo-gris" id="main-content">
        <a class="btn btn-naranja btn-volver" href="/categoria.php?id=<?php echo $producto['categorias_id']; ?>">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#000000"
                stroke-width="1"
                stroke-linecap="round"
                stroke-linejoin="round"
                >
                    <path d="M9 14l-4 -4l4 -4" />
                    <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
            </svg>
        </a>
        <div class="contenedor">
            <nav class="breadcrumb" aria-label="Ruta">
                <a href="/index.php">Inicio</a>
                <span>/</span>
                <?php if($categoriaProducto): ?>
                    <a href="/categoria.php?id=<?php echo $categoriaId; ?>"><?php echo $categoriaProducto['nombre']; ?></a>
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
                <div class="alerta error" role="alert">
                    Producto sin stock.
                </div>
            <?php endif; ?>
            <?php if($agregado): ?>
                <div class="alerta exito" role="status">
                    Producto agregado al carrito.
                </div>
            <?php endif; ?>

            <div class="producto">
                <div class="producto-media">
                    <img src="<?php echo obtenerImagenProducto($producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>" loading="lazy">
                </div>
                <div class="producto-info">
                    <p class="producto-nombre"><?php echo $producto['nombre']; ?></p>
                    <div class="product-rating">
                        <div class="product-stars" aria-hidden="true">
                            <?php echo renderStars($metricas['rating']); ?>
                        </div>
                        <span class="rating-text"><?php echo $metricas['rating']; ?> (<?php echo $metricas['reviews']; ?> opiniones)</span>
                    </div>
                    <p class="producto-precio">$<?php echo number_format($producto['precio'], 2, ".", ","); ?></p>
                    <p class="producto-stock">Stock disponible: <?php echo $producto['stock']; ?></p>
                    <div class="producto-beneficios">
                        <p>Envio rapido y seguimiento en tiempo real.</p>
                        <p>Devolucion gratis dentro de 30 dias.</p>
                    </div>
                    <h4>Descripcion:</h4>
                    <p class="producto-descripcion">
                        <?php echo $producto['descripcion']; ?>
                    </p>
                    <form action="/producto.php?id=<?php echo $producto['id']; ?>" class="producto-form" method="POST">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad" min="1" max="<?php echo $producto['stock']; ?>" value="1" required <?php echo $sinStock ? 'disabled' : ''; ?>>

                        <button class="btn btn-comprar" type="submit" <?php echo $sinStock ? 'disabled' : ''; ?>>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="32"
                                height="32"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#000000"
                                stroke-width="1"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                >
                                <path d="M4 19a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M12.5 17h-6.5v-14h-2" />
                                <path d="M6 5l14 1l-.86 6.017m-2.64 .983h-10.5" />
                                <path d="M16 19h6" />
                                <path d="M19 16v6" />
                            </svg>
                            <h3>Comprar</h3>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php
    incluirTemplate('footer');
?>
