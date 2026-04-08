<?php
    //si no se ha iniciado sesion, se inicia desde el header
    if(!isset($_SESSION)){
        session_start();
    }

    //si no existe o el usuario no se autentico, placeholder de null
    $auth = $_SESSION['login'] ?? null;

    $db = conectarDB();
    $query = "SELECT carrito.id, carrito.cantidad, productos.id AS producto_id, productos.nombre,
                     productos.precio, productos.imagen
              FROM carrito
              JOIN productos ON carrito.productos_id = productos.id";
    $resultado = mysqli_query($db, $query);

    $carrito = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
    $totalCarrito = 0;
    $totalCantidad = 0;
    $totalSeleccion = 0;
    $cantidadSeleccion = 0;
    $cartBump = filter_var($_GET['agregado'] ?? null, FILTER_VALIDATE_BOOLEAN);
    $seleccionActiva = array_key_exists('carrito_seleccion', $_SESSION) && is_array($_SESSION['carrito_seleccion']);
    $seleccion = $seleccionActiva ? array_map('intval', $_SESSION['carrito_seleccion']) : [];
    $carritoDetalle = [];

    foreach($carrito as $producto){
        $totalCarrito += (float) $producto['precio'] * (int) $producto['cantidad'];
        $totalCantidad += (int) $producto['cantidad'];

        $seleccionado = !$seleccionActiva || in_array((int) $producto['producto_id'], $seleccion, true);
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

    $carrito = $carritoDetalle;
    $puedePagar = $seleccionActiva ? $cantidadSeleccion > 0 : !empty($carrito);
    $uriActual = $_SERVER['REQUEST_URI'] ?? '/';
    $separador = strpos($uriActual, '?') !== false ? '&' : '?';
    $returnTo = $uriActual . $separador . 'carrito=1';

    $queryCategorias = "SELECT id, nombre FROM categorias ORDER BY nombre";
    $resultadoCategorias = mysqli_query($db, $queryCategorias);
    $categorias = $resultadoCategorias ? mysqli_fetch_all($resultadoCategorias, MYSQLI_ASSOC) : [];
    $categoriaSeleccionada = filter_var($_GET['categoria'] ?? null, FILTER_VALIDATE_INT);
    
    if(!$categoriaSeleccionada){
        $categoriaSeleccionada = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    }
    $busquedaActual = trim($_GET['q'] ?? '');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- STYLE -->
    <link rel="stylesheet" href="/public/build/css/app.css">

    <!-- TITLE -->
    <title>Shop-Corner</title>
    <link rel="icon" href="/public/build/img/icono.png" type="image/png">
</head>
<body>
    <a class="skip-link" href="#main-content">Saltar al contenido</a>
    <header class="header">
        <div class="contenedor">
            <div class="header-contenido">
                <a href="/index.php">
                    <img src="/public/build/img/logo.png" alt="Logo Shop-Corner" type="image/png" class="logo">
                </a>

                <form class="search" action="/index.php" method="GET" role="search">
                    <label class="sr-only" for="busqueda">Buscar productos</label>
                    <div class="search-group">
                        <select name="categoria" class="search-select" aria-label="Categoria">
                            <option value="">Todas</option>
                            <?php foreach($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>" <?php echo $categoriaSeleccionada === (int) $categoria['id'] ? 'selected' : ''; ?>>
                                    <?php echo $categoria['nombre']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input
                            type="search"
                            id="busqueda"
                            name="q"
                            class="search-input"
                            placeholder="Buscar productos, marcas y mas"
                            value="<?php echo htmlspecialchars($busquedaActual, ENT_QUOTES); ?>"
                        >
                        <button type="submit" class="search-btn" aria-label="Buscar">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M11 3a8 8 0 1 0 5.293 14.293l3.707 3.707 1.414-1.414-3.707-3.707A8 8 0 0 0 11 3zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12z" fill="currentColor" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="botones">
                    <button onclick="mostrarCarrito()" class="add-to-cart" aria-label="Carrito (<?php echo $totalCantidad; ?>)" aria-controls="carrito" data-cart-count="<?php echo $totalCantidad; ?>" data-cart-bump="<?php echo $cartBump ? 'true' : 'false'; ?>">
                        <span>Carrito</span>
                        <span class="carrito-badge <?php echo $totalCantidad ? '' : 'is-hidden'; ?>" aria-live="polite"><?php echo $totalCantidad; ?></span>
                        <svg class="morph" viewBox="0 0 64 13">
                            <path d="M0 12C6 12 17 12 32 12C47.9024 12 58 12 64 12V13H0V12Z" />
                        </svg>
                        <div class="cart">
                            <svg viewBox="0 0 36 26">
                                <path d="M1 2.5H6L10 18.5H25.5L28.5 7.5L7.5 7.5" class="shape" />
                                <path
                                    d="M11.5 25C12.6046 25 13.5 24.1046 13.5 23C13.5 21.8954 12.6046 21 11.5 21C10.3954 21 9.5 21.8954 9.5 23C9.5 24.1046 10.3954 25 11.5 25Z"
                                    class="wheel" />
                                <path
                                    d="M24 25C25.1046 25 26 24.1046 26 23C26 21.8954 25.1046 21 24 21C22.8954 21 22 21.8954 22 23C22 24.1046 22.8954 25 24 25Z"
                                    class="wheel" />
                                <path d="M14.5 13.5L16.5 15.5L21.5 10.5" class="tick" />
                            </svg>
                        </div>
                    </button>

                    <div id="overlay" onclick="cerrarCarrito()" aria-hidden="true"></div>
                    <div id="carrito" role="dialog" aria-modal="true" aria-labelledby="carrito-titulo" aria-hidden="true">
                        <button type="button" class="cerrar" onclick="cerrarCarrito()" aria-label="Cerrar carrito">X</button>
                        <h3 id="carrito-titulo">Tu carrito</h3>
                        <ul id="lista-carrito">
                            <!-- AGREGAR PRODUCTOS AL CARRITO -->
                            <?php if(empty($carrito)): ?>
                                <li class="carrito-vacio">Tu carrito esta vacio.</li>
                            <?php else: ?>
                                <?php foreach($carrito as $producto): ?>
                                    <li class="carrito-item">
                                        <form class="carrito-item-form" method="POST" action="/carrito.php">
                                            <input type="hidden" name="producto_id" value="<?php echo $producto['producto_id']; ?>">
                                            <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($returnTo, ENT_QUOTES); ?>">
                                            <img class="carrito-img" src="<?php echo obtenerImagenProducto($producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>" loading="lazy">
                                            <div class="carrito-info">
                                                <p class="carrito-nombre"><?php echo $producto['nombre']; ?></p>
                                                <p class="carrito-precio">$<?php echo number_format($producto['precio'], 2, ".", ","); ?></p>
                                                <div class="carrito-controles">
                                                    <span class="carrito-cantidad"><?php echo $producto['cantidad']; ?></span>
                                                </div>
                                            </div>
                                            <p class="carrito-subtotal">$<?php echo number_format($producto['precio'] * $producto['cantidad'], 2, ".", ","); ?></p>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        <?php if(!empty($carrito)): ?>
                            <div class="carrito-resumen">
                                <div class="carrito-resumen-linea">
                                    <span>Productos</span>
                                    <span><?php echo $totalCantidad; ?></span>
                                </div>
                                <div class="carrito-resumen-linea">
                                    <span>Seleccionados</span>
                                    <span><?php echo $cantidadSeleccion; ?></span>
                                </div>
                                <div class="carrito-resumen-linea">
                                    <span>Total seleccionado</span>
                                    <span>$<?php echo number_format($seleccionActiva ? $totalSeleccion : $totalCarrito, 2, ".", ","); ?></span>
                                </div>
                                <?php if($seleccionActiva): ?>
                                    <div class="carrito-resumen-linea secundaria">
                                        <span>Total general</span>
                                        <span>$<?php echo number_format($totalCarrito, 2, ".", ","); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="carrito-acciones">
                                <a class="btn btn-ghost btn-carrito" href="/carrito.php">Editar carrito</a>
                                <a class="pagar btn btn-verde <?php echo $puedePagar ? '' : 'is-disabled'; ?>" href="/pagar.php" <?php echo $puedePagar ? '' : 'aria-disabled="true" tabindex="-1"'; ?>>Pagar</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if(!$auth): ?>
                        <a href="/login.php" aria-label="User Login Button" tabindex="0" role="button" class="user-profile">
                            <div class="user-profile-inner">
                                <svg
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    >
                                    <g data-name="Layer 2" id="Layer_2">
                                        <path
                                        d="m15.626 11.769a6 6 0 1 0 -7.252 0 9.008 9.008 0 0 0 -5.374 8.231 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 9.008 9.008 0 0 0 -5.374-8.231zm-7.626-4.769a4 4 0 1 1 4 4 4 4 0 0 1 -4-4zm10 14h-12a1 1 0 0 1 -1-1 7 7 0 0 1 14 0 1 1 0 0 1 -1 1z"
                                        ></path>
                                    </g>
                                </svg>
                                <p>Iniciar Sesión</p>
                            </div>
                        </a>
                    <?php else: ?>
                        <a href="/cerrar-sesion.php" aria-label="User Login Button" tabindex="0" role="button" class="user-profile">
                            <div class="user-profile-inner">
                                <svg
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    >
                                    <g data-name="Layer 2" id="Layer_2">
                                        <path
                                        d="m15.626 11.769a6 6 0 1 0 -7.252 0 9.008 9.008 0 0 0 -5.374 8.231 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 9.008 9.008 0 0 0 -5.374-8.231zm-7.626-4.769a4 4 0 1 1 4 4 4 4 0 0 1 -4-4zm10 14h-12a1 1 0 0 1 -1-1 7 7 0 0 1 14 0 1 1 0 0 1 -1 1z"
                                        ></path>
                                    </g>
                                </svg>
                                <p>Cerrar Sesión</p>
                            </div>
                        </a>
                        <a class="btn btn-azulOsc btn_login" href="/admin/index.php">Administrar</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </header>
    <div class="subnav">
        <div class="contenedor">
            <nav class="subnav-links" aria-label="Categorias">
                <?php foreach($categorias as $categoria): ?>
                    <a href="/categoria.php?id=<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre']; ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </div>
