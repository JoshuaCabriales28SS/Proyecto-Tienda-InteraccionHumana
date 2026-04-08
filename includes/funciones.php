<?php

define('TEMPLATES_URL', __DIR__ . '/templates');
define('FUNCIONES_URL', __DIR__ . '/funciones.php');

use Model\Categoria;

function debuguear($variable){
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

function obtenerImagenProducto(?string $imagen): string{
    $imagen = trim((string) $imagen);
    if($imagen !== ''){
        $ruta = __DIR__ . '/../images/' . $imagen;
        if(file_exists($ruta)){
            return '/images/' . $imagen;
        }
    }
    return '/public/build/img/producto_prueba.png';
}

function obtenerMetricasProducto(int $productoId): array{
    $rating = 3.6 + (($productoId % 14) / 10);
    if($rating > 4.9){
        $rating = 4.9;
    }
    $rating = round($rating, 1);
    $reviews = 25 + (($productoId * 17) % 400);

    return [
        'rating' => $rating,
        'reviews' => $reviews
    ];
}

function renderStars(float $rating): string{
    $estrellas = '';
    $llenas = (int) round($rating);
    if($llenas < 1){
        $llenas = 1;
    }
    if($llenas > 5){
        $llenas = 5;
    }

    $icono = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l2.9 6.2 6.8.6-5.1 4.4 1.6 6.6L12 16l-6.2 3.8 1.6-6.6L2 8.8l6.8-.6L12 2z" fill="currentColor"/></svg>';
    for($i = 0; $i < 5; $i++){
        $clase = $i < $llenas ? 'star is-filled' : 'star';
        $estrellas .= '<span class="' . $clase . '">' . $icono . '</span>';
    }

    return $estrellas;
}

function estaAutenticado(): void{
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    if(empty($_SESSION['login'])){
        header('Location: /index.php');
        exit;
    }
}

function redirect(string $url): void{
    header("Location: $url");
    exit;
}

function sanitizeReturnTo(string $returnTo): string{
    $returnTo = trim($returnTo);
    if($returnTo === '' || substr($returnTo, 0, 1) !== '/' || substr($returnTo, 0, 2) === '//'){
        return '/carrito.php';
    }
    return $returnTo;
}

function getCartItems(mysqli $db): array{
    $query = "SELECT carrito.id, carrito.cantidad, productos.id AS producto_id, productos.nombre, productos.precio, productos.imagen
              FROM carrito
              JOIN productos ON carrito.productos_id = productos.id";
    $resultado = mysqli_query($db, $query);
    return $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
}

function summarizeCart(array $carrito, ?array $seleccion = null): array{
    $totalCarrito = 0;
    $totalCantidad = 0;
    $totalSeleccion = 0;
    $cantidadSeleccion = 0;
    $seleccionActiva = is_array($seleccion);

    foreach($carrito as $producto){
        $totalCarrito += (float) $producto['precio'] * (int) $producto['cantidad'];
        $totalCantidad += (int) $producto['cantidad'];

        $seleccionado = !$seleccionActiva || in_array((int) $producto['producto_id'], $seleccion, true);
        if($seleccionado){
            $totalSeleccion += (float) $producto['precio'] * (int) $producto['cantidad'];
            $cantidadSeleccion += (int) $producto['cantidad'];
        }
    }

    if($seleccionActiva && empty($seleccion)){
        $seleccionActiva = false;
    }

    return [
        'carrito' => $carrito,
        'puedePagar' => $seleccionActiva ? $cantidadSeleccion > 0 : !empty($carrito),
        'totalCantidad' => $totalCantidad,
        'totalSeleccion' => $totalSeleccion,
        'cantidadSeleccion' => $cantidadSeleccion,
        'seleccionActiva' => $seleccionActiva,
        'seleccion' => $seleccionActiva ? $seleccion : []
    ];
}

function getHeaderData(mysqli $db): array{
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    $auth = $_SESSION['login'] ?? false;
    $carrito = getCartItems($db);
    $seleccion = array_key_exists('carrito_seleccion', $_SESSION) && is_array($_SESSION['carrito_seleccion']) ? array_map('intval', $_SESSION['carrito_seleccion']) : null;
    $carritoResumen = summarizeCart($carrito, $seleccion);

    $uriActual = $_SERVER['REQUEST_URI'] ?? '/';
    $separador = strpos($uriActual, '?') !== false ? '&' : '?';
    $returnTo = $uriActual . $separador . 'carrito=1';

    $categorias = Categoria::all();
    $categoriaSeleccionada = filter_var($_GET['categoria'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
    $busquedaActual = trim($_GET['q'] ?? '');

    return array_merge($carritoResumen, [
        'auth' => $auth,
        'categorias' => $categorias,
        'categoriaSeleccionada' => $categoriaSeleccionada,
        'busquedaActual' => $busquedaActual,
        'returnTo' => $returnTo
    ]);
}

function escape(string $value): string{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
