<?php
    require 'includes/app.php';

    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    
    if(!$id){
        header('Location: /index.php');
        exit;
    }

    $db = conectarDB();
    
    $queryCat = "SELECT * from categorias where id=$id";
    $resultadoCat = mysqli_query($db, $queryCat);
    $categoria = $resultadoCat ? mysqli_fetch_assoc($resultadoCat) : null;

    if(!$categoria){
        header('Location: /index.php');
        exit;
    }

    $orden = $_GET['orden'] ?? 'relevancia';
    $ordenesValidos = ['relevancia', 'precio-asc', 'precio-desc', 'nombre'];
    if(!in_array($orden, $ordenesValidos, true)){
        $orden = 'relevancia';
    }

    $minPrecio = filter_var($_GET['min'] ?? null, FILTER_VALIDATE_FLOAT);
    $maxPrecio = filter_var($_GET['max'] ?? null, FILTER_VALIDATE_FLOAT);

    if($minPrecio !== null && $minPrecio !== false && $maxPrecio !== null && $maxPrecio !== false && $minPrecio > $maxPrecio){
        $tmp = $minPrecio;
        $minPrecio = $maxPrecio;
        $maxPrecio = $tmp;
    }

    $condiciones = ["categorias_id=$id"];
    if($minPrecio !== null && $minPrecio !== false){
        $condiciones[] = "precio >= $minPrecio";
    }
    if($maxPrecio !== null && $maxPrecio !== false){
        $condiciones[] = "precio <= $maxPrecio";
    }

    $orderSql = "ORDER BY id DESC";
    if($orden === 'precio-asc'){
        $orderSql = "ORDER BY precio ASC";
    }elseif($orden === 'precio-desc'){
        $orderSql = "ORDER BY precio DESC";
    }elseif($orden === 'nombre'){
        $orderSql = "ORDER BY nombre ASC";
    }

    $whereSql = "WHERE " . implode(' AND ', $condiciones);
    $query = "SELECT * from productos $whereSql $orderSql";

    $resultado = mysqli_query($db, $query);
    $productos = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];

    incluirTemplate('header');
?>


<main class="categoria-main" id="main-content">
        <a class="btn btn-naranja btn-volver btn-izq" href="/index.php">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
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
            <div class="categoria-encabezado">
                <h1><?php echo $categoria['nombre']; ?></h1>
                <p class="categoria-subtexto">Explora productos recomendados de esta categoria.</p>
            </div>

            <div class="categoria-layout">
                <aside class="filtros">
                    <h2>Filtrar</h2>
                    <form method="GET" action="/categoria.php" class="filtros-form">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="campo">
                            <label for="orden">Ordenar por</label>
                            <select name="orden" id="orden">
                                <option value="relevancia" <?php echo $orden === 'relevancia' ? 'selected' : ''; ?>>Relevancia</option>
                                <option value="precio-asc" <?php echo $orden === 'precio-asc' ? 'selected' : ''; ?>>Precio: menor a mayor</option>
                                <option value="precio-desc" <?php echo $orden === 'precio-desc' ? 'selected' : ''; ?>>Precio: mayor a menor</option>
                                <option value="nombre" <?php echo $orden === 'nombre' ? 'selected' : ''; ?>>Nombre</option>
                            </select>
                        </div>
                        <div class="campo">
                            <label for="min">Precio minimo</label>
                            <input type="number" name="min" id="min" min="0" step="0.01" value="<?php echo $minPrecio !== false && $minPrecio !== null ? $minPrecio : ''; ?>">
                        </div>
                        <div class="campo">
                            <label for="max">Precio maximo</label>
                            <input type="number" name="max" id="max" min="0" step="0.01" value="<?php echo $maxPrecio !== false && $maxPrecio !== null ? $maxPrecio : ''; ?>">
                        </div>
                        <button class="btn btn-azul btn-filtro" type="submit">Aplicar filtros</button>
                        <a class="btn btn-ghost btn-filtro" href="/categoria.php?id=<?php echo $id; ?>">Limpiar</a>
                    </form>
                </aside>

                <section class="categoria-productos">
                    <?php if(empty($productos)): ?>
                        <div class="empty-state">
                            <p>No hay productos con estos filtros.</p>
                            <a class="btn btn-azul" href="/categoria.php?id=<?php echo $id; ?>">Ver todo</a>
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
                                            <p class="product-price">$<?php echo number_format($producto['precio'], 2,  '.', ','); ?></p>
                                            <p class="product-meta">Disponible con envio rapido</p>
                                        </div>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>

<?php
    incluirTemplate('footer');
?>
